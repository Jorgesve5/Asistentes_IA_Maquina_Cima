<?php

namespace App\Livewire;

use App\Models\Machine;
use App\Models\Alert;
use App\Models\SupervisorMessage;
use App\Models\Manual;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Smalot\PdfParser\Parser;

class AdminDashboard extends Component
{
    use WithFileUploads;

    public $activeTab = 'status'; // status, manuals, chatbot, messages
    
    // Status change modal states
    public $selectedMachineId = null;
    public $targetStatus = null;
    public $reason = '';
    public $showModal = false;

    // Manuals PDF upload states
    public $uploadedFiles = [];
    public $uploadingMachineId = null;

    // Chatbot test states
    public $selectedMachineForChat = '';
    public $chatMessages = [];
    public $userInput = '';
    public $isThinking = false;
    public $customPrompt = '';
    public $imageAttachment;

    public function mount()
    {
        if (!Auth::check()) {
            return redirect()->to('/admin/login');
        }

        // Initialize chatbot test machine
        $firstMachine = Machine::first();
        if ($firstMachine) {
            $this->selectedMachineForChat = $firstMachine->id;
            $this->customPrompt = $firstMachine->custom_prompt ?? '';
            $this->chatMessages[] = [
                'id' => 'welcome',
                'sender' => 'bot',
                'text' => "¡Hola! Soy el asistente virtual de la **{$firstMachine->name}** (Modo Admin). ¿Qué deseas probar hoy?",
                'timestamp' => now()->format('H:i')
            ];
        }
    }

    public function logout()
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
        return redirect()->to('/');
    }

    // Tab switcher helper
    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }

    // Machine selector changed
    public function updatedSelectedMachineForChat($value)
    {
        $machine = Machine::find($value);
        if ($machine) {
            $this->customPrompt = $machine->custom_prompt ?? '';
            $this->chatMessages = [
                [
                    'id' => 'welcome',
                    'sender' => 'bot',
                    'text' => "¡Hola! Soy el asistente virtual de la **{$machine->name}** (Modo Admin). ¿Qué deseas probar hoy?",
                    'timestamp' => now()->format('H:i')
                ]
            ];
        }
    }

    // Save custom prompt instructions
    public function saveCustomPrompt()
    {
        $machine = Machine::find($this->selectedMachineForChat);
        if ($machine) {
            $machine->update([
                'custom_prompt' => trim($this->customPrompt)
            ]);
            session()->flash('prompt_success', "Instrucciones de la IA actualizadas con éxito.");
        }
    }

    // Chatbot test actions
    public function sendChatbotMessage()
    {
        $this->validate([
            'userInput' => 'required_without:imageAttachment|nullable|string|max:1000',
            'imageAttachment' => 'nullable|image|max:10240' // max 10MB
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
        $this->chatMessages[] = [
            'id' => 'msg-' . now()->timestamp . '-' . uniqid(),
            'sender' => 'user',
            'text' => $query,
            'image_url' => $imageUrl,
            'image_path' => $imagePath,
            'timestamp' => now()->format('H:i')
        ];

        $this->isThinking = true;
    }

    public function getBotResponse()
    {
        if (!$this->isThinking) return;

        $lastMsg = end($this->chatMessages);
        $query = $lastMsg['text'] ?? '';
        $machine = Machine::with('manuals')->find($this->selectedMachineForChat);
        
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
                foreach ($machine->manuals as $manual) {
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
        $systemPrompt = $machine->custom_prompt ?: "Eres el asistente técnico de IA experto para la máquina {$machine->name} (Serial: {$machine->serial}) de CIMA Cableados.\n";
        
        if (!empty($context)) {
            $systemPrompt .= "\nUsa la siguiente documentación técnica seleccionada de la máquina para responder las preguntas del usuario:\n{$context}\n";
            $systemPrompt .= "Si la pregunta es técnica, basa tu respuesta principalmente en esta documentación técnica. Si no encuentras la respuesta en ella, indícalo de forma amable pero intenta responder según tu conocimiento técnico general.\n";
        } else {
            $systemPrompt .= "\nActualmente no hay manuales o no se requiere documentación técnica para esta consulta. Puedes responder de forma amigable y útil sobre el funcionamiento teórico general de la máquina o entablar una conversación informal según sea el caso.\n";
        }

        $systemPrompt .= "\nREGLA CRÍTICA: Debes poder responder de forma totalmente normal, natural y amigable a saludos habituales (como 'hola', 'buenos días'), listados generales (por ejemplo: 'dime los números del 1 al 10', 'cuenta hasta 5') y preguntas conversacionales generales (como '¿qué tal estás?'). No digas que falta información técnica para responder saludos o preguntas cotidianas.\n";
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
                $historyCount = count($this->chatMessages);
                $startIdx = max(0, $historyCount - 9); // Send last 9 messages of history

                for ($i = $startIdx; $i < $historyCount; $i++) {
                    $msg = $this->chatMessages[$i];
                    if ($msg['id'] === 'welcome') continue;
                    
                    $msgText = $msg['text'] ?? '';
                    $imagePath = $msg['image_path'] ?? null;
                    $fullPath = $imagePath ? storage_path('app/public/' . $imagePath) : null;

                    if ($imagePath && file_exists($fullPath)) {
                        $hasImageInConversation = true;
                        $mimeType = mime_content_type($fullPath);
                        $base64 = base64_encode(file_get_contents($fullPath));

                        $content = [];
                        if (!empty($msgText)) {
                            $content[] = [
                                'type' => 'text',
                                'text' => $msgText
                            ];
                        } else {
                            $content[] = [
                                'type' => 'text',
                                'text' => 'Analiza esta imagen.'
                            ];
                        }
                        $content[] = [
                            'type' => 'image_url',
                            'image_url' => [
                                'url' => "data:{$mimeType};base64,{$base64}"
                            ]
                        ];

                        $apiMessages[] = [
                            'role' => $msg['sender'] === 'bot' ? 'assistant' : 'user',
                            'content' => $content
                        ];
                    } else {
                        $apiMessages[] = [
                            'role' => $msg['sender'] === 'bot' ? 'assistant' : 'user',
                            'content' => $msgText
                        ];
                    }
                }

                $model = $hasImageInConversation ? 'meta-llama/llama-4-scout-17b-16e-instruct' : 'llama-3.1-8b-instant';

                // Call Groq API (OpenAI-compatible)
                $response = Http::withoutVerifying()->withHeaders([
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $groqKey,
                ])->post("https://api.groq.com/openai/v1/chat/completions", [
                    'model' => $model,
                    'messages' => $apiMessages,
                    'temperature' => 0.4,
                    'max_tokens' => 1024
                ]);

                if ($response->successful()) {
                    $resJson = $response->json();
                    $botText = $resJson['choices'][0]['message']['content'] ?? 'No he podido procesar tu solicitud.';
                } else {
                    $botText = $this->localSimulatorFallback($query, $machine);
                }
            } elseif ($geminiKey) {
                // Map history for Gemini
                $geminiContents = [];
                $historyCount = count($this->chatMessages);
                $startIdx = max(0, $historyCount - 9);

                for ($i = $startIdx; $i < $historyCount; $i++) {
                    $msg = $this->chatMessages[$i];
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
                ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$geminiKey}", [
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
                    $botText = $this->localSimulatorFallback($query, $machine);
                }
            } else {
                $botText = "⚠️ Error: No se ha configurado ninguna clave de API en el archivo .env. Por favor, define GROQ_API_KEY o GEMINI_API_KEY.";
            }
        } catch (\Exception $e) {
            $botText = $this->localSimulatorFallback($query, $machine);
        }

        $this->chatMessages[] = [
            'id' => 'msg-' . now()->timestamp . '-' . uniqid(),
            'sender' => 'bot',
            'text' => $botText,
            'timestamp' => now()->format('H:i')
        ];

        $this->isThinking = false;
    }

    private function localSimulatorFallback($query, $machine)
    {
        $q = mb_strtolower(trim($query));
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

        foreach ($machine->manuals as $manual) {
            $words = explode(' ', $q);
            foreach ($words as $word) {
                if (strlen($word) > 4 && mb_strpos(mb_strtolower($manual->text), $word) !== false) {
                    $pos = mb_strpos(mb_strtolower($manual->text), $word);
                    $start = max(0, $pos - 100);
                    $snippet = mb_substr($manual->text, $start, 300);
                    return "📖 **[Simulador RAG - Coincidencia en {$manual->fileName}]**:\n\n... " . trim($snippet) . " ...";
                }
            }
        }

        return "🤖 **[Modo Simulación CIMA]**:\nActualmente no tengo conexión activa con la API de IA (Groq/Gemini) o el servidor respondió con un error. Esta es una respuesta simulada local.";
    }

    // Initiate status change
    public function initiateStatusChange($machineId, $status)
    {
        $this->selectedMachineId = $machineId;
        $this->targetStatus = $status;

        if ($status === 'online') {
            $this->reason = '';
            $this->saveStatusChange();
        } else {
            $this->reason = '';
            $this->showModal = true;
        }
    }

    public function saveStatusChange()
    {
        $machine = Machine::find($this->selectedMachineId);
        if (!$machine) return;

        $oldStatus = $machine->status;
        $newStatus = $this->targetStatus;

        $statusNames = [
            'online' => 'Disponible',
            'warning' => 'Avería',
            'maintenance' => 'Mantenimiento',
            'waiting' => 'En Espera',
        ];

        $cleanReason = trim($this->reason);
        $subLabel = '';
        if ($newStatus !== 'online') {
            $subLabel = $newStatus === 'warning' ? "AVERÍA: {$cleanReason}" : 
                        ($newStatus === 'maintenance' ? "MANT: {$cleanReason}" : "ESPERA: {$cleanReason}");
        }

        $machine->update([
            'status' => $newStatus,
            'subLabel' => $subLabel
        ]);

        $alertMessage = "{$machine->name} marcada en " . $statusNames[$newStatus];
        if (!empty($cleanReason)) {
            $alertMessage .= ". Motivo: {$cleanReason}";
        }

        Alert::create([
            'id' => 'alert-' . now()->timestamp . '-' . uniqid(),
            'machine_id' => $machine->id,
            'machine_name' => $machine->name,
            'message' => $alertMessage,
            'type' => $newStatus === 'online' ? 'info' : $newStatus,
            'timestamp' => now()->format('d/m H:i'),
            'read' => false
        ]);

        $this->dispatch('alert-created')->to(GlobalToast::class);

        $this->showModal = false;
        $this->selectedMachineId = null;
        $this->targetStatus = null;
        $this->reason = '';
    }

    // Manual PDF Upload Handler
    public function startUpload($machineId)
    {
        $this->uploadingMachineId = $machineId;
    }

    public function updatedUploadedFiles()
    {
        $this->validate([
            'uploadedFiles.*' => 'required|mimes:pdf|max:25600', // max 25MB
        ]);

        if (empty($this->uploadingMachineId)) {
            return;
        }

        $count = 0;
        foreach ($this->uploadedFiles as $file) {
            $path = $file->getRealPath();
            $fileName = $file->getClientOriginalName();
            $size = round($file->getSize() / 1024 / 1024, 1) . ' MB';

            try {
                $parser = new Parser();
                $pdf = $parser->parseFile($path);
                $text = $pdf->getText();

                $text = filter_var($text, FILTER_DEFAULT);
                $text = preg_replace('/[[:cntrl:]]/', ' ', $text);
                $text = trim(preg_replace('/\s+/', ' ', $text));

                if (empty($text)) {
                    $text = "Manual cargado. Sin texto estructurado extraíble.";
                }

                Manual::create([
                    'machine_id' => $this->uploadingMachineId,
                    'fileName' => $fileName,
                    'size' => $size,
                    'text' => $text
                ]);
                $count++;
            } catch (\Exception $e) {
                session()->flash('upload_error', "Error al parsear el PDF '{$fileName}': " . $e->getMessage());
            }
        }

        if ($count > 0) {
            session()->flash('upload_success', "Se han subido e indexado {$count} manuales técnicos.");
        }

        $this->uploadedFiles = [];
        $this->uploadingMachineId = null;
    }

    public function deleteManual($id)
    {
        $manual = Manual::find($id);
        if ($manual) {
            $manual->delete();
            session()->flash('upload_success', "Manual técnico eliminado con éxito.");
        }
    }

    public function deleteMessage($id)
    {
        $msg = SupervisorMessage::find($id);
        if ($msg) {
            $msg->delete();
        }
    }

    public function render()
    {
        $machines = Machine::with('manuals')->get();
        $messages = SupervisorMessage::orderBy('created_at', 'desc')->get();

        return view('livewire.admin-dashboard', [
            'machines' => $machines,
            'messages' => $messages
        ])->title('Consola de Supervisores - CIMA');
    }
}
