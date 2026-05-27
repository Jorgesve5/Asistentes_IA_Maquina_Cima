<?php

namespace App\Livewire;

use App\Models\Machine;
use App\Models\Alert;
use App\Models\Manual;
use App\Models\SupervisorMessage;
use App\Models\MachineError;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Smalot\PdfParser\Parser;

class MachineDetail extends Component
{
    use WithFileUploads;

    public $machineId;
    public $contextKey;
    public $showWarnBanner = true;

    // Document search and explorer state
    public $docSearch = '';
    public $docCategory = '';
    public $showDocExplorerModal = false;
    public $showErrorsModal = false;
    public $showTrainingModal = false;
    public $showFaqModal = false;
    public $showIncidencesModal = false;

    // Save conversation state
    public $showSaveModal = false;
    public $saveTitle = '';
    public $saveDescription = '';
    public $showViewConversationModal = false;
    public $viewingConversationMessages = [];
    public $viewingConversationTitle = '';

    // Viewer modal state
    public $viewingManualId = null;
    public $showViewerModal = false;
    
    // Chatbot state
    public $chatMessages = [];
    public $userInput = '';
    public $isThinking = false;
    public $imageAttachment;
    
    // Supervisor chat state
    public $supervisorInput = '';
    public $supervisorMessages = [];
    
    // Operator incidence states
    public $incidenceStatus = 'warning';
    public $incidenceReason = '';
    public $alertDismissed = false;

    public function mount($id)
    {
        $this->machineId = $id;

        // Build a user-namespaced context key so each user has their own chat history
        $userId = auth()->id() ?? 'guest_' . session()->getId();
        $this->contextKey = "machine_chat_context_{$userId}_{$id}";
        
        // Always start with clean on-screen messages
        $machine = Machine::find($id);
        $welcomeMessage = [
            'id' => 'welcome',
            'sender' => 'bot',
            'text' => "Hola, soy el asistente virtual de la **{$machine->name}**. ¿En qué puedo ayudarte hoy con esta unidad?",
            'timestamp' => now()->format('H:i')
        ];
        $this->chatMessages[] = $welcomeMessage;

        // Check if admin triggered a global IA reset for this machine
        $this->checkGlobalIAReset($machine);

        // Initialize background chat context in session if it doesn't exist
        if (!session()->has($this->contextKey)) {
            session([$this->contextKey => $this->chatMessages]);
        }

        $this->loadSupervisorMessages();
    }

    public function sendChatbotMessage()
    {
        $this->validate([
            'userInput' => 'required_without:imageAttachment|nullable|string|max:1000',
            'imageAttachment' => 'nullable|image|max:102400' // max 100MB
        ]);

        $query = trim($this->userInput);
        $this->userInput = '';

        $imageUrl = null;
        $imagePath = null;
        if ($this->imageAttachment) {
            $imagePath = $this->imageAttachment->store('chatbot_attachments', 'public');
            $imageUrl = asset('storage/' . $imagePath);
            $this->imageAttachment = null;
        }

        // Add user message
        $userMsgId = 'msg-' . now()->timestamp . '-' . uniqid();
        $userMsg = [
            'id' => $userMsgId,
            'sender' => 'user',
            'text' => $query,
            'image_url' => $imageUrl,
            'image_path' => $imagePath,
            'timestamp' => now()->format('H:i')
        ];
        $this->chatMessages[] = $userMsg;

        $this->appendToSessionContext($userMsg);

        $this->isThinking = true;
    }

    // This runs after the render cycle to call the AI API asynchronously using Livewire's hooks
    public function getBotResponse()
    {
        if (!$this->isThinking) return;

        $lastMsg = end($this->chatMessages);
        $query = $lastMsg['text'] ?? '';
        $imagePath = $lastMsg['image_path'] ?? null;
        $machine = Machine::with('manuals')->find($this->machineId);
        
        // Gather manual texts as context (Smart RAG search)
        $context = "";
        if (!empty($query) && $machine && $machine->manuals->isNotEmpty()) {
            $q = mb_strtolower($query);
            // Detect if conversational, simple math, or short query
            $isConversational = preg_match('/^(hola|buenos dias|buenas tardes|saludos|cuenta|dime los numeros|como te llamas|quien eres|22\+22|\d+\s*[\+\-\*\/]\s*\d+)/i', $q) || strlen($q) < 8;
            
            if (!$isConversational) {
                // Technical keyword extraction
                $words = array_filter(explode(' ', preg_replace('/[^\p{L}\p{N}\s]/u', '', $q)), function($w) {
                    return strlen($w) > 4;
                });
                
                $snippets = [];
                $chatManuals = $machine->manuals->where('in_chat', true);
                foreach ($chatManuals as $manual) {
                    $manualText = $manual->text;
                    foreach ($words as $word) {
                        $pos = mb_strpos(mb_strtolower($manualText), $word);
                        if ($pos !== false) {
                            $start = max(0, $pos - 200);
                            $length = 500;
                            $snippet = mb_substr($manualText, $start, $length);
                            $snippets[] = "📖 [... " . trim($snippet) . " ...]";
                            if (count($snippets) >= 3) break 2; // limit to top 3 matching snippets
                        }
                    }
                }
                
                if (!empty($snippets)) {
                    $context = "DOCUMENTACIÓN RELEVANTE ENCONTRADA EN LOS MANUALES:\n" . implode("\n\n", $snippets) . "\n";
                }
            }
        }

        // Set prompt system context (use custom if defined, otherwise default)
        $systemPrompt = $machine->custom_prompt ?: "Eres el asistente técnico de IA experto para la máquina {$machine->name} (Serial: {$machine->serial}) de Arancalo.\n";
        
        $chatManuals = $machine->manuals->where('in_chat', true);
        $manualNames = $chatManuals->pluck('fileName')->implode(', ');
        
        if ($manualNames) {
            $systemPrompt .= "Tienes asignados y cargados en tu memoria los siguientes manuales de esta máquina: {$manualNames}.\n";
        }

        if (!empty($context)) {
            $systemPrompt .= "\nUsa la siguiente documentación técnica seleccionada (extraída por el buscador semántico) para responder las preguntas del usuario:\n{$context}\n";
            $systemPrompt .= "Si la pregunta es técnica, basa tu respuesta principalmente en esta documentación técnica. Si el texto extraído no contiene la respuesta exacta a lo que pregunta el usuario, indícalo de forma amable (por ejemplo: 'En el manual XYZ no he encontrado los detalles exactos de...') y luego intenta sugerir una solución según tu conocimiento técnico general.\n";
        } else {
            $systemPrompt .= "\nActualmente el buscador automático no ha extraído ningún fragmento relevante de los manuales para esta consulta específica. Puedes responder de forma amigable y útil sobre el funcionamiento teórico general de la máquina, o pedirle al usuario que use otras palabras clave para que el buscador encuentre el texto en el manual.\n";
        }

        $systemPrompt .= "REGLA CRÍTICA: Debes poder responder de forma totalmente normal, natural y amigable a saludos habituales (como 'hola', 'buenos días'), listados generales (por ejemplo: 'dime los números del 1 al 10', 'cuenta hasta 5') y preguntas conversacionales generales (como '¿qué tal estás?'). No digas que falta información técnica para responder saludos o preguntas cotidianas.\n";
        $systemPrompt .= "Responde siempre en español usando formato Markdown claro con viñetas si es necesario.";

        try {
            $groqKey = config('services.groq.key');
            $geminiKey = config('services.gemini.key');

            if ($groqKey) {
                // Map chat messages to OpenAI message format (including history)
                $apiMessages = [];
                $apiMessages[] = [
                    'role' => 'system',
                    'content' => $systemPrompt
                ];

                $hasImageInConversation = false;
                $contextMessages = session($this->contextKey, []);
                $historyCount = count($contextMessages);
                $startIdx = max(0, $historyCount - 3); // Send last 3 messages of history

                for ($i = $startIdx; $i < $historyCount; $i++) {
                    $msg = $contextMessages[$i];
                    if ($msg['id'] === 'welcome') continue;
                    
                    $msgText = $msg['text'] ?? '';
                    $imagePath = $msg['image_path'] ?? null;
                    $fullPath = $imagePath ? storage_path('app/public/' . $imagePath) : null;

                    if ($imagePath && file_exists($fullPath)) {
                        // FORZAR MODO TEXTO: Ignoramos la imagen porque Groq ha desactivado los modelos de visión.
                        $apiMessages[] = [
                            'role' => $msg['sender'] === 'bot' ? 'assistant' : 'user',
                            'content' => $msgText ?: '[El usuario ha enviado una imagen, pero el análisis visual está desactivado temporalmente en la IA]'
                        ];
                    } else {
                        $apiMessages[] = [
                            'role' => $msg['sender'] === 'bot' ? 'assistant' : 'user',
                            'content' => $msgText
                        ];
                    }
                }

                $model = 'llama-3.1-8b-instant'; // Forzamos siempre el modelo de texto

                // Call Groq API (OpenAI-compatible)
                $response = Http::withoutVerifying()->withHeaders([
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $groqKey,
                ])->post("https://api.groq.com/openai/v1/chat/completions", [
                    'model' => $model,
                    'messages' => $apiMessages,
                    'temperature' => 0.4,
                    'max_tokens' => 512
                ]);

                if ($response->successful()) {
                    $resJson = $response->json();
                    $botText = $resJson['choices'][0]['message']['content'] ?? 'No he podido procesar tu solicitud.';
                } else {
                    \Illuminate\Support\Facades\Log::error("Groq API Error: Status " . $response->status() . " - Body: " . $response->body());
                    if ($response->status() === 429) {
                        $botText = "🤖 **[Aviso de Servidor]**:\nHe recibido demasiadas consultas seguidas (Límite de la API alcanzado). Por favor, espera unos 10 segundos y vuelve a intentarlo.";
                    } else {
                        $botText = $this->localSimulatorFallback($query, $machine);
                    }
                }
            } elseif ($geminiKey) {
                // Map history for Gemini
                $geminiContents = [];
                $contextMessages = session($this->contextKey, []);
                $historyCount = count($contextMessages);
                $startIdx = max(0, $historyCount - 3);

                for ($i = $startIdx; $i < $historyCount; $i++) {
                    $msg = $contextMessages[$i];
                    if ($msg['id'] === 'welcome') continue;
                    
                    $msgText = $msg['text'] ?? '';
                    $imagePath = $msg['image_path'] ?? null;
                    $fullPath = $imagePath ? storage_path('app/public/' . $imagePath) : null;

                    $parts = [];
                    if ($imagePath && file_exists($fullPath)) {
                        $mimeType = mime_content_type($fullPath);
                        $base64 = base64_encode(file_get_contents($fullPath));
                        
                        $textVal = $msgText;
                        if ($i === $historyCount - 1 && $msg['sender'] === 'user') {
                            $textVal = "System Instruction:\n{$systemPrompt}\n\nUser Question: " . ($textVal ?: 'Analiza esta imagen.');
                        }
                        
                        if (!empty($textVal)) {
                            $parts[] = ['text' => $textVal];
                        } else {
                            $parts[] = ['text' => 'Analiza esta imagen.'];
                        }
                        
                        $parts[] = [
                            'inlineData' => [
                                'mimeType' => $mimeType,
                                'data' => $base64
                            ]
                        ];
                    } else {
                        $textVal = $msgText;
                        if ($i === $historyCount - 1 && $msg['sender'] === 'user') {
                            $textVal = "System Instruction:\n{$systemPrompt}\n\nUser Question: {$textVal}";
                        }
                        $parts[] = ['text' => $textVal];
                    }

                    $geminiContents[] = [
                        'role' => $msg['sender'] === 'bot' ? 'model' : 'user',
                        'parts' => $parts
                    ];
                }

                // Call Gemini API
                $response = Http::withoutVerifying()->withHeaders([
                    'Content-Type' => 'application/json',
                ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$geminiKey}", [
                    'contents' => $geminiContents,
                    'generationConfig' => [
                        'maxOutputTokens' => 1024,
                        'temperature' => 0.4
                    ]
                ]);

                if ($response->successful()) {
                    $resJson = $response->json();
                    $botText = $resJson['candidates'][0]['content']['parts'][0]['text'] ?? 'No he podido procesar tu solicitud.';
                } else {
                    \Illuminate\Support\Facades\Log::error("Gemini API Error: Status " . $response->status() . " - Body: " . $response->body());
                    $botText = $this->localSimulatorFallback($query, $machine);
                }
            } else {
                $botText = "⚠️ Error: No se ha configurado ninguna clave de API en el archivo .env. Por favor, define GROQ_API_KEY o GEMINI_API_KEY para comunicarte con la IA.";
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Chatbot Exception: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            $botText = $this->localSimulatorFallback($query, $machine);
        }

        // Add bot message
        $botMsg = [
            'id' => 'msg-' . now()->timestamp . '-' . uniqid(),
            'sender' => 'bot',
            'text' => $botText,
            'timestamp' => now()->format('H:i')
        ];
        $this->chatMessages[] = $botMsg;

        $this->appendToSessionContext($botMsg);

        $this->isThinking = false;
    }

    private function localSimulatorFallback($query, $machine)
    {
        $q = mb_strtolower(trim($query));
        
        // Conversational / Greetings
        if (preg_match('/^(hola|buenos dias|buenas tardes|saludos)/i', $q)) {
            return "¡Hola! Soy el simulador local de asistencia para la máquina **{$machine->name}**. ¿Qué consulta deseas realizar?";
        }
        if (preg_match('/(como te llamas|quien eres)/i', $q)) {
            return "Soy el asistente inteligente para la unidad **{$machine->name}**. Estoy listo para ayudarte.";
        }
        if (preg_match('/dime los numeros del (\d+) al (\d+)/i', $q, $matches)) {
            $nums = range($matches[1], $matches[2]);
            return "Aquí tienes los números del {$matches[1]} al {$matches[2]}:\n" . implode(", ", $nums);
        }
        if (preg_match('/(cuenta hasta|escribe del 1 al)/i', $q)) {
            return "Claro, aquí tienes:\n1, 2, 3, 4, 5, 6, 7, 8, 9, 10.";
        }

        // Look in database manuals (local keyword search)
        foreach ($machine->manuals->where('in_chat', true) as $manual) {
            $words = explode(' ', $q);
            foreach ($words as $word) {
                if (strlen($word) > 4 && mb_strpos(mb_strtolower($manual->text), $word) !== false) {
                    // Extract sentence around the keyword
                    $pos = mb_strpos(mb_strtolower($manual->text), $word);
                    $start = max(0, $pos - 100);
                    $snippet = mb_substr($manual->text, $start, 300);
                    // Clean up strange characters like dotted lines from the PDF
                    $snippet = preg_replace('/-{3,}/', ' ', $snippet);
                    $snippet = preg_replace('/\s+/', ' ', $snippet);
                    return "📖 **[Simulador RAG - Coincidencia en {$manual->fileName}]**:\n\n... " . trim($snippet) . " ...";
                }
            }
        }

        return "🤖 **[Modo Simulación Arancalo]**:\nActualmente no tengo conexión activa con la API de IA (Groq/Gemini). He buscado tu consulta en los manuales de la máquina **{$machine->name}** pero no he encontrado coincidencias directas. Por favor, intenta de nuevo o sube un manual en PDF.";
    }

    // Supervisor Chat functions
    public function sendSupervisorMessage()
    {
        $this->validate([
            'supervisorInput' => 'required|string|max:1000'
        ]);

        $machine = Machine::find($this->machineId);
        
        SupervisorMessage::create([
            'id' => 'msg-' . now()->timestamp . '-' . uniqid(),
            'machine_id' => $this->machineId,
            'machine_name' => $machine->name,
            'text' => trim($this->supervisorInput),
            'from' => 'operator',
            'senderName' => 'Operario Arancalo',
            'timestamp' => now()->format('H:i'),
            'read' => false
        ]);

        $this->supervisorInput = '';
    }

    public function deleteSupervisorMessage($id)
    {
        if (!auth()->check()) {
            return;
        }
        
        $msg = SupervisorMessage::find($id);
        if ($msg) {
            $msg->delete();
        }
    }

    // Operator Incidence Registration
    public function registerIncidence()
    {
        $this->validate([
            'incidenceStatus' => 'required|in:online,maintenance,waiting,warning',
            'incidenceReason' => 'required_if:incidenceStatus,maintenance,waiting,warning|string|max:500'
        ]);

        $machine = Machine::find($this->machineId);
        if (!$machine) return;

        $newStatus = $this->incidenceStatus;
        $reason = trim($this->incidenceReason);

        $statusNames = [
            'online' => 'Disponible',
            'warning' => 'Avería',
            'maintenance' => 'Mantenimiento',
            'waiting' => 'En Espera',
        ];

        $subLabel = '';
        if ($newStatus !== 'online') {
            $subLabel = $newStatus === 'warning' ? "AVERÍA: {$reason}" : 
                        ($newStatus === 'maintenance' ? "MANT: {$reason}" : "ESPERA: {$reason}");
        }

        $machine->update([
            'status' => $newStatus,
            'subLabel' => $subLabel
        ]);

        $alertMessage = "Incidencia: {$machine->name} marcada en " . $statusNames[$newStatus];
        if (!empty($reason)) {
            $alertMessage .= ". Motivo: {$reason}";
        }

        // Create alert notifications
        Alert::create([
            'id' => 'alert-' . now()->timestamp . '-' . uniqid(),
            'machine_id' => $machine->id,
            'machine_name' => $machine->name,
            'message' => $alertMessage,
            'type' => $newStatus === 'online' ? 'info' : $newStatus,
            'timestamp' => now()->format('d/m H:i'),
            'read' => false
        ]);

        // Auto-post a message in the supervisor log
        if ($newStatus !== 'online') {
            SupervisorMessage::create([
                'id' => 'msg-' . now()->timestamp . '-' . uniqid(),
                'machine_id' => $machine->id,
                'machine_name' => $machine->name,
                'text' => "⚠️ Reportó Incidencia ({$statusNames[$newStatus]}): {$reason}",
                'from' => 'operator',
                'senderName' => 'Operario Arancalo',
                'timestamp' => now()->format('H:i'),
                'read' => false
            ]);
        }

        $this->incidenceReason = '';
        $this->incidenceStatus = 'warning';
        session()->flash('incidence_success', "Incidencia registrada y estado actualizado con éxito.");

        // Dispatch event globally so the GlobalToast component catches it immediately
        $this->dispatch('alert-created')->to(GlobalToast::class);
    }

    public function dismissAlert()
    {
        $this->alertDismissed = true;
    }

    public function openDocExplorer()
    {
        $this->showDocExplorerModal = true;
    }

    public function closeDocExplorer()
    {
        $this->showDocExplorerModal = false;
        $this->docSearch = '';
        $this->docCategory = '';
    }

    public function openViewer($id)
    {
        $this->viewingManualId = $id;
        $this->showViewerModal = true;
    }

    public function closeViewer()
    {
        $this->showViewerModal = false;
        $this->viewingManualId = null;
    }

    public function openErrorsModal()
    {
        $this->showErrorsModal = true;
    }

    public function closeErrorsModal()
    {
        $this->showErrorsModal = false;
    }

    public function openTrainingModal()
    {
        $this->showTrainingModal = true;
    }

    public function closeTrainingModal()
    {
        $this->showTrainingModal = false;
    }

    public function openFaqModal()
    {
        $this->showFaqModal = true;
    }

    public function closeFaqModal()
    {
        $this->showFaqModal = false;
    }

    public function openIncidencesModal()
    {
        $this->showIncidencesModal = true;
    }

    public function closeIncidencesModal()
    {
        $this->showIncidencesModal = false;
    }

    public function deleteIncidence($id)
    {
        $alert = \App\Models\Alert::find($id);
        if ($alert) {
            $alert->delete();
        }
    }

    public function deleteMachineError($id)
    {
        $err = \App\Models\MachineError::find($id);
        if ($err) {
            $err->delete();
        }
    }

    public function openSaveModal()
    {
        $this->saveTitle = '';
        $this->saveDescription = '';
        $this->showSaveModal = true;
    }
    
    public function closeSaveModal()
    {
        $this->showSaveModal = false;
    }
    
    public function saveConversation()
    {
        $this->validate([
            'saveTitle' => 'required|string|max:255',
            'saveDescription' => 'nullable|string|max:1000'
        ]);
        
        MachineError::create([
            'machine_id' => $this->machineId,
            'title' => $this->saveTitle,
            'description' => $this->saveDescription,
            'messages' => $this->chatMessages,
            'is_saved' => true,
        ]);
        
        $this->showSaveModal = false;
        session()->flash('save_success', 'Conversación guardada con éxito.');
    }
    
    public function loadConversation($id)
    {
        $conversation = MachineError::where('machine_id', $this->machineId)
            ->where('is_saved', true)
            ->find($id);
        if ($conversation) {
            $msgs = $conversation->messages;
            $this->chatMessages = is_array($msgs) ? $msgs : [];
            session([$this->contextKey => $this->chatMessages]);
            $this->showErrorsModal = false; // Close the modal after loading
            $this->showViewConversationModal = false; // Close view modal if open
        }
    }

    public function viewConversation($id)
    {
        $conversation = MachineError::where('machine_id', $this->machineId)
            ->where('is_saved', true)
            ->find($id);
        if ($conversation) {
            $msgs = $conversation->messages;
            $this->viewingConversationMessages = is_array($msgs) ? $msgs : [];
            $this->viewingConversationTitle = $conversation->title;
            $this->showViewConversationModal = true;
            $this->showErrorsModal = false; // Close the list so it doesn't stay behind
        }
    }

    public function closeViewConversationModal()
    {
        $this->showViewConversationModal = false;
        $this->viewingConversationMessages = [];
        $this->viewingConversationTitle = '';
        $this->showErrorsModal = true; // Reopen the list automatically when closing the viewer
    }

    public function clearChatHistory()
    {
        $machine = Machine::find($this->machineId);
        $this->chatMessages = [];
        $welcomeMsg = [
            'id' => 'welcome',
            'sender' => 'bot',
            'text' => "Hola, soy el asistente virtual de la **{$machine->name}**. ¿En qué puedo ayudarte hoy con esta unidad?",
            'timestamp' => now()->format('H:i')
        ];
        $this->chatMessages[] = $welcomeMsg;
        
        // LIMPIAMOS EL CONTEXTO DE LA SESIÓN PARA EVITAR IMÁGENES ATASCADAS
        session()->forget($this->contextKey);
        session([$this->contextKey => $this->chatMessages]);
    }

    private function appendToSessionContext(array $message)
    {
        $context = session($this->contextKey, []);
        $context[] = $message;

        // Keep the welcome message at index 0, and the last 30 messages to avoid session bloat
        if (count($context) > 31) {
            $welcome = $context[0];
            $lastMessages = array_slice($context, -30);
            $context = array_merge([$welcome], $lastMessages);
        }

        session([$this->contextKey => $context]);
    }

    /**
     * Check if the admin triggered a global IA reset for this machine.
     * Compares the global reset timestamp (in Cache) with the user's session timestamp.
     * If a newer reset exists, wipe the user's chat history.
     */
    private function checkGlobalIAReset($machine)
    {
        $globalResetAt = Cache::get("machine_ia_reset_at_{$this->machineId}");
        $sessionResetAt = session("machine_chat_reset_at_{$this->machineId}");

        if ($globalResetAt && (!$sessionResetAt || Carbon::parse($sessionResetAt)->lt(Carbon::parse($globalResetAt)))) {
            // Admin reset happened after this user's last acknowledgement — wipe context
            session()->forget($this->contextKey);
            $this->chatMessages = [];
            $this->chatMessages[] = [
                'id' => 'welcome',
                'sender' => 'bot',
                'text' => "Hola, soy el asistente virtual de la **{$machine->name}**. ¿En qué puedo ayudarte hoy con esta unidad?",
                'timestamp' => now()->format('H:i')
            ];
            session([$this->contextKey => $this->chatMessages]);
            // Mark the reset as acknowledged for this user's session
            session(["machine_chat_reset_at_{$this->machineId}" => now()->toDateTimeString()]);
        }
    }

    private function loadSupervisorMessages()
    {
        $this->supervisorMessages = SupervisorMessage::where('machine_id', $this->machineId)
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function render()
    {
        $machine = Machine::with('manuals')->find($this->machineId);

        // Only query manuals when the doc explorer modal is open
        $machineManuals = collect();
        if ($this->showDocExplorerModal) {
            $manualsQuery = Manual::where('machine_id', $this->machineId);

            if (!empty($this->docSearch)) {
                $searchTerm = '%' . $this->docSearch . '%';
                $manualsQuery->where(function($q) use ($searchTerm) {
                    $q->where('fileName', 'like', $searchTerm)
                      ->orWhere('text', 'like', $searchTerm);
                });
            }

            if (!empty($this->docCategory)) {
                $manualsQuery->where('category', $this->docCategory);
            }

            $machineManuals = $manualsQuery->latest()->get();
        }

        $categories = [
            'Manual de Operación',
            'Esquema Eléctrico',
            'Guía Rápida',
            'Hoja de Registro',
            'Imágenes',
            'Otro'
        ];

        $viewingManual = $this->viewingManualId ? Manual::find($this->viewingManualId) : null;

        $this->loadSupervisorMessages();

        // Only query errors when the errors modal is open
        $machineErrors = $this->showErrorsModal
            ? \App\Models\MachineError::where('machine_id', $this->machineId)->where('is_saved', true)->latest()->get()
            : collect();

        // Query recent status changes/incidences for the sidebar log (always loaded)
        $recentIncidences = \App\Models\Alert::where('machine_id', $this->machineId)
            ->whereIn('type', ['warning', 'maintenance', 'waiting', 'info'])
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        // Query all status changes for the modal log (only loaded when active)
        $machineIncidences = $this->showIncidencesModal
            ? \App\Models\Alert::where('machine_id', $this->machineId)
                ->whereIn('type', ['warning', 'maintenance', 'waiting', 'info'])
                ->orderBy('created_at', 'desc')
                ->get()
            : collect();

        return view('livewire.machine-detail', [
            'machine' => $machine,
            'machineManuals' => $machineManuals,
            'categories' => $categories,
            'viewingManual' => $viewingManual,
            'supervisorMessages' => $this->supervisorMessages,
            'machineErrors' => $machineErrors,
            'recentIncidences' => $recentIncidences,
            'machineIncidences' => $machineIncidences
        ])->title("Ficha Técnica: {$machine->name}");
    }
}
