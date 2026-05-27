<div class="flex-1 flex flex-col max-w-[1400px] mx-auto w-full px-4 sm:px-6 py-6" x-data="{ showAlert: false, alertMsg: '', init() { $nextTick(() => { if (typeof scrollToBottom === 'function') { scrollToBottom('chatbot-box'); scrollToBottom('supervisor-box'); } }) } }" x-on:show-alert-message.window="alertMsg = $event.detail.message || ($event.detail && $event.detail[0] && $event.detail[0].message) || ''; showAlert = true;">
    <!-- Back button -->
    <div class="mb-6 flex items-center justify-between">
        <a
            href="/"
            onclick="window.playAudio('click');"
            class="flex items-center gap-2 text-xs font-bold text-slate-500 hover:text-cyan-600 uppercase tracking-widest transition-colors group"
        >
            <svg class="h-4 w-4 group-hover:-translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
            <span>Volver a Planta</span>
        </a>
        <div class="flex items-center gap-1.5 font-mono text-[10px] text-slate-500">
            <span>UNIDAD: {{ strtoupper($machine->id) }}</span>
        </div>
    </div>

    <!-- ── Cuidado Warning Banner ── -->
    @if($machine->status !== 'online' && !$alertDismissed)
        <div class="mb-6 bg-red-50 border border-red-200 rounded-2xl p-4 flex items-start justify-between gap-4 shadow-sm">
            <div class="flex items-start gap-3">
                <div class="p-2 bg-red-100 rounded-xl text-red-650 text-red-650 text-red-600">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div>
                    <h4 class="text-xs font-black text-red-900 uppercase tracking-wider">Cuidado</h4>
                    <p class="text-[11px] text-red-750 text-red-700 leading-relaxed mt-0.5">
                        ESTA MÁQUINA ESTÁ EN {{ strtoupper($machine->status === 'warning' ? 'Avería' : ($machine->status === 'maintenance' ? 'Mantenimiento' : 'Espera')) }}. TRABAJE CON EXTREMA PRECAUCIÓN.
                    </p>
                </div>
            </div>
            <button
                wire:click="dismissAlert"
                class="text-red-400 hover:text-red-700 transition-colors"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    @endif

    <!-- Main Grid: Spec + Upload, Chatbot, Supervisor Chat -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- Column 1: Info & Manuals Manager (3 cols) -->
        <div class="lg:col-span-3 flex flex-col gap-6">
            
            <!-- Specs Card -->
            <div class="bg-white border border-slate-200 rounded-3xl p-5 shadow-sm">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-4">
                    <h2 class="text-sm font-black text-slate-800 tracking-widest uppercase font-outfit">Ficha Técnica</h2>
                    @php
                        $badgeClasses = [
                            'online' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                            'maintenance' => 'bg-orange-50 text-orange-700 border-orange-100',
                            'waiting' => 'bg-amber-50 text-amber-700 border-amber-100',
                            'warning' => 'bg-red-50 text-red-700 border-red-100',
                        ];
                        $statusNames = [
                            'online' => 'Operativa',
                            'maintenance' => 'Mantenimiento',
                            'waiting' => 'En Espera',
                            'warning' => 'Avería',
                        ];
                    @endphp
                    <span class="px-2.5 py-0.5 rounded-full text-[9px] font-bold border {{ $badgeClasses[$machine->status] ?? $badgeClasses['online'] }}">
                        {{ $statusNames[$machine->status] ?? 'Desconocido' }}
                    </span>
                </div>
                
                <h1 class="text-xl font-black text-slate-900 tracking-wide uppercase leading-tight font-outfit">
                    {{ $machine->name }}
                </h1>

                @if(trim(strtolower($machine->status)) === 'maintenance' || trim(strtolower($machine->status)) === 'waiting' || trim(strtolower($machine->status)) === 'warning')
                    <button
                        type="button"
                        wire:click="openElapsedModal"
                        onclick="window.playAudio('click');"
                        class="w-full py-3 bg-orange-50 hover:bg-orange-100 text-orange-700 hover:text-orange-850 rounded-3xl border border-orange-200 font-black text-xs tracking-wider uppercase transition-all duration-200 flex items-center justify-center gap-2 shadow-sm mt-4"
                    >
                        <svg class="h-4.5 w-4.5 text-orange-600 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Tiempo Transcurrido</span>
                    </button>
                @endif
            </div>

            <!-- Registrar Incidencia Card -->
            <div class="bg-white border border-slate-200 rounded-3xl p-5 shadow-sm">
                <h2 class="text-base font-black text-slate-800 tracking-wider uppercase mb-4 font-outfit">Reportar Incidencia</h2>
                
                @if(session()->has('incidence_success'))
                    <div class="mb-4 bg-emerald-50 border border-emerald-100 text-emerald-800 text-xs font-mono px-3 py-2 rounded-xl">
                        {{ session('incidence_success') }}
                    </div>
                @endif

                <form wire:submit.prevent="registerIncidence" class="font-mono text-xs">
                    <div class="mb-4">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Nuevo Estado</label>
                        <select
                            wire:model.live="incidenceStatus"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:border-cyan-500/50 transition-colors"
                        >
                            @if(trim(strtolower($machine->status)) !== 'maintenance' && trim(strtolower($machine->status)) !== 'waiting' && trim(strtolower($machine->status)) !== 'warning')
                                <option value="online" class="bg-white text-slate-800">Operativa (Disponible)</option>
                            @endif
                            <option value="warning" class="bg-white text-slate-800">Avería</option>
                            <option value="maintenance" class="bg-white text-slate-800">Mantenimiento</option>
                            <option value="waiting" class="bg-white text-slate-800">En Espera</option>
                        </select>
                    </div>

                    @if($incidenceStatus !== 'online')
                        <div class="mb-4">
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Detalles del Motivo / Avería</label>
                            <textarea
                                wire:model="incidenceReason"
                                required
                                rows="3"
                                placeholder="Ej: Fuga de presión en cilindro neumático principal..."
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:border-cyan-500/50 transition-colors leading-relaxed"
                            ></textarea>
                        </div>
                    @endif

                    <button
                        type="submit"
                        onclick="window.playAudio('success');"
                        class="w-full py-3 bg-cyan-600 hover:bg-cyan-500 text-white text-xs font-black uppercase tracking-wider rounded-xl shadow-md active:scale-95 transition-all"
                    >
                        Registrar Estado
                    </button>
                </form>
            </div>

            <!-- Historial de Errores IA Quick Access -->
            <div class="mt-4 flex flex-col gap-3">
                <button
                    type="button"
                    wire:click="openErrorsModal"
                    onclick="window.playAudio('click');"
                    class="w-full py-3 bg-red-50 hover:bg-red-100 text-red-600 hover:text-red-700 rounded-3xl border border-red-200/80 font-black text-xs tracking-wider uppercase transition-all duration-200 flex items-center justify-center gap-2 shadow-sm"
                >
                    <svg class="h-4.5 w-4.5 text-red-500 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <span>Historial de Errores</span>
                </button>
                <button
                    type="button"
                    wire:click="openTrainingModal"
                    onclick="window.playAudio('click');"
                    class="w-full py-3 bg-cyan-50 hover:bg-cyan-100 text-cyan-700 hover:text-cyan-800 rounded-3xl border border-cyan-200/80 font-black text-xs tracking-wider uppercase transition-all duration-200 flex items-center justify-center gap-2 shadow-sm"
                >
                    <svg class="h-4.5 w-4.5 text-cyan-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    <span>Formación</span>
                </button>
                <button
                    type="button"
                    wire:click="openFaqModal"
                    onclick="window.playAudio('click');"
                    class="w-full py-3 bg-fuchsia-50 hover:bg-fuchsia-100 text-fuchsia-700 hover:text-fuchsia-800 rounded-3xl border border-fuchsia-200/80 font-black text-xs tracking-wider uppercase transition-all duration-200 flex items-center justify-center gap-2 shadow-sm"
                >
                    <svg class="h-4.5 w-4.5 text-fuchsia-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Preguntas Frecuentes</span>
                </button>
                <button
                    type="button"
                    wire:click="openIncidencesModal"
                    onclick="window.playAudio('click');"
                    class="w-full py-3 bg-amber-50 hover:bg-amber-100 text-amber-700 hover:text-amber-800 rounded-3xl border border-amber-200/80 font-black text-xs tracking-wider uppercase transition-all duration-200 flex items-center justify-center gap-2 shadow-sm"
                >
                    <svg class="h-4.5 w-4.5 text-amber-500 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Historial de Estados</span>
                </button>
            </div>
        </div>

        <!-- Column 2: Chatbot Console (6 cols) -->
        <div class="lg:col-span-6 flex flex-col bg-white ring-1 ring-cyan-500/20 rounded-3xl h-[700px] shadow-2xl shadow-cyan-900/5 overflow-hidden relative">
            <!-- Console Header -->
            <div class="px-6 py-4 flex items-center justify-between bg-slate-900 border-b border-slate-800">
                <div class="flex items-center gap-3">
                    <div class="relative flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-cyan-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-cyan-500 shadow-[0_0_8px_rgba(34,211,238,0.8)]"></span>
                    </div>
                    <span class="text-[15px] font-black text-white tracking-widest uppercase font-outfit drop-shadow-sm truncate">Asistente IA</span>
                </div>
                
                <div class="flex items-center gap-3">
                    <button
                        type="button"
                        wire:click="openSaveModal"
                        onclick="window.playAudio('click');"
                        title="Guardar esta conversación manualmente"
                        class="flex items-center gap-1.5 px-3.5 py-1.5 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-400 hover:to-teal-400 text-white rounded-full text-[10px] font-bold uppercase tracking-widest transition-all duration-300 shadow-[0_4px_10px_rgba(16,185,129,0.3)] hover:shadow-[0_6px_15px_rgba(16,185,129,0.4)] hover:-translate-y-0.5 border border-emerald-400/50"
                    >
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                        </svg>
                        <span class="hidden sm:inline">Guardar Conversación</span>
                    </button>
                    <button
                        wire:click="clearChatHistory"
                        onclick="window.playAudio('click');"
                        title="Limpiar el historial de la conversación"
                        class="flex items-center justify-center h-8 w-8 bg-slate-800/80 hover:bg-rose-500/20 border border-slate-700/50 hover:border-rose-500/30 text-slate-400 hover:text-rose-400 rounded-full transition-all duration-200"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                    <button
                        wire:click="openDocExplorer"
                        onclick="window.playAudio('click');"
                        title="Ver recursos y manuales de esta máquina"
                        class="flex items-center gap-1.5 px-3.5 py-1.5 bg-slate-800/80 hover:bg-cyan-500/20 border border-slate-700/50 hover:border-cyan-500/40 text-slate-300 hover:text-cyan-400 rounded-full text-[10px] font-bold uppercase tracking-widest transition-all duration-200"
                    >
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                        </svg>
                        <span class="hidden sm:inline">Recursos</span>
                    </button>
                </div>
            </div>

            <!-- Messages Logs -->
            <div
                id="chatbot-box"
                class="flex-1 overflow-y-auto p-6 space-y-5 font-sans text-sm scroll-smooth bg-gradient-to-b from-white to-slate-50/50"
                x-init="$watch('$wire.chatMessages', () => { setTimeout(() => { $el.scrollTop = $el.scrollHeight }, 100) })"
            >
                @foreach($chatMessages as $msg)
                    <div class="flex {{ $msg['sender'] === 'user' ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-[85%] rounded-[20px] px-6 py-4 shadow-sm {{ $msg['sender'] === 'user' ? 'bg-gradient-to-br from-slate-800 to-slate-900 text-white rounded-tr-sm border border-slate-700/50' : 'bg-white border border-slate-200/60 text-slate-700 rounded-tl-sm shadow-[0_4px_20px_-10px_rgba(0,0,0,0.05)]' }}">
                            @if($msg['sender'] === 'bot')
                                <!-- Bot Icon -->
                                <div class="flex items-center gap-2 mb-3 pb-2 border-b border-slate-100/80">
                                    <div class="bg-cyan-50 p-1.5 rounded-lg border border-cyan-100 text-cyan-600">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <span class="text-[11px] font-black tracking-widest text-slate-800 uppercase">Asistente Virtual</span>
                                </div>
                            @endif
                            @if(!empty($msg['image_url']))
                                <div class="mb-2">
                                    <img src="{{ $msg['image_url'] }}" class="max-h-56 rounded-lg border border-slate-200 object-contain cursor-pointer" onclick="window.open('{{ $msg['image_url'] }}', '_blank')" />
                                </div>
                            @endif
                            @if(!empty($msg['text']))
                                <div class="leading-relaxed whitespace-pre-line prose prose-sm max-w-none {{ $msg['sender'] === 'user' ? 'text-slate-100 prose-invert' : 'text-slate-700' }}">
                                    {!! nl2br(e($msg['text'])) !!}
                                </div>
                            @endif
                            <span class="block text-[10px] font-mono {{ $msg['sender'] === 'user' ? 'text-slate-400' : 'text-slate-400' }} text-right mt-3 opacity-80">{{ $msg['timestamp'] }}</span>
                        </div>
                    </div>
                @endforeach

                <!-- Thinking Spinner Animation -->
                @if($isThinking)
                    <div class="flex justify-start" wire:init="getBotResponse">
                        <div class="bg-white border border-slate-200/60 rounded-[20px] px-6 py-4 shadow-[0_4px_20px_-10px_rgba(0,0,0,0.05)] rounded-tl-sm flex items-center gap-3">
                            <div class="flex gap-1.5">
                                <span class="h-2 w-2 rounded-full bg-cyan-500 animate-bounce"></span>
                                <span class="h-2 w-2 rounded-full bg-cyan-400 animate-bounce [animation-delay:0.2s]"></span>
                                <span class="h-2 w-2 rounded-full bg-cyan-300 animate-bounce [animation-delay:0.4s]"></span>
                            </div>
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Analizando...</span>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Console Inputs (Modern Floating Bar) -->
            <div class="p-6 bg-gradient-to-t from-white via-white to-transparent shrink-0">
                <form wire:submit.prevent="sendChatbotMessage" class="relative flex flex-col gap-3">
                    
                    <!-- Image attachment preview -->
                    @if($imageAttachment)
                        <div class="flex items-center gap-2 p-1.5 bg-white border border-slate-200 rounded-xl max-w-max shadow-sm ml-2">
                            <img src="{{ $imageAttachment->temporaryUrl() }}" class="h-12 w-12 object-cover rounded-lg border border-slate-100" />
                            <button
                                type="button"
                                wire:click="$set('imageAttachment', null)"
                                class="bg-red-50 hover:bg-red-100 text-red-500 rounded-lg p-2 transition-colors h-12 flex items-center justify-center"
                            >
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    @endif

                    <!-- Input Pill Container -->
                    <div class="flex items-center bg-white border border-slate-200 shadow-[0_8px_30px_rgb(0,0,0,0.04)] rounded-full p-1.5 transition-all focus-within:shadow-[0_8px_30px_rgb(6,182,212,0.1)] focus-within:border-cyan-300/50">
                        
                        <!-- Upload Button inside pill -->
                        <label class="cursor-pointer h-10 w-10 flex items-center justify-center rounded-full text-slate-400 hover:text-cyan-600 hover:bg-cyan-50 transition-colors shrink-0 relative">
                            <input
                                type="file"
                                wire:model="imageAttachment"
                                accept="image/*"
                                class="hidden"
                            />
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                            <!-- Loading spinner -->
                            <div wire:loading wire:target="imageAttachment" class="absolute inset-0 bg-white/90 rounded-full flex items-center justify-center">
                                <svg class="animate-spin h-4 w-4 text-cyan-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                        </label>

                        <!-- Input Field -->
                        <input
                            type="text"
                            wire:model="userInput"
                            placeholder="Pregúntame cualquier duda..."
                            class="flex-1 bg-transparent border-none px-3 text-[15px] text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-0"
                        />

                        <!-- Submit Button inside pill -->
                        <button
                            type="submit"
                            class="h-10 w-10 bg-cyan-600 hover:bg-cyan-500 text-white rounded-full flex items-center justify-center shrink-0 transition-colors shadow-sm"
                        >
                            <svg class="h-4 w-4 ml-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 12h16M14 6l6 6-6 6" />
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Column 3: Supervisor Chat Console (3 cols) -->
        <div class="lg:col-span-3 flex flex-col bg-white border border-slate-200 rounded-3xl h-[700px] shadow-sm overflow-hidden relative">
            <!-- Chat Header -->
            <div class="px-5 py-4 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg class="h-5 w-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" />
                    </svg>
                    <span class="text-sm font-black text-slate-800 tracking-wider uppercase font-outfit">Canal Supervisor</span>
                </div>
            </div>

            <!-- Messages Log -->
            <div
                id="supervisor-box"
                class="flex-1 overflow-y-auto p-4 space-y-3 text-sm scroll-smooth bg-slate-50/30"
                x-init="$watch('$wire.supervisorMessages', () => { setTimeout(() => { $el.scrollTop = $el.scrollHeight }, 100) })"
            >
                @if($supervisorMessages->isEmpty())
                    <div class="py-12 text-center">
                        <svg class="h-8 w-8 text-slate-400 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        <p class="text-xs font-mono text-slate-400">Canal vacío. Escribe para dar aviso.</p>
                    </div>
                @else
                    @foreach($supervisorMessages as $msg)
                        <div class="group relative flex flex-col gap-1 rounded-2xl p-4 border {{ $msg->from === 'admin' ? 'bg-amber-50 border-amber-200/60' : 'bg-white border-slate-200' }} shadow-sm">
                            
                            <div class="flex items-center justify-between border-b border-slate-100 pb-1.5">
                                <span class="text-[10px] font-black uppercase tracking-wider {{ $msg->from === 'admin' ? 'text-amber-700' : 'text-slate-500' }}">
                                    {{ $msg->senderName }}
                                </span>
                                <div class="flex items-center gap-2">
                                    <span class="text-[9px] font-mono text-slate-400">{{ $msg->timestamp }}</span>
                                </div>
                            </div>
                            <p class="text-sm leading-relaxed text-slate-705 text-slate-700 mt-1 whitespace-pre-wrap">{{ $msg->text }}</p>
                        </div>
                    @endforeach
                @endif
            </div>

            <!-- Chat Inputs -->
            <form wire:submit.prevent="sendSupervisorMessage" class="p-3 border-t border-slate-200 bg-slate-50 flex items-center gap-2">
                <input
                    type="text"
                    wire:model="supervisorInput"
                    placeholder="Escribe aviso..."
                    class="flex-1 bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:border-amber-500/40 transition-colors shadow-sm"
                />
                <button
                    type="submit"
                    class="p-2 bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-xl shadow-md active:scale-95 transition-all flex items-center justify-center"
                >
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </button>
            </form>
        </div>

    </div>

    {{-- ── Document Explorer Modal ── --}}
    @if($showDocExplorerModal)
        <div
            class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4 z-[90]"
            wire:click.self="closeDocExplorer"
            x-data="{ activeTab: 'manuals' }"
        >
            <div class="w-full max-w-3xl max-h-[80vh] bg-white border border-slate-200 rounded-3xl shadow-2xl flex flex-col overflow-hidden">

                {{-- Modal Header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 bg-slate-50 flex-shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="h-8 w-8 bg-cyan-50 border border-cyan-100 rounded-lg flex items-center justify-center text-cyan-600 shadow-sm">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-slate-800 uppercase tracking-wider">Recursos de la Máquina</h3>
                            <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wider mt-0.5">{{ $machine->name }}</p>
                        </div>
                    </div>
                    <button wire:click="closeDocExplorer" class="text-slate-400 hover:text-slate-700 transition-colors">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Tabs --}}
                <div class="flex border-b border-slate-200 bg-white">
                    <button @click="activeTab = 'manuals'" :class="activeTab === 'manuals' ? 'border-b-2 border-cyan-500 text-cyan-600' : 'text-slate-500'" class="px-6 py-3 text-xs font-bold uppercase tracking-widest transition-colors">Manuales</button>
                </div>

                {{-- Search & Filter Bar --}}
                <div class="px-6 py-4 border-b border-slate-200 flex-shrink-0 flex flex-col sm:flex-row gap-3 bg-white" x-show="activeTab === 'manuals'">
                    <div class="relative flex-1">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </span>
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="docSearch"
                            placeholder="Buscar por nombre o contenido..."
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-10 pr-4 py-2 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-cyan-500/50 transition-colors"
                        />
                    </div>

                </div>

                {{-- Documents List --}}
                <div class="flex-1 overflow-y-auto p-5 space-y-2 bg-slate-50/20">
                    @if($machineManuals->isEmpty())
                        <div class="py-16 text-center">
                            <div class="h-14 w-14 bg-slate-50 border border-slate-200 rounded-2xl flex items-center justify-center text-slate-400 mx-auto mb-3">
                                <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <p class="text-xs font-mono text-slate-400 uppercase tracking-widest">Sin recursos disponibles para esta máquina</p>
                            <p class="text-[10px] text-slate-500 mt-1">Ve al panel de administración para subir archivos.</p>
                        </div>
                    @else
                        @php
                            $typeColors = [
                                'pdf'   => ['bg' => 'bg-red-50 text-red-700 border-red-100', 'label' => 'PDF'],
                                'image' => ['bg' => 'bg-purple-50 text-purple-700 border-purple-100', 'label' => 'IMG'],
                                'excel' => ['bg' => 'bg-emerald-50 text-emerald-700 border-emerald-100', 'label' => 'XLS'],
                                'word'  => ['bg' => 'bg-blue-50 text-blue-700 border-blue-100', 'label' => 'DOC'],
                                'other' => ['bg' => 'bg-slate-50 text-slate-700 border-slate-100', 'label' => 'FILE'],
                            ];
                        @endphp

                        @foreach($machineManuals as $manual)
                            @php
                                $type = $manual->file_type ?? 'other';
                                $tc = $typeColors[$type] ?? $typeColors['other'];
                            @endphp
                            <div
                                wire:click="openViewer('{{ $manual->id }}')"
                                onclick="window.playAudio('click');"
                                class="group flex items-center gap-4 p-3.5 bg-white hover:bg-slate-50 border border-slate-200 hover:border-cyan-500/25 rounded-2xl cursor-pointer transition-all duration-200 shadow-sm"
                            >
                                {{-- File Type Badge --}}
                                <div class="h-10 w-10 flex-shrink-0 rounded-xl border flex items-center justify-center font-black text-[9px] tracking-widest {{ $tc['bg'] }}">
                                    {{ $tc['label'] }}
                                </div>

                                {{-- File Info --}}
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-bold text-slate-850 text-slate-800 truncate group-hover:text-cyan-600 transition-colors uppercase">{{ $manual->fileName }}</p>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        <span class="text-[10px] text-slate-400 font-mono">{{ is_numeric($manual->size) ? number_format($manual->size / 1024, 1) . ' KB' : $manual->size }}</span>

                                        @if($manual->in_chat)
                                            <span class="text-[9px] font-black text-emerald-600 uppercase tracking-widest">RAG</span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Open icon --}}
                                <svg class="h-4 w-4 text-slate-400 group-hover:text-cyan-600 flex-shrink-0 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- ── Document Viewer Modal ── --}}
    @if($showViewerModal && $viewingManual)
        <div
            class="fixed inset-0 bg-slate-900/45 backdrop-blur-sm flex items-center justify-center p-4 z-[100]"
            wire:click.self="closeViewer"
        >
            <div class="w-full max-w-5xl h-[88vh] bg-white border border-slate-200 rounded-3xl shadow-2xl flex flex-col overflow-hidden">

                {{-- Viewer Header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 bg-slate-50 flex-shrink-0">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="h-8 w-8 bg-cyan-50 border border-cyan-155 border-cyan-100 rounded-lg flex items-center justify-center text-cyan-600 flex-shrink-0 shadow-sm">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-sm font-black text-slate-800 truncate uppercase" title="{{ $viewingManual->fileName }}">{{ $viewingManual->fileName }}</h3>
                            <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wider mt-0.5">{{ $viewingManual->category }} &bull; {{ $machine->name }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 flex-shrink-0">
                        {{-- Back to explorer button --}}
                        <button
                            wire:click="closeViewer"
                            onclick="window.playAudio('click');"
                            class="flex items-center gap-1.5 bg-white hover:bg-slate-50 border border-slate-200 text-slate-600 hover:text-slate-800 px-3 py-1.5 rounded-xl text-[10px] font-bold uppercase tracking-wider transition-all shadow-sm"
                        >
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                            </svg>
                            <span class="hidden sm:inline">Recursos</span>
                        </button>
                        @if($viewingManual->file_path)
                            <a
                                href="{{ asset('storage/' . $viewingManual->file_path) }}"
                                download="{{ $viewingManual->fileName }}"
                                onclick="window.playAudio('click');"
                                class="flex items-center gap-1.5 bg-cyan-55 bg-cyan-50 border border-cyan-100 hover:bg-cyan-100 text-cyan-700 px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all shadow-sm"
                            >
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                <span class="hidden sm:inline">Descargar</span>
                            </a>
                        @endif
                        <button wire:click="closeViewer" class="text-slate-400 hover:text-slate-700 transition-colors">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Viewer Body --}}
                <div class="flex-1 bg-slate-100 overflow-hidden relative flex flex-col items-center justify-center">
                    @if($viewingManual->file_path)
                        @if($viewingManual->file_type === 'pdf')
                            <iframe
                                src="{{ asset('storage/' . $viewingManual->file_path) }}#toolbar=1&navpanes=0"
                                class="w-full h-full border-none"
                            ></iframe>
                        @elseif($viewingManual->file_type === 'image')
                            <div class="w-full h-full p-6 flex items-center justify-center overflow-auto bg-slate-50">
                                <img
                                    src="{{ asset('storage/' . $viewingManual->file_path) }}"
                                    alt="{{ $viewingManual->fileName }}"
                                    class="max-w-full max-h-full object-contain rounded-lg border border-slate-200 shadow-lg"
                                />
                            </div>
                        @elseif($viewingManual->file_type === 'word' || $viewingManual->file_type === 'excel')
                            <div class="w-full h-full p-6 sm:p-8 overflow-y-auto flex flex-col bg-slate-50">
                                <iframe
                                    src="https://view.officeapps.live.com/op/embed.aspx?src={{ urlencode(asset('storage/' . $viewingManual->file_path)) }}"
                                    class="w-full h-full border-none"
                                ></iframe>
                            </div>
                        @else
                            <div class="w-full h-full p-8 flex flex-col items-center justify-center bg-slate-50">
                                <div class="text-center max-w-md">
                                    <div class="h-16 w-16 bg-white border border-slate-200 rounded-2xl flex items-center justify-center text-slate-400 mx-auto mb-4 shadow-sm">
                                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                    <h4 class="text-sm font-black text-slate-800 uppercase tracking-wider">Formato no visualizable</h4>
                                    <p class="text-xs text-slate-500 mt-2 mb-6">Este tipo de archivo no admite previsualización directa en el navegador.</p>
                                    <a
                                        href="{{ asset('storage/' . $viewingManual->file_path) }}"
                                        download
                                        class="inline-flex items-center gap-1.5 bg-cyan-600 hover:bg-cyan-500 text-white px-5 py-2.5 rounded-xl text-xs font-bold transition-all shadow-md"
                                    >
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                        </svg>
                                        <span>Descargar y Abrir</span>
                                    </a>
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="w-full h-full p-6 sm:p-8 overflow-y-auto flex flex-col bg-slate-50">
                            <div class="max-w-3xl mx-auto w-full bg-white border border-slate-200 rounded-2xl p-6 sm:p-8 font-mono text-xs text-slate-700 leading-relaxed break-words whitespace-pre-wrap shadow-sm">
                                {{ $viewingManual->text }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- ── Visor de Errores Modal ── --}}
    @if($showErrorsModal)
        <div
            class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4 z-[100]"
            style="z-index: 9999;"
            wire:click.self="closeErrorsModal"
        >
            <div 
                class="w-full max-w-5xl max-h-[85vh] bg-white border border-slate-200 rounded-3xl shadow-2xl flex flex-col overflow-hidden"
            >
                {{-- Modal Header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 bg-slate-50 flex-shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="h-8 w-8 bg-emerald-50 border border-emerald-200 rounded-lg flex items-center justify-center text-emerald-600 shadow-sm">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-slate-800 uppercase tracking-wider">Conversaciones Guardadas</h3>
                            <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wider mt-0.5">{{ $machine->name }}</p>
                        </div>
                    </div>
                    <button wire:click="closeErrorsModal" class="text-slate-400 hover:text-slate-700 transition-colors">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Modal Body --}}
                <div class="flex-1 overflow-y-auto p-6 bg-slate-50/50">
                    @if($machineErrors->isEmpty())
                            <div class="py-20 text-center">
                                <div class="h-16 w-16 bg-white border border-slate-200 rounded-2xl flex items-center justify-center text-slate-400 mx-auto mb-4 shadow-sm">
                                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                    </svg>
                                </div>
                                <h4 class="text-xs font-mono text-slate-400 uppercase tracking-widest">No hay conversaciones</h4>
                                <p class="text-[10px] text-slate-500 mt-1 max-w-sm mx-auto">Usa el botón "Guardar Conversación" en el chat para registrar un hilo completo manualmente.</p>
                            </div>
                        @else
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                @foreach($machineErrors as $conv)
                                    <div class="bg-white border border-slate-200 hover:border-emerald-300/50 rounded-2xl p-5 shadow-sm hover:shadow-md transition-all group relative flex flex-col">
                                        {{-- Delete Button --}}
                                        <button
                                            wire:click="deleteMachineError('{{ $conv->id }}')"
                                            onclick="window.playAudio('click');"
                                            title="Eliminar conversación"
                                            class="absolute top-4 right-4 text-slate-300 hover:text-red-500 transition-colors opacity-0 group-hover:opacity-100"
                                        >
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                        
                                        <div class="flex justify-between items-start mb-3 pr-6">
                                            <h4 class="text-sm font-black text-slate-800 line-clamp-2">{{ $conv->title }}</h4>
                                        </div>
                                        <span class="text-[10px] font-mono text-slate-400 mb-2 inline-flex items-center gap-1.5 bg-slate-50 border border-slate-100 rounded-md px-2 py-0.5 w-max">
                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                            {{ $conv->created_at->format('d/m/Y H:i') }}
                                        </span>
                                        
                                        @if($conv->description)
                                            <p class="text-xs text-slate-600 line-clamp-3 mb-4 leading-relaxed flex-1">{{ $conv->description }}</p>
                                        @else
                                            <p class="text-xs text-slate-400 italic mb-4 flex-1">Sin descripción</p>
                                        @endif
                                        
                                        <div class="mt-auto pt-4 border-t border-slate-100 flex gap-2">
                                            <button
                                                wire:click="viewConversation('{{ $conv->id }}')"
                                                onclick="window.playAudio('click');"
                                                class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 bg-slate-50 hover:bg-slate-100 text-slate-700 rounded-xl text-xs font-black uppercase tracking-wider transition-colors border border-slate-200"
                                            >
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                                Visualizar
                                            </button>
                                            <button
                                                wire:click="loadConversation('{{ $conv->id }}')"
                                                onclick="if(confirm('Esto reemplazará tu chat actual. ¿Continuar?')) { window.playAudio('success'); } else { return false; }"
                                                class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 rounded-xl text-xs font-black uppercase tracking-wider transition-colors border border-emerald-200/50"
                                            >
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                                                </svg>
                                                Restaurar
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                </div>
            </div>
        </div>
    @endif

    {{-- ── Visualizar Conversación Modal ── --}}
    @if($showViewConversationModal)
        <div
            class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4 z-[120]"
            style="z-index: 9999;"
            wire:click.self="closeViewConversationModal"
        >
            <div class="w-full max-w-4xl h-[85vh] bg-white border border-slate-200 rounded-3xl shadow-2xl flex flex-col overflow-hidden">
                {{-- Modal Header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 bg-slate-50 flex-shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="h-8 w-8 bg-slate-100 border border-slate-200 rounded-lg flex items-center justify-center text-slate-600 shadow-sm">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-slate-800 uppercase tracking-wider line-clamp-1">{{ $viewingConversationTitle ?: 'Conversación Guardada' }}</h3>
                            <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wider mt-0.5">Modo de sólo lectura</p>
                        </div>
                    </div>
                    <button wire:click="closeViewConversationModal" class="flex items-center gap-2 px-3 py-1.5 text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded-lg transition-colors font-bold text-xs tracking-wider uppercase">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Volver
                    </button>
                </div>

                {{-- Modal Body: Chat History Viewer --}}
                <div class="flex-1 overflow-y-auto p-6 bg-slate-50/50 space-y-4">
                    @forelse($viewingConversationMessages as $msg)
                        <div class="flex {{ $msg['sender'] === 'user' ? 'justify-end' : 'justify-start' }}">
                            <div class="max-w-[85%] rounded-[20px] px-6 py-4 shadow-sm {{ $msg['sender'] === 'user' ? 'bg-gradient-to-br from-slate-800 to-slate-900 text-white rounded-tr-sm border border-slate-700/50' : 'bg-white border border-slate-200/60 text-slate-700 rounded-tl-sm shadow-[0_4px_20px_-10px_rgba(0,0,0,0.05)]' }}">
                                @if($msg['sender'] === 'bot')
                                    <div class="flex items-center gap-2 mb-3 pb-2 border-b border-slate-100/80">
                                        <div class="bg-cyan-50 p-1.5 rounded-lg border border-cyan-100 text-cyan-600">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                        <span class="text-[11px] font-black tracking-widest text-slate-800 uppercase">Asistente Virtual</span>
                                    </div>
                                @endif
                                @if(!empty($msg['image_url']))
                                    <div class="mb-2">
                                        <img src="{{ $msg['image_url'] }}" class="max-h-56 rounded-lg border border-slate-200 object-contain cursor-zoom-in" onclick="window.open('{{ $msg['image_url'] }}', '_blank')" />
                                    </div>
                                @endif
                                @if(!empty($msg['text']))
                                    <div class="leading-relaxed whitespace-pre-line prose prose-sm max-w-none {{ $msg['sender'] === 'user' ? 'text-slate-100 prose-invert' : 'text-slate-700' }}">
                                        {!! nl2br(e($msg['text'])) !!}
                                    </div>
                                @endif
                                <span class="block text-[10px] font-mono {{ $msg['sender'] === 'user' ? 'text-slate-400' : 'text-slate-400' }} text-right mt-3 opacity-80">{{ $msg['timestamp'] }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="py-20 text-center">
                            <div class="h-16 w-16 bg-white border border-slate-200 rounded-2xl flex items-center justify-center text-slate-400 mx-auto mb-4 shadow-sm">
                                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                            </div>
                            <h4 class="text-xs font-mono text-slate-400 uppercase tracking-widest">Conversación Vacía</h4>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    @endif

    {{-- ── Formación Modal (Solo Manual) ── --}}
    @if($showTrainingModal)
        <div
            class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4 z-[90]"
            wire:click.self="closeTrainingModal"
            x-data="{ isExpanded: false }"
            x-on:toggle-pdf-view.window="isExpanded = $event.detail.shown"
        >
            <div 
                class="w-full bg-white border border-slate-200 rounded-3xl shadow-2xl flex flex-col overflow-hidden"
                style="transition: max-width 0.5s ease-in-out, height 0.5s ease-in-out, max-height 0.5s ease-in-out;"
                :style="isExpanded ? 'max-width: 80rem; height: 92vh; max-height: 92vh;' : 'max-width: 56rem; max-height: 85vh; height: auto;'"
            >
                {{-- Modal Header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 bg-slate-50 flex-shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="h-8 w-8 bg-cyan-50 border border-cyan-200 rounded-lg flex items-center justify-center text-cyan-600 shadow-sm">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-slate-800 uppercase tracking-wider">Manual de Formación</h3>
                            <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wider mt-0.5">{{ $machine->name }}</p>
                        </div>
                    </div>
                    <button wire:click="closeTrainingModal" class="text-slate-400 hover:text-slate-700 transition-colors">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Modal Body --}}
                <div class="flex-1 overflow-y-auto p-6 sm:p-8 bg-white">
                    @if(empty(trim(strip_tags($machine->manual_content, '<img><iframe><video><audio><a>'))))
                        <div class="py-20 text-center">
                            <div class="h-16 w-16 bg-slate-50 border border-slate-200 rounded-2xl flex items-center justify-center text-slate-400 mx-auto mb-4 shadow-sm">
                                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                            </div>
                            <h4 class="text-xs font-black text-slate-800 uppercase tracking-widest">Sin manual de aprendizaje</h4>
                            <p class="text-[11px] font-medium text-slate-500 mt-1 max-w-sm mx-auto">El administrador aún no ha añadido material formativo para esta máquina.</p>
                        </div>
                    @else
                        <div class="prose prose-sm max-w-none text-slate-700 ql-editor" x-data="{
                            renderPreview() {
                                return window.renderPdfPreview(this.$refs.rawContent.innerHTML, '');
                            }
                        }">
                            <div x-ref="rawContent" style="display: none;">{!! $machine->manual_content !!}</div>
                            <div x-html="renderPreview()"></div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- ── Preguntas Frecuentes Modal ── --}}
    @if($showFaqModal)
        <div
            class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4 z-[90]"
            wire:click.self="closeFaqModal"
            x-data="{ isExpanded: false }"
            x-on:toggle-pdf-view.window="isExpanded = $event.detail.shown"
        >
            <div 
                class="w-full bg-white border border-slate-200 rounded-3xl shadow-2xl flex flex-col overflow-hidden"
                style="transition: max-width 0.5s ease-in-out, height 0.5s ease-in-out, max-height 0.5s ease-in-out;"
                :style="isExpanded ? 'max-width: 80rem; height: 92vh; max-height: 92vh;' : 'max-width: 56rem; max-height: 85vh; height: auto;'"
            >
                {{-- Modal Header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 bg-slate-50 flex-shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="h-8 w-8 bg-fuchsia-50 border border-fuchsia-200 rounded-lg flex items-center justify-center text-fuchsia-600 shadow-sm">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-slate-800 uppercase tracking-wider">Preguntas Frecuentes</h3>
                            <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wider mt-0.5">{{ $machine->name }}</p>
                        </div>
                    </div>
                    <button wire:click="closeFaqModal" class="text-slate-400 hover:text-slate-700 transition-colors">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Modal Body --}}
                <div class="flex-1 overflow-y-auto p-6 sm:p-8 bg-white">
                    @if(empty(trim(strip_tags($machine->faq_content, '<img><iframe><video><audio><a>'))))
                        <div class="py-20 text-center">
                            <div class="h-16 w-16 bg-slate-50 border border-slate-200 rounded-2xl flex items-center justify-center text-slate-400 mx-auto mb-4 shadow-sm">
                                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h4 class="text-xs font-black text-slate-800 uppercase tracking-widest">Sin Preguntas Frecuentes</h4>
                            <p class="text-[11px] font-medium text-slate-500 mt-1 max-w-sm mx-auto">El administrador aún no ha añadido un listado de preguntas frecuentes para esta unidad.</p>
                        </div>
                    @else
                        <div class="prose prose-sm max-w-none text-slate-700 ql-editor" x-data="{
                            renderPreview() {
                                return window.renderPdfPreview(this.$refs.rawContent.innerHTML, '');
                            }
                        }">
                            <div x-ref="rawContent" style="display: none;">{!! $machine->faq_content !!}</div>
                            <div x-html="renderPreview()"></div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- ── Guardar Conversación Modal ── --}}
    @if($showSaveModal)
        <div
            class="fixed inset-0 bg-slate-900/60 backdrop-blur-md flex items-center justify-center p-4 z-[110] transition-opacity duration-300"
            wire:click.self="closeSaveModal"
        >
            <div class="w-full max-w-lg bg-white border border-slate-200 rounded-3xl shadow-2xl flex flex-col overflow-hidden transform transition-all duration-300 scale-100">
                <div class="px-6 py-5 border-b border-slate-200 bg-slate-50 flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 bg-emerald-100 text-emerald-600 rounded-xl flex items-center justify-center shadow-sm">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-slate-800 uppercase tracking-wide font-outfit">Guardar Conversación</h3>
                            <p class="text-[11px] text-slate-500 mt-0.5">Registrar historial manualmente</p>
                        </div>
                    </div>
                    <button wire:click="closeSaveModal" class="text-slate-400 hover:text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-full p-1.5 transition-colors">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <form wire:submit.prevent="saveConversation" class="p-6 space-y-5">
                    <div>
                        <label for="saveTitle" class="block text-xs font-black text-slate-700 uppercase tracking-widest mb-2">¿Nombre de la conversación?</label>
                        <input
                            type="text"
                            id="saveTitle"
                            wire:model.defer="saveTitle"
                            placeholder="Ej: Problema con el motor principal..."
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-700 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-colors"
                            required
                        >
                        @error('saveTitle') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label for="saveDescription" class="block text-xs font-black text-slate-700 uppercase tracking-widest mb-2">Descripción</label>
                        <textarea
                            id="saveDescription"
                            wire:model.defer="saveDescription"
                            rows="3"
                            placeholder="Añade detalles adicionales sobre la solución..."
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-700 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-colors resize-none"
                        ></textarea>
                        @error('saveDescription') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="pt-2 flex justify-end gap-3 border-t border-slate-100">
                        <button
                            type="button"
                            wire:click="closeSaveModal"
                            class="px-5 py-2.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-600 rounded-xl text-xs font-black uppercase tracking-widest transition-colors"
                        >
                            Cancelar
                        </button>
                        <button
                            type="submit"
                            class="px-5 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl text-xs font-black uppercase tracking-widest shadow-lg shadow-emerald-500/30 transition-all hover:-translate-y-0.5"
                        >
                            Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- ── Tiempo Transcurrido Modal ── --}}
    @if($showElapsedModal)
        <div
            class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4 z-[100]"
            style="z-index: 9999;"
            wire:click.self="closeElapsedModal"
        >
            <div class="w-full max-w-lg bg-white border border-slate-200 rounded-3xl shadow-2xl overflow-hidden flex flex-col">
                {{-- Modal Header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 bg-slate-50 flex-shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 bg-orange-50 border border-orange-200 rounded-xl flex items-center justify-center text-orange-600 shadow-sm">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-slate-800 uppercase tracking-wider font-outfit">Registrar Tiempo</h3>
                            <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wider mt-0.5">{{ $machine->name }}</p>
                        </div>
                    </div>
                    <button wire:click="closeElapsedModal" class="text-slate-400 hover:text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-full p-1.5 transition-colors">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Modal Body --}}
                <form wire:submit.prevent="saveElapsedTime" class="p-6 space-y-4 font-mono text-xs">
                    <div>
                        <span class="block text-[11px] text-slate-500 font-bold uppercase tracking-wider mb-2">¿Cuánto tiempo ha estado la unidad en este estado?</span>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-1">Horas</label>
                                <input
                                    type="number"
                                    wire:model="elapsedHours"
                                    min="0"
                                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm text-slate-800 focus:outline-none focus:border-orange-500/50 transition-colors"
                                />
                                @error('elapsedHours') <span class="text-red-500 text-[10px] mt-1">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-1">Minutos</label>
                                <input
                                    type="number"
                                    wire:model="elapsedMinutes"
                                    min="0"
                                    max="59"
                                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm text-slate-800 focus:outline-none focus:border-orange-500/50 transition-colors"
                                />
                                @error('elapsedMinutes') <span class="text-red-500 text-[10px] mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-1.5">Observaciones / Trabajos Realizados</label>
                        <textarea
                            wire:model="elapsedComments"
                            rows="4"
                            placeholder="Ej: Se realizaron ajustes en las presiones de aire y limpieza de guías..."
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:border-orange-500/50 transition-colors leading-relaxed"
                        ></textarea>
                        @error('elapsedComments') <span class="text-red-500 text-[10px] mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button
                            type="button"
                            wire:click="closeElapsedModal"
                            class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold uppercase tracking-widest transition-colors"
                        >
                            Cancelar
                        </button>
                        <button
                            type="submit"
                            class="px-5 py-2.5 bg-orange-500 hover:bg-orange-600 text-white rounded-xl text-xs font-black uppercase tracking-widest shadow-md transition-all active:scale-95"
                        >
                            Registrar y Activar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- ── Historial de Incidencias/Estados Modal ── --}}
    @if($showIncidencesModal)
        <div
            class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4 z-[100]"
            style="z-index: 9999;"
            wire:click.self="closeIncidencesModal"
        >
            <div class="w-full max-w-4xl max-h-[85vh] bg-white border border-slate-200 rounded-3xl shadow-2xl flex flex-col overflow-hidden">
                {{-- Modal Header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 bg-slate-50 flex-shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 bg-amber-50 border border-amber-200 rounded-xl flex items-center justify-center text-amber-600 shadow-sm">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-slate-800 uppercase tracking-wider font-outfit">Historial del Ciclo de Vida</h3>
                            <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wider mt-0.5">{{ $machine->name }} &bull; Estados e Incidencias</p>
                        </div>
                    </div>
                    <button wire:click="closeIncidencesModal" class="text-slate-400 hover:text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-full p-1.5 transition-colors">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Modal Body --}}
                <div class="flex-1 overflow-y-auto p-6 bg-slate-50/50 space-y-6">
                    
                    {{-- Stats Summary --}}
                    @php
                        $totalCount = $machineIncidences->count();
                        $averiaCount = $machineIncidences->where('type', 'warning')->count();
                        $maintCount = $machineIncidences->where('type', 'maintenance')->count();
                        $esperaCount = $machineIncidences->where('type', 'waiting')->count();
                        $operCount = $machineIncidences->where('type', 'info')->count();
                    @endphp
                    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 shrink-0">
                        <div class="bg-white border border-slate-200 rounded-2xl p-3.5 shadow-sm text-center">
                            <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1.5">Total Cambios</div>
                            <div class="text-xl font-black text-slate-800">{{ $totalCount }}</div>
                        </div>
                        <div class="bg-white border border-emerald-100 rounded-2xl p-3.5 shadow-sm text-center">
                            <div class="text-[10px] font-black text-emerald-600 uppercase tracking-widest leading-none mb-1.5">Operativa</div>
                            <div class="text-xl font-black text-emerald-700">{{ $operCount }}</div>
                        </div>
                        <div class="bg-white border border-red-100 rounded-2xl p-3.5 shadow-sm text-center">
                            <div class="text-[10px] font-black text-red-650 uppercase tracking-widest leading-none mb-1.5">Avería</div>
                            <div class="text-xl font-black text-red-600">{{ $averiaCount }}</div>
                        </div>
                        <div class="bg-white border border-orange-100 rounded-2xl p-3.5 shadow-sm text-center">
                            <div class="text-[10px] font-black text-orange-600 uppercase tracking-widest leading-none mb-1.5">Mantenimiento</div>
                            <div class="text-xl font-black text-orange-700">{{ $maintCount }}</div>
                        </div>
                        <div class="bg-white border border-amber-100 rounded-2xl p-3.5 shadow-sm text-center">
                            <div class="text-[10px] font-black text-amber-600 uppercase tracking-widest leading-none mb-1.5">En Espera</div>
                            <div class="text-xl font-black text-amber-700">{{ $esperaCount }}</div>
                        </div>
                    </div>

                    {{-- Timeline Log --}}
                    @if($machineIncidences->isEmpty())
                        <div class="py-20 text-center bg-white border border-slate-200 rounded-3xl shadow-sm">
                            <div class="h-16 w-16 bg-slate-50 border border-slate-200 rounded-2xl flex items-center justify-center text-slate-400 mx-auto mb-4 shadow-sm">
                                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h4 class="text-xs font-mono text-slate-400 uppercase tracking-widest">Sin registros históricos</h4>
                            <p class="text-[10px] text-slate-500 mt-1 max-w-sm mx-auto">No se ha registrado ningún cambio de estado.</p>
                        </div>
                    @else
                        <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm">
                            <div class="space-y-6">
                                @foreach($machineIncidences as $inc)
                                    @php
                                        $dotColors = [
                                            'info' => 'bg-emerald-500 ring-emerald-100',
                                            'warning' => 'bg-red-500 ring-red-100',
                                            'maintenance' => 'bg-orange-500 ring-orange-100',
                                            'waiting' => 'bg-amber-500 ring-amber-100',
                                        ];
                                        $textColors = [
                                            'info' => 'text-emerald-700 bg-emerald-50 border-emerald-100',
                                            'warning' => 'text-red-700 bg-red-50 border-red-100',
                                            'maintenance' => 'text-orange-700 bg-orange-50 border-orange-100',
                                            'waiting' => 'text-amber-700 bg-amber-50 border-amber-100',
                                        ];
                                        $labels = [
                                            'info' => 'Operativa',
                                            'warning' => 'Avería',
                                            'maintenance' => 'Mantenimiento',
                                            'waiting' => 'En Espera',
                                        ];
                                        $type = $inc->type ?? 'info';
                                    @endphp

                                    <div class="flex gap-4 items-start relative group/modal transition-all duration-300">
                                        <!-- Left dot column -->
                                        <div class="flex flex-col items-center shrink-0 relative mt-1">
                                            <!-- Dot -->
                                            <div class="h-4 w-4 rounded-full border-2 border-white {{ $dotColors[$type] ?? $dotColors['info'] }} ring-4 shadow-[0_0_8px_rgba(0,0,0,0.05)] flex items-center justify-center z-10">
                                                @if($type !== 'info')
                                                    <span class="h-1.5 w-1.5 rounded-full bg-white animate-ping"></span>
                                                @endif
                                            </div>
                                            <!-- Vertical Line connected to next item -->
                                            @if(!$loop->last)
                                                <div class="w-0.5 bg-slate-100 absolute top-4 bottom-0 -mb-6"></div>
                                            @endif
                                        </div>

                                        <!-- Right content column -->
                                        <div class="flex-1 min-w-0 pb-2">
                                            <!-- Header line with Inline Delete Button -->
                                            <div class="flex items-center justify-between gap-3 pr-2">
                                                <div class="flex items-center gap-2.5">
                                                    <span class="px-2.5 py-0.5 rounded-full text-[9px] font-black border uppercase tracking-wider leading-none {{ $textColors[$type] ?? $textColors['info'] }}">
                                                        {{ $labels[$type] ?? 'Desconocido' }}
                                                    </span>
                                                    <span class="text-[10px] font-mono text-slate-400">
                                                        {{ $inc->created_at ? $inc->created_at->format('d/m/Y H:i:s') : $inc->timestamp }}
                                                    </span>
                                                </div>

                                                {{-- Always Visible Delete Button --}}
                                                <button
                                                    wire:click="deleteIncidence('{{ $inc->id }}')"
                                                    onclick="return confirm('¿Seguro que deseas eliminar este registro del historial?')"
                                                    title="Eliminar del historial"
                                                    class="flex items-center gap-1.5 px-2.5 py-1 text-[10px] font-bold text-red-500 hover:bg-red-50 hover:text-red-700 border border-transparent hover:border-red-100 rounded-lg transition-all"
                                                >
                                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                    <span>Eliminar</span>
                                                </button>
                                            </div>

                                            <!-- Message box -->
                                            <div class="text-xs text-slate-700 leading-relaxed font-mono mt-2 bg-slate-50 border border-slate-100 rounded-xl p-3.5 max-w-2xl flex flex-col gap-1 shadow-sm">
                                                <div><span class="font-bold text-slate-900">{{ $inc->machine_name ?? $machine->name }}</span> marcada en <span class="font-bold uppercase text-slate-800">{{ $inc->clean_state }}</span></div>
                                                
                                                @if($inc->elapsed_time)
                                                    <div class="text-slate-600 mt-1 border-t border-slate-200/60 pt-1">
                                                        <span class="font-bold text-slate-900">Tiempo transcurrido hasta estar operativa:</span> {{ $inc->elapsed_time }}
                                                    </div>
                                                @endif

                                                @if($inc->clean_description)
                                                    <div class="text-slate-500 mt-1.5 border-t border-slate-200/60 pt-1.5">
                                                        <span class="font-bold">Descripción:</span> {{ $inc->clean_description }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Alpine.js scroll assistant + PDF viewer helpers -->
    <script>
        function scrollToBottom(id) {
            const el = document.getElementById(id);
            if (el) {
                el.scrollTop = el.scrollHeight;
            }
        }

        function togglePdfViewer(viewerId, btnId) {
            const v = document.getElementById(viewerId);
            const b = document.getElementById(btnId);
            if (!v) return;
            const eyeOpenSvg = '<svg style="height:14px;width:14px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg><span>Visualizar</span>';
            const eyeClosedSvg = '<svg style="height:14px;width:14px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L6.59 6.59m7.532 7.532l3.29 3.29M3 3l18 18"/></svg><span>Ocultar</span>';

            if (v.style.display === 'none' || !v.style.display) {
                v.style.display = 'block';
                if (b) b.innerHTML = eyeClosedSvg;
            } else {
                v.style.display = 'none';
                if (b) b.innerHTML = eyeOpenSvg;
            }
            _firePdfToggleEvent();
        }

        function closePdfViewer(el) {
            const viewer = el.closest('[id^=pdf-viewer-]');
            if (!viewer) return;
            viewer.style.display = 'none';
            const btnId = 'pdf-btn-' + viewer.id;
            const b = document.getElementById(btnId);
            if (b) {
                b.innerHTML = '<svg style="height:14px;width:14px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg><span>Visualizar</span>';
            }
            _firePdfToggleEvent();
        }

        function _firePdfToggleEvent() {
            const any = Array.from(document.querySelectorAll('[id^=pdf-viewer-]')).some(el => el.style.display === 'block');
            window.dispatchEvent(new CustomEvent('toggle-pdf-view', { detail: { shown: any } }));
        }

        window.renderPdfPreview = function(html, fallbackHtml = '') {
            if (!html || !html.trim()) return fallbackHtml;
            let div = document.createElement('div');
            div.innerHTML = html;
            
            let pdfGroups = {};
            let links = div.querySelectorAll('a');
            links.forEach(link => {
                let href = link.getAttribute('href') || '';
                let isPdf = href.toLowerCase().endsWith('.pdf') || 
                            href.toLowerCase().includes('.pdf?') || 
                            href.toLowerCase().includes('/training-files/') || 
                            link.innerText.toLowerCase().includes('.pdf');
                if (isPdf) {
                    if (!pdfGroups[href]) {
                        pdfGroups[href] = [];
                    }
                    pdfGroups[href].push(link);
                }
            });
            
            Object.keys(pdfGroups).forEach((href, idx) => {
                let group = pdfGroups[href];
                let displayName = '';
                let sizeInfo = '';
                
                group.forEach(link => {
                    let text = link.innerText.trim();
                    let sizeMatch = text.match(/(\d+(?:\.\d+)?\s*(?:KB|MB|GB|kb|mb|gb))/i);
                    if (sizeMatch) {
                        sizeInfo = sizeMatch[1].toUpperCase();
                    }
                    
                    let isMetadata = text.toLowerCase() === 'pdf' || 
                                     text.includes('·') || 
                                     /^\s*(?:pdf\s*)?\(?\d+(?:\.\d+)?\s*(?:kb|mb|gb)\)?\s*$/i.test(text);
                    
                    if (!isMetadata) {
                        let cleaned = text.replace('📄', '').trim();
                        if (cleaned) displayName = cleaned;
                    }
                });
                
                if (!displayName) {
                    try {
                        let urlParts = href.split('/');
                        displayName = decodeURIComponent(urlParts[urlParts.length - 1]).split('?')[0];
                    } catch(e) {
                        displayName = 'Documento';
                    }
                }
                
                // Determine Extension and Styling
                let ext = 'FILE';
                let displayNameLower = displayName.toLowerCase();
                let hrefLower = href.toLowerCase();
                
                if (displayNameLower.includes('.pdf') || hrefLower.includes('.pdf')) ext = 'PDF';
                else if (displayNameLower.includes('.xls') || hrefLower.includes('.xls')) ext = 'XLSX';
                else if (displayNameLower.includes('.doc') || hrefLower.includes('.doc')) ext = 'DOCX';
                else if (displayNameLower.includes('.png') || displayNameLower.includes('.jpg') || displayNameLower.includes('.jpeg')) ext = 'IMG';
                else {
                    let parts = displayName.split('.');
                    if (parts.length > 1) {
                        let last = parts.pop().toUpperCase();
                        if (last.length <= 4) ext = last;
                    }
                }

                let badgeStyle = '';
                let iconGradient = '';
                
                if (ext.includes('PDF')) {
                    badgeStyle = 'bg-red-50 border-red-100 text-red-600';
                    iconGradient = 'from-red-500 via-rose-500 to-red-700 shadow-red-500/20';
                } else if (ext.includes('XLS')) {
                    badgeStyle = 'bg-emerald-50 border-emerald-100 text-emerald-600';
                    iconGradient = 'from-emerald-500 via-teal-500 to-emerald-700 shadow-emerald-500/20';
                } else if (ext.includes('DOC')) {
                    badgeStyle = 'bg-blue-50 border-blue-100 text-blue-600';
                    iconGradient = 'from-blue-500 via-indigo-500 to-blue-700 shadow-blue-500/20';
                } else if (ext.includes('IMG')) {
                    badgeStyle = 'bg-purple-50 border-purple-100 text-purple-600';
                    iconGradient = 'from-purple-500 via-fuchsia-500 to-purple-700 shadow-purple-500/20';
                } else {
                    badgeStyle = 'bg-slate-50 border-slate-200 text-slate-600';
                    iconGradient = 'from-slate-500 via-slate-600 to-slate-700 shadow-slate-500/20';
                }
                
                let viewerId = 'pdf-viewer-' + idx + '-' + Math.floor(Math.random() * 100000);
                let btnId = 'pdf-btn-' + viewerId;
                
                let pdfWidget = `<div class="not-prose my-6 rounded-2xl border border-slate-200/80 overflow-hidden shadow-[0_4px_20px_-4px_rgba(0,0,0,0.06)] hover:shadow-[0_8px_30px_-6px_rgba(0,0,0,0.1)] transition-all duration-300 bg-white group/card">
                    <div class="px-5 py-4 bg-gradient-to-r from-white via-white to-slate-50/30 flex flex-wrap items-center justify-between gap-4">
                        <div class="flex items-center gap-3.5 min-w-0 flex-1">
                            <div class="h-12 w-12 flex-shrink-0 rounded-xl flex items-center justify-center shadow-md group-hover/card:scale-105 transition-all duration-300 bg-gradient-to-br ${iconGradient} text-white">
                                <svg class="text-white h-6 w-6" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 9h1.5m-1.5 3H12m-3 3h4" />
                                </svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="text-[14px] font-bold text-slate-800 leading-snug truncate" title="${displayName}">${displayName}</div>
                                <div class="flex items-center gap-2 mt-1 flex-wrap">
                                    <span class="inline-flex items-center px-1.5 py-0.5 border rounded text-[9px] font-black uppercase tracking-wider leading-none ${badgeStyle}">${ext}</span>
                                    ${sizeInfo ? `<span class="text-slate-300 text-[10px] leading-none">•</span><span class="text-[11px] text-slate-500 font-medium leading-none">${sizeInfo}</span>` : ''}
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2.5 flex-shrink-0">
                            <a href="${href}" target="_blank" rel="noopener" class="px-4 py-2 bg-slate-100 hover:bg-slate-800 text-slate-600 hover:text-white text-[11px] font-bold rounded-xl transition-all duration-200 flex items-center gap-2 no-underline shadow-sm border border-slate-200/40">
                                <svg class="h-3.5 w-3.5" width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                                <span>Abrir</span>
                            </a>
                            <button id="${btnId}" type="button" onclick="window.togglePdfViewer('${viewerId}', '${btnId}')" class="px-4 py-2 bg-gradient-to-r from-cyan-500 to-cyan-600 hover:from-cyan-600 hover:to-cyan-700 text-white text-[11px] font-bold rounded-xl transition-all duration-200 flex items-center gap-2 shadow-sm shadow-cyan-500/20 hover:shadow-md hover:shadow-cyan-500/30">
                                <svg class="h-3.5 w-3.5" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <span>Visualizar</span>
                            </button>
                        </div>
                    </div>
                    <div id="${viewerId}" style="display: none;" class="border-t border-slate-150">
                        <div class="px-5 py-3 bg-gradient-to-r from-slate-800 via-slate-800 to-slate-900 flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <div class="h-6 w-6 rounded-md bg-white/10 flex items-center justify-center">
                                    <svg class="text-cyan-400 h-3.5 w-3.5" width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <span class="text-[10px] font-black text-slate-300 tracking-wider uppercase">Vista Previa del Documento</span>
                            </div>
                            <button type="button" onclick="window.closePdfViewer(this)" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white text-[10px] font-bold uppercase tracking-wider transition-all">
                                <svg class="h-3 w-3" width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                <span>Cerrar</span>
                            </button>
                        </div>
                        <div class="bg-slate-100 p-2">
                            <iframe src="${href}#view=FitH&toolbar=1" class="w-full border-none rounded-lg bg-white shadow-inner" style="height: 600px;"></iframe>
                        </div>
                        <div class="px-5 py-2.5 bg-slate-50 border-t border-slate-150 flex items-center justify-between">
                            <span class="text-[10px] text-slate-400 font-mono tracking-wide">Visor PDF Interactivo</span>
                            <a href="${href}" download class="text-[10px] text-cyan-600 hover:text-cyan-700 font-bold flex items-center gap-1 no-underline hover:underline transition-all">
                                <svg class="h-3 w-3" width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                <span>Descargar Archivo</span>
                            </a>
                        </div>
                    </div>
                </div>`;
                
                let firstLink = group[0];
                let parentP = firstLink.closest('p');
                let tempDiv = document.createElement('div');
                tempDiv.innerHTML = pdfWidget;
                let widgetNode = tempDiv.firstElementChild;
                
                if (parentP && parentP.parentNode) {
                    let clone = parentP.cloneNode(true);
                    let linkInClone = clone.querySelector('a');
                    if (linkInClone) linkInClone.remove();
                    let textLeft = clone.innerText.trim();
                    if (textLeft === '' || textLeft === '📄') {
                        parentP.parentNode.replaceChild(widgetNode, parentP);
                    } else {
                        firstLink.parentNode.replaceChild(widgetNode, firstLink);
                    }
                } else {
                    firstLink.parentNode.replaceChild(widgetNode, firstLink);
                }
                
                for (let i = 1; i < group.length; i++) {
                    let extraLink = group[i];
                    let extraP = extraLink.closest('p');
                    if (extraP && extraP.parentNode) {
                        let clone = extraP.cloneNode(true);
                        let linkInClone = clone.querySelector('a');
                        if (linkInClone) linkInClone.remove();
                        if (clone.innerText.trim() === '') {
                            extraP.remove();
                        } else {
                            extraLink.remove();
                        }
                    } else {
                        extraLink.remove();
                    }
                }
            });
            
            let elements = Array.from(div.children);
            let out = '';
            let inAccordion = false;
            let accordionHtml = '';
            
            elements.forEach(el => {
                if (el.tagName === 'H3' && el.innerText.includes('❓')) {
                    if (inAccordion) {
                        out += `<div class="mt-4 pt-4 border-t border-slate-100 text-slate-650 text-sm leading-relaxed">` + accordionHtml + `</div></details>`;
                    }
                    inAccordion = true;
                    accordionHtml = '';
                    out += `<details class="bg-white border border-slate-200 rounded-xl my-4 p-4 shadow-sm cursor-pointer group"><summary class="font-black text-base text-slate-800 list-none flex items-center gap-2 outline-none"><span class="text-fuchsia-600 text-lg group-open:rotate-90 transition-transform">▶</span> ` + el.innerHTML.replace('❓', '') + `</summary>`;
                } else {
                    if (inAccordion) {
                        accordionHtml += el.outerHTML;
                    } else {
                        out += el.outerHTML;
                    }
                }
            });
            
            if (inAccordion) {
                out += `<div class="mt-4 pt-4 border-t border-slate-100 text-slate-650 text-sm leading-relaxed">` + accordionHtml + `</div></details>`;
            }
            
            return out;
        };
    </script>

    <!-- Custom Alert Modal -->
    <div
        x-show="showAlert"
        class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4 z-[200]"
        style="z-index: 10000; display: none;"
        x-transition
    >
        <div class="w-full max-w-sm bg-white border border-slate-200 rounded-3xl shadow-2xl overflow-hidden flex flex-col p-6 text-center">
            <div class="h-12 w-12 bg-amber-50 border border-amber-200 rounded-full flex items-center justify-center text-amber-500 mx-auto mb-4 shadow-sm">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <h3 class="text-sm font-black text-slate-800 uppercase tracking-wider font-outfit mb-2">¡Atención!</h3>
            <p class="text-xs text-slate-600 leading-relaxed font-mono mb-6" x-text="alertMsg"></p>
            <button
                type="button"
                x-on:click="showAlert = false"
                onclick="window.playAudio('click');"
                class="w-full py-2.5 bg-orange-500 hover:bg-orange-600 text-white rounded-xl text-xs font-black uppercase tracking-widest shadow-md transition-all active:scale-95"
                style="background-color: rgb(249, 115, 22);"
            >
                Aceptar
            </button>
        </div>
    </div>
</div>
