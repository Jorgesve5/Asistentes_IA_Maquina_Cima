<div class="flex-1 flex flex-col max-w-[1400px] mx-auto w-full px-4 sm:px-6 py-6" x-data="{ init() { $nextTick(() => { const cb = document.getElementById('chatbot-box'); if(cb) cb.scrollTop = cb.scrollHeight; }) } }">

    <!-- Header bar -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-white/5 pb-5 mb-6">
        <div>
            <div class="flex items-center gap-2">
                <span class="h-2 w-2 rounded-full bg-cyan-400 animate-pulse"></span>
                <span class="text-xs font-black text-slate-400 tracking-wider uppercase font-mono">Consola Supervisor</span>
            </div>
            <h1 class="text-3xl font-black text-white tracking-tight uppercase mt-1 font-outfit">Control de Planta</h1>
        </div>

        <div class="flex items-center gap-3">
            <!-- Logout -->
            <button
                wire:click="logout"
                onclick="window.playAudio('click');"
                class="px-4 py-2 text-xs font-bold bg-red-500/10 border border-red-500/25 hover:border-red-500/40 text-red-400 rounded-xl transition-all"
            >
                Cerrar Sesión
            </button>
        </div>
    </div>

    <!-- Tabs switchers -->
    <div class="flex flex-wrap gap-1.5 mb-8 bg-white/3 border border-white/5 rounded-2xl p-1.5 w-fit">
        <button
            wire:click="setTab('status')"
            onclick="window.playAudio('click');"
            class="px-5 py-2.5 rounded-xl text-xs font-black uppercase tracking-wider transition-all duration-200 {{ $activeTab === 'status' ? 'bg-cyan-500 text-slate-950 shadow-[0_0_12px_rgba(34,211,238,0.3)]' : 'text-slate-400 hover:text-white' }}"
        >
            Estado de Máquinas
        </button>
        <button
            wire:click="setTab('manuals')"
            onclick="window.playAudio('click');"
            class="px-5 py-2.5 rounded-xl text-xs font-black uppercase tracking-wider transition-all duration-200 {{ $activeTab === 'manuals' ? 'bg-cyan-500 text-slate-950 shadow-[0_0_12px_rgba(34,211,238,0.3)]' : 'text-slate-400 hover:text-white' }}"
        >
            Manuales PDF
        </button>
        <button
            wire:click="setTab('chatbot')"
            onclick="window.playAudio('click');"
            class="px-5 py-2.5 rounded-xl text-xs font-black uppercase tracking-wider transition-all duration-200 {{ $activeTab === 'chatbot' ? 'bg-cyan-500 text-slate-950 shadow-[0_0_12px_rgba(34,211,238,0.3)]' : 'text-slate-400 hover:text-white' }}"
        >
            Prueba / Config IA
        </button>
        <button
            wire:click="setTab('messages')"
            onclick="window.playAudio('click');"
            class="px-5 py-2.5 rounded-xl text-xs font-black uppercase tracking-wider transition-all duration-200 {{ $activeTab === 'messages' ? 'bg-cyan-500 text-slate-950 shadow-[0_0_12px_rgba(34,211,238,0.3)]' : 'text-slate-400 hover:text-white' }}"
        >
            Mensajes
        </button>
    </div>

    <!-- ── TAB: Status (Machine States Cards) ── -->
    @if($activeTab === 'status')
        <div>
            <p class="text-xs text-slate-500 mb-6 tracking-wide uppercase font-mono">
                Cambia el estado de cada máquina. Los operarios verán el color y recibirán alertas en tiempo real en la pantalla de planta.
            </p>
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
                @foreach($machines as $m)
                    @php
                        $badgeClasses = [
                            'online' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                            'maintenance' => 'bg-orange-500/10 text-orange-400 border-orange-500/20',
                            'waiting' => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
                            'warning' => 'bg-red-500/10 text-red-400 border-red-500/20',
                        ];
                        $statusNames = [
                            'online' => 'Operativa',
                            'maintenance' => 'Mantenimiento',
                            'waiting' => 'En Espera',
                            'warning' => 'Avería',
                        ];
                    @endphp
                    <div class="bg-[#0a0e17]/80 border border-white/5 rounded-3xl p-5 flex flex-col justify-between gap-4 shadow-2xl backdrop-blur-md">
                        <div>
                            <span class="text-[9px] font-mono text-slate-600 tracking-wider block mb-0.5">{{ $m->serial ?: 'SIN SERIAL' }}</span>
                            <h3 class="font-black text-base text-white tracking-wide uppercase leading-tight font-outfit">{{ $m->name }}</h3>
                            
                            <div class="mt-2.5 px-3 py-1.5 rounded-xl border text-[11px] font-bold flex items-center gap-2 {{ $badgeClasses[$m->status] }}">
                                <span class="h-1.5 w-1.5 rounded-full bg-current animate-pulse"></span>
                                {{ $statusNames[$m->status] }}
                            </div>
                            
                            @if($m->status !== 'online' && $m->subLabel)
                                <p class="text-[10px] font-mono text-slate-400 mt-2 bg-white/3 border border-white/5 rounded-lg px-2.5 py-1.5 leading-snug">
                                    {{ $m->subLabel }}
                                </p>
                            @endif
                        </div>

                        <div class="flex flex-col gap-1.5 mt-3 pt-3 border-t border-white/5">
                            <button
                                wire:click="initiateStatusChange('{{ $m->id }}', 'online')"
                                onclick="window.playAudio('click');"
                                class="w-full flex items-center justify-between px-3 py-2 rounded-xl border text-[11px] font-bold transition-all duration-200
                                  {{ $m->status === 'online' ? 'bg-emerald-500/20 border-emerald-500/40 text-emerald-300' : 'border-white/5 text-slate-500 hover:border-white/10 hover:text-slate-300' }}"
                            >
                                <span>Disponible</span>
                                @if($m->status === 'online') <span class="h-1 w-1 bg-emerald-400 rounded-full"></span> @endif
                            </button>
                            <button
                                wire:click="initiateStatusChange('{{ $m->id }}', 'maintenance')"
                                onclick="window.playAudio('click');"
                                class="w-full flex items-center justify-between px-3 py-2 rounded-xl border text-[11px] font-bold transition-all duration-200
                                  {{ $m->status === 'maintenance' ? 'bg-orange-500/20 border-orange-500/40 text-orange-300' : 'border-white/5 text-slate-500 hover:border-white/10 hover:text-slate-300' }}"
                            >
                                <span>Mantenimiento</span>
                                @if($m->status === 'maintenance') <span class="h-1 w-1 bg-orange-400 rounded-full"></span> @endif
                            </button>
                            <button
                                wire:click="initiateStatusChange('{{ $m->id }}', 'waiting')"
                                onclick="window.playAudio('click');"
                                class="w-full flex items-center justify-between px-3 py-2 rounded-xl border text-[11px] font-bold transition-all duration-200
                                  {{ $m->status === 'waiting' ? 'bg-amber-500/20 border-amber-500/40 text-amber-300' : 'border-white/5 text-slate-500 hover:border-white/10 hover:text-slate-300' }}"
                            >
                                <span>En Espera</span>
                                @if($m->status === 'waiting') <span class="h-1 w-1 bg-amber-400 rounded-full"></span> @endif
                            </button>
                            <button
                                wire:click="initiateStatusChange('{{ $m->id }}', 'warning')"
                                onclick="window.playAudio('click');"
                                class="w-full flex items-center justify-between px-3 py-2 rounded-xl border text-[11px] font-bold transition-all duration-200
                                  {{ $m->status === 'warning' ? 'bg-red-500/20 border-red-500/40 text-red-300' : 'border-white/5 text-slate-500 hover:border-white/10 hover:text-slate-300' }}"
                            >
                                <span>Avería</span>
                                @if($m->status === 'warning') <span class="h-1 w-1 bg-red-400 rounded-full"></span> @endif
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- ── TAB: Manuals (PDFs Management) ── -->
    @if($activeTab === 'manuals')
        <div>
            <p class="text-xs text-slate-500 mb-6 tracking-wide uppercase font-mono">
                Sube manuales técnicos en PDF por máquina. El motor RAG los indexará para responder consultas de operarios.
            </p>

            @if(session()->has('upload_success'))
                <div class="mb-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs px-4 py-3 rounded-2xl">
                    {{ session('upload_success') }}
                </div>
            @endif
            @if(session()->has('upload_error'))
                <div class="mb-4 bg-red-500/10 border border-red-500/20 text-red-400 text-xs px-4 py-3 rounded-2xl">
                    {{ session('upload_error') }}
                </div>
            @endif

            <div class="grid grid-cols-1 gap-4">
                @foreach($machines as $m)
                    <div class="bg-[#0a0e17]/80 border border-white/5 rounded-3xl p-5 shadow-2xl backdrop-blur-md flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <div class="flex-1 min-w-0">
                            <span class="text-[9px] font-mono text-slate-600 tracking-wider block mb-0.5">{{ $m->serial ?: 'SIN SERIAL' }}</span>
                            <h3 class="font-black text-sm text-white tracking-wide uppercase font-outfit">{{ $m->name }}</h3>
                            
                            <!-- Manuals list -->
                            <div class="mt-3.5 space-y-2">
                                @if($m->manuals->isEmpty())
                                    <span class="text-[10px] font-mono text-slate-600 block">Sin manuales cargados</span>
                                @else
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($m->manuals as $man)
                                            <div class="flex items-center gap-2 bg-white/3 border border-white/5 px-3 py-1.5 rounded-xl text-[10px] font-mono group">
                                                <svg class="h-3.5 w-3.5 text-cyan-400/80" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                                </svg>
                                                <span class="text-slate-300 font-bold max-w-[200px] truncate">{{ $man->fileName }}</span>
                                                <span class="text-slate-500">({{ $man->size }})</span>
                                                <button
                                                    wire:click="deleteManual({{ $man->id }})"
                                                    onclick="window.playAudio('click');"
                                                    class="text-slate-500 hover:text-red-400 transition-colors ml-1"
                                                >
                                                    ✕
                                                </button>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Upload buttons trigger -->
                        <div class="flex items-center gap-3">
                            <label
                                onclick="window.playAudio('click'); @this.set('uploadingMachineId', '{{ $m->id }}')"
                                class="flex items-center gap-2 bg-white/4 hover:bg-cyan-500 border border-white/8 hover:border-cyan-500 text-slate-400 hover:text-slate-950 px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-wider cursor-pointer transition-all duration-200 whitespace-nowrap"
                            >
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                </svg>
                                Subir PDF(s)
                                <input
                                    type="file"
                                    wire:model="uploadedFiles"
                                    multiple
                                    accept=".pdf"
                                    class="hidden"
                                />
                            </label>
                            
                            @if($uploadingMachineId === $m->id)
                                <div wire:loading wire:target="uploadedFiles" class="text-[10px] text-cyan-400 font-mono">
                                    Procesando RAG...
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- ── TAB: Chatbot Config & Test ── -->
    @if($activeTab === 'chatbot')
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            
            <!-- Prompt Config Column (5 cols) -->
            <div class="lg:col-span-5 bg-[#0a0e17]/80 border border-white/5 rounded-3xl p-5 shadow-2xl backdrop-blur-md">
                <h2 class="text-sm font-black text-white tracking-widest uppercase mb-4 font-outfit">Configuración de Prompt IA</h2>
                
                @if(session()->has('prompt_success'))
                    <div class="mb-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs px-3 py-2 rounded-xl">
                        {{ session('prompt_success') }}
                    </div>
                @endif

                <div class="space-y-4">
                    <div>
                        <label class="block text-[9px] font-bold text-slate-500 uppercase tracking-widest mb-1.5">Unidad a configurar</label>
                        <select
                            wire:model.live="selectedMachineForChat"
                            class="w-full bg-white/3 border border-white/10 rounded-xl px-4 py-3 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-cyan-500/50 transition-colors"
                        >
                            @foreach($machines as $m)
                                <option value="{{ $m->id }}" class="bg-[#0f172a]">{{ $m->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <form wire:submit.prevent="saveCustomPrompt" class="space-y-4">
                        <div>
                            <label class="block text-[9px] font-bold text-slate-500 uppercase tracking-widest mb-1.5">Instrucciones del Sistema (Prompt)</label>
                            <textarea
                                wire:model="customPrompt"
                                rows="8"
                                placeholder="Escribe aquí las directrices específicas de la IA para esta máquina..."
                                class="w-full bg-white/3 border border-white/10 rounded-xl p-3.5 font-mono text-[10px] text-slate-200 placeholder-slate-600 focus:outline-none focus:border-cyan-500/50 transition-colors leading-relaxed"
                            ></textarea>
                        </div>

                        <button
                            type="submit"
                            onclick="window.playAudio('success');"
                            class="w-full py-2.5 bg-cyan-500 hover:bg-cyan-400 text-slate-950 text-xs font-black uppercase tracking-wider rounded-xl shadow-lg active:scale-95 transition-all"
                        >
                            Guardar Instrucciones
                        </button>
                    </form>
                </div>
            </div>

            <!-- Chatbot Testing Column (7 cols) -->
            <div class="lg:col-span-7 flex flex-col bg-[#0a0e17]/80 border border-white/5 rounded-3xl h-[560px] shadow-2xl backdrop-blur-md overflow-hidden relative">
                
                <div class="px-5 py-4 border-b border-white/5 bg-[#0a0e17]/90 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span class="text-xs font-black text-white tracking-wider uppercase font-outfit">Consola de Prueba IA</span>
                    </div>
                </div>

                <!-- Message logs -->
                <div
                    id="chatbot-box"
                    class="flex-1 overflow-y-auto p-5 space-y-4 font-sans text-xs scroll-smooth"
                    x-init="$watch('$wire.chatMessages', () => { setTimeout(() => { $el.scrollTop = $el.scrollHeight }, 100) })"
                >
                    @foreach($chatMessages as $msg)
                        <div class="flex {{ $msg['sender'] === 'user' ? 'justify-end' : 'justify-start' }}">
                            <div class="max-w-[85%] rounded-[18px] px-4 py-3 border {{ $msg['sender'] === 'user' ? 'bg-cyan-500/5 border-cyan-500/25 text-white' : 'bg-white/3 border-white/5 text-slate-200' }}">
                                @if($msg['sender'] === 'bot')
                                    <div class="flex items-center gap-1.5 mb-1.5 border-b border-white/4 pb-1 text-[9px] font-black tracking-widest text-cyan-400 uppercase">
                                        🤖 ASISTENTE MOCK ADMIN
                                    </div>
                                @endif
                                @if(!empty($msg['image_url']))
                                    <div class="mb-2">
                                        <img src="{{ $msg['image_url'] }}" class="max-h-48 rounded-lg border border-white/10 object-contain cursor-pointer" onclick="window.open('{{ $msg['image_url'] }}', '_blank')" />
                                    </div>
                                @endif
                                @if(!empty($msg['text']))
                                    <div class="leading-relaxed whitespace-pre-line prose prose-invert prose-xs">
                                        {!! nl2br(e($msg['text'])) !!}
                                    </div>
                                @endif
                                <span class="block text-[8px] font-mono text-slate-500 text-right mt-1.5">{{ $msg['timestamp'] }}</span>
                            </div>
                        </div>
                    @endforeach

                    @if($isThinking)
                        <div class="flex justify-start" wire:init="getBotResponse">
                            <div class="bg-white/3 border border-white/5 rounded-[18px] px-4 py-3 text-slate-400 max-w-[80%]">
                                <div class="flex items-center gap-2">
                                    <div class="flex gap-1">
                                        <span class="h-1.5 w-1.5 rounded-full bg-cyan-400 animate-bounce"></span>
                                        <span class="h-1.5 w-1.5 rounded-full bg-cyan-400 animate-bounce [animation-delay:0.2s]"></span>
                                        <span class="h-1.5 w-1.5 rounded-full bg-cyan-400 animate-bounce [animation-delay:0.4s]"></span>
                                    </div>
                                    <span class="text-[9px] font-mono text-slate-500 uppercase tracking-widest">Generando respuesta RAG...</span>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Inputs -->
                <form wire:submit.prevent="sendChatbotMessage" class="p-4 border-t border-white/5 bg-[#0a0e17]/95 flex flex-col gap-2">
                    <!-- Image attachment preview -->
                    @if($imageAttachment)
                        <div class="flex items-center gap-2 p-2 bg-white/5 border border-white/10 rounded-xl relative max-w-max">
                            <img src="{{ $imageAttachment->temporaryUrl() }}" class="h-14 w-14 object-cover rounded-lg border border-white/10" />
                            <button
                                type="button"
                                wire:click="$set('imageAttachment', null)"
                                class="absolute -top-1.5 -right-1.5 bg-red-500 hover:bg-red-400 text-white rounded-full p-1 shadow-md active:scale-90 transition-all flex items-center justify-center"
                            >
                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    @endif

                    <div class="flex items-center gap-3">
                        <!-- Image Upload Button -->
                        <label class="cursor-pointer p-2.5 bg-white/5 hover:bg-white/10 text-slate-400 hover:text-white rounded-xl border border-white/10 transition-colors flex items-center justify-center relative">
                            <input
                                type="file"
                                wire:model="imageAttachment"
                                accept="image/*"
                                class="hidden"
                            />
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <!-- Loading spinner for image upload -->
                            <div wire:loading wire:target="imageAttachment" class="absolute inset-0 bg-[#0a0e17]/85 rounded-xl flex items-center justify-center">
                                <svg class="animate-spin h-4 w-4 text-cyan-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                        </label>

                        <input
                            type="text"
                            wire:model="userInput"
                            placeholder="Prueba una pregunta técnica o sube una imagen..."
                            class="flex-1 bg-white/3 border border-white/10 rounded-xl px-4 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-cyan-500/50 transition-colors"
                        />
                        <button
                            type="submit"
                            class="p-2.5 bg-cyan-500 hover:bg-cyan-400 text-slate-950 rounded-xl active:scale-95 transition-all flex items-center justify-center"
                        >
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- ── TAB: Messages (Supervisor & operator chat log) ── -->
    @if($activeTab === 'messages')
        <div class="bg-[#0a0e17]/80 border border-white/5 rounded-3xl p-5 shadow-2xl backdrop-blur-md">
            <h2 class="text-base font-black text-white uppercase tracking-wider mb-5 font-outfit">Canal de Operarios</h2>
            
            <div class="space-y-4 max-h-[550px] overflow-y-auto pr-2">
                @if($messages->isEmpty())
                    <div class="py-12 text-center border border-dashed border-white/5 rounded-2xl">
                        <p class="text-xs font-mono text-slate-500">Sin mensajes registrados</p>
                    </div>
                @else
                    @foreach($messages as $msg)
                        <div class="group relative flex flex-col gap-1.5 p-4 rounded-2xl bg-white/3 border border-white/5 hover:bg-white/5 transition-all">
                            
                            <!-- Delete message on hover -->
                            <button
                                style="cursor: pointer;"
                                wire:click="deleteMessage('{{ $msg->id }}')"
                                onclick="window.playAudio('click');"
                                class="absolute top-4 right-4 p-1 text-slate-500 hover:text-red-400 opacity-0 group-hover:opacity-100 transition-all"
                            >
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>

                            <div class="flex items-center gap-2 text-xs font-mono">
                                <span class="font-black uppercase {{ $msg->from === 'admin' ? 'text-yellow-400' : 'text-cyan-400' }}">{{ $msg->senderName }}</span>
                                <span class="text-slate-600 font-bold">•</span>
                                <span class="text-slate-500 uppercase font-black">MÁQUINA: {{ $msg->machine_name }}</span>
                                <span class="text-slate-600 font-bold">•</span>
                                <span class="text-[10px] text-slate-500">{{ $msg->timestamp }}</span>
                            </div>
                            <p class="text-xs text-slate-200 mt-1 leading-relaxed whitespace-pre-wrap">{{ $msg->text }}</p>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    @endif

    <!-- ── Status change reason modal ── -->
    @if($showModal)
        <div class="fixed inset-0 bg-[#02050a]/80 backdrop-blur-md flex items-center justify-center p-4 z-50 animate-fade-in">
            <div class="w-full max-w-md bg-[#0d1117] border border-white/8 rounded-3xl p-6 shadow-2xl relative overflow-hidden">
                <button
                    wire:click="$set('showModal', false)"
                    onclick="window.playAudio('click');"
                    class="absolute top-4 right-4 text-slate-500 hover:text-white transition-colors"
                >
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                
                <h3 class="text-base font-black text-white uppercase tracking-wider mb-4 font-outfit">Motivo de Inactividad</h3>
                
                <form wire:submit.prevent="saveStatusChange" class="space-y-4 font-mono text-xs">
                    <div>
                        <p class="text-slate-400 leading-relaxed mb-3">
                            Describe brevemente el motivo para cambiar el estado de esta unidad a <span class="text-white font-bold">{{ strtoupper($targetStatus === 'warning' ? 'Avería' : ($targetStatus === 'maintenance' ? 'Mantenimiento' : 'Espera')) }}</span>.
                        </p>
                        <input
                            type="text"
                            wire:model="reason"
                            required
                            placeholder="Ej: Fuga de presión en cilindro, revisión preventiva..."
                            class="w-full bg-white/3 border border-white/10 rounded-xl px-4 py-2.5 text-white placeholder-slate-600 focus:outline-none focus:border-cyan-500/50 transition-colors"
                        />
                    </div>

                    <button
                        type="submit"
                        onclick="window.playAudio('success');"
                        class="w-full py-2.5 bg-gradient-to-r from-cyan-500 to-blue-500 hover:from-cyan-400 hover:to-blue-400 text-slate-950 font-black uppercase tracking-widest rounded-xl shadow-lg active:scale-95 transition-all"
                    >
                        Confirmar Cambio
                    </button>
                </form>
            </div>
        </div>
    @endif
</div>
