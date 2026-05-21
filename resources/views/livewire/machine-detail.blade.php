<div class="flex-1 flex flex-col max-w-[1400px] mx-auto w-full px-4 sm:px-6 py-6" x-data="{ init() { $nextTick(() => { this.scrollToBottom('chatbot-box'); this.scrollToBottom('supervisor-box'); }) } }">
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

        <!-- Column 1: Info & Manuals Manager (4 cols) -->
        <div class="lg:col-span-4 flex flex-col gap-6">
            
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
            </div>

            <!-- Registrar Incidencia Card -->
            <div class="bg-white border border-slate-200 rounded-3xl p-5 shadow-sm">
                <h2 class="text-sm font-black text-slate-800 tracking-widest uppercase mb-4 font-outfit">Reportar Incidencia</h2>
                
                @if(session()->has('incidence_success'))
                    <div class="mb-4 bg-emerald-50 border border-emerald-100 text-emerald-800 text-[10px] font-mono px-3 py-2 rounded-xl">
                        {{ session('incidence_success') }}
                    </div>
                @endif

                <form wire:submit.prevent="registerIncidence" class="space-y-4 font-mono text-[10px]">
                    <div>
                        <label class="block text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Nuevo Estado</label>
                        <select
                            wire:model.live="incidenceStatus"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-cyan-500/50 transition-colors"
                        >
                            <option value="online" class="bg-white text-slate-800">Operativa (Disponible)</option>
                            <option value="warning" class="bg-white text-slate-800">Avería</option>
                            <option value="maintenance" class="bg-white text-slate-800">Mantenimiento</option>
                            <option value="waiting" class="bg-white text-slate-800">En Espera</option>
                        </select>
                    </div>

                    @if($incidenceStatus !== 'online')
                        <div>
                            <label class="block text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Detalles del Motivo / Avería</label>
                            <textarea
                                wire:model="incidenceReason"
                                required
                                rows="3"
                                placeholder="Ej: Fuga de presión en cilindro neumático principal..."
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-[11px] text-slate-800 placeholder-slate-400 focus:outline-none focus:border-cyan-500/50 transition-colors leading-relaxed"
                            ></textarea>
                        </div>
                    @endif

                    <button
                        type="submit"
                        onclick="window.playAudio('success');"
                        class="w-full py-2.5 bg-cyan-600 hover:bg-cyan-500 text-white text-[10px] font-black uppercase tracking-wider rounded-xl shadow-md active:scale-95 transition-all"
                    >
                        Registrar Estado
                    </button>
                </form>
            </div>

            <!-- Historial de Errores IA Quick Access -->
            <div class="mt-4">
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
            </div>
        </div>

        <!-- Column 2: Chatbot Console (5 cols) -->
        <div class="lg:col-span-5 flex flex-col bg-white border border-slate-200 rounded-3xl h-[560px] shadow-sm overflow-hidden relative">
            <!-- Console Header -->
            <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between bg-slate-50">
                <div class="flex items-center gap-2">
                    <span class="h-2 w-2 rounded-full bg-cyan-500 animate-pulse"></span>
                    <span class="text-xs font-black text-slate-800 tracking-wider uppercase font-outfit">Asistente IA (RAG)</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-[8px] font-mono text-cyan-600 bg-cyan-50 px-2 py-0.5 border border-cyan-100 rounded-full uppercase tracking-widest hidden sm:inline-block">
                        Asistente Activo
                    </span>
                    <button
                        wire:click="clearChatHistory"
                        onclick="window.playAudio('click');"
                        title="Limpiar el historial de la conversación"
                        class="flex items-center gap-1.5 px-2.5 py-1 bg-white hover:bg-rose-50 border border-slate-200 hover:border-rose-300 text-slate-500 hover:text-rose-600 rounded-lg text-[10px] font-bold uppercase tracking-widest transition-all duration-200 group shadow-sm"
                    >
                        <svg class="h-3.5 w-3.5 group-hover:rotate-12 transition-transform text-slate-400 group-hover:text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        <span class="hidden sm:inline">Limpiar Chat</span>
                    </button>
                    <button
                        wire:click="openDocExplorer"
                        onclick="window.playAudio('click');"
                        title="Ver recursos y manuales de esta máquina"
                        class="flex items-center gap-1.5 px-2.5 py-1 bg-white hover:bg-slate-50 border border-slate-200 hover:border-cyan-500/40 text-slate-600 hover:text-cyan-600 rounded-lg text-[10px] font-bold uppercase tracking-widest transition-all duration-200 group shadow-sm"
                    >
                        <svg class="h-3.5 w-3.5 group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                        </svg>
                        <span class="hidden sm:inline">Recursos</span>
                    </button>
                </div>
            </div>

            <!-- Messages Logs -->
            <div
                id="chatbot-box"
                class="flex-1 overflow-y-auto p-5 space-y-4 font-sans text-xs scroll-smooth bg-slate-50/30"
                x-init="$watch('$wire.chatMessages', () => { setTimeout(() => { $el.scrollTop = $el.scrollHeight }, 100) })"
            >
                @foreach($chatMessages as $msg)
                    <div class="flex {{ $msg['sender'] === 'user' ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-[85%] rounded-[18px] px-4 py-3 border {{ $msg['sender'] === 'user' ? 'bg-cyan-50/80 border-cyan-100 text-slate-800 shadow-sm' : 'bg-white border-slate-200/80 text-slate-700 shadow-sm' }}">
                            @if($msg['sender'] === 'bot')
                                <!-- Bot Icon -->
                                <div class="flex items-center gap-1.5 mb-1.5 border-b border-slate-100 pb-1 text-[9px] font-black tracking-widest text-cyan-600 uppercase">
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    <span>ASISTENTE VIRTUAL</span>
                                </div>
                            @endif
                            @if(!empty($msg['image_url']))
                                <div class="mb-2">
                                    <img src="{{ $msg['image_url'] }}" class="max-h-48 rounded-lg border border-slate-200 object-contain cursor-pointer" onclick="window.open('{{ $msg['image_url'] }}', '_blank')" />
                                </div>
                            @endif
                            @if(!empty($msg['text']))
                                <div class="leading-relaxed whitespace-pre-line prose prose-xs text-slate-700">
                                    {!! nl2br(e($msg['text'])) !!}
                                </div>
                            @endif
                            <span class="block text-[8px] font-mono text-slate-400 text-right mt-1.5">{{ $msg['timestamp'] }}</span>
                        </div>
                    </div>
                @endforeach

                <!-- Thinking Spinner Animation -->
                @if($isThinking)
                    <div class="flex justify-start" wire:init="getBotResponse">
                        <div class="bg-white border border-slate-200 rounded-[18px] px-4 py-3 text-slate-500 max-w-[80%] shadow-sm">
                            <div class="flex items-center gap-2">
                                <div class="flex gap-1">
                                    <span class="h-1.5 w-1.5 rounded-full bg-cyan-500 animate-bounce"></span>
                                    <span class="h-1.5 w-1.5 rounded-full bg-cyan-500 animate-bounce [animation-delay:0.2s]"></span>
                                    <span class="h-1.5 w-1.5 rounded-full bg-cyan-500 animate-bounce [animation-delay:0.4s]"></span>
                                </div>
                                <span class="text-[9px] font-mono text-slate-400 uppercase tracking-widest">Analizando manuales...</span>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Console Inputs -->
            <form wire:submit.prevent="sendChatbotMessage" class="p-4 border-t border-slate-200 bg-slate-50 flex flex-col gap-2">
                <!-- Image attachment preview -->
                @if($imageAttachment)
                    <div class="flex items-center gap-2 p-2 bg-white border border-slate-200 rounded-xl relative max-w-max shadow-sm">
                        <img src="{{ $imageAttachment->temporaryUrl() }}" class="h-14 w-14 object-cover rounded-lg border border-slate-100" />
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

                <div class="flex items-center gap-2">
                    <!-- Image Upload Button -->
                    <label class="cursor-pointer p-2.5 bg-white hover:bg-slate-100 text-slate-500 hover:text-slate-800 rounded-xl border border-slate-200 transition-colors flex items-center justify-center relative shadow-sm shrink-0">
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
                        <div wire:loading wire:target="imageAttachment" class="absolute inset-0 bg-white/90 rounded-xl flex items-center justify-center">
                            <svg class="animate-spin h-4 w-4 text-cyan-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    </label>

                    <input
                        type="text"
                        wire:model="userInput"
                        placeholder="Pregúntame o sube una imagen del fallo..."
                        class="flex-1 bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-cyan-500/50 transition-colors shadow-sm"
                    />
                    <button
                        type="submit"
                        class="p-2.5 bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-500 hover:to-blue-500 text-white rounded-xl shadow-md hover:shadow-lg active:scale-95 transition-all flex items-center justify-center shrink-0"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </button>
                </div>
            </form>
        </div>

        <!-- Column 3: Supervisor Chat Console (3 cols) -->
        <div class="lg:col-span-3 flex flex-col bg-white border border-slate-200 rounded-3xl h-[560px] shadow-sm overflow-hidden relative">
            <!-- Chat Header -->
            <div class="px-5 py-4 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg class="h-4 w-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" />
                    </svg>
                    <span class="text-xs font-black text-slate-800 tracking-wider uppercase font-outfit">Canal Supervisor</span>
                </div>
            </div>

            <!-- Messages Log -->
            <div
                id="supervisor-box"
                class="flex-1 overflow-y-auto p-4 space-y-3 text-xs scroll-smooth bg-slate-50/30"
                x-init="$watch('$wire.supervisorMessages', () => { setTimeout(() => { $el.scrollTop = $el.scrollHeight }, 100) })"
            >
                @if($supervisorMessages->isEmpty())
                    <div class="py-12 text-center">
                        <svg class="h-6 w-6 text-slate-400 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        <p class="text-[10px] font-mono text-slate-400">Canal vacío. Escribe para dar aviso.</p>
                    </div>
                @else
                    @foreach($supervisorMessages as $msg)
                        <div class="group relative flex flex-col gap-1 rounded-2xl p-3 border {{ $msg->from === 'admin' ? 'bg-amber-50 border-amber-200/60' : 'bg-white border-slate-200' }} shadow-sm">
                            
                            <!-- Delete message on hover (restricted to admins) -->
                            @auth
                                <button
                                    style="cursor: pointer;"
                                    wire:click="deleteSupervisorMessage('{{ $msg->id }}')"
                                    onclick="window.playAudio('click');"
                                    class="absolute top-2 right-2 text-slate-400 hover:text-red-650 text-red-600 opacity-0 group-hover:opacity-100 transition-all"
                                >
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            @endauth

                            <div class="flex items-center justify-between border-b border-slate-100 pb-1">
                                <span class="text-[9px] font-black uppercase tracking-wider {{ $msg->from === 'admin' ? 'text-amber-700' : 'text-slate-500' }}">
                                    {{ $msg->senderName }}
                                </span>
                                <span class="text-[8px] font-mono text-slate-400">{{ $msg->timestamp }}</span>
                            </div>
                            <p class="text-[11px] leading-relaxed text-slate-705 text-slate-700 mt-1 whitespace-pre-wrap">{{ $msg->text }}</p>
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
                    class="flex-1 bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-amber-500/40 transition-colors shadow-sm"
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
        >
            <div class="w-full max-w-3xl max-h-[80vh] bg-white border border-slate-200 rounded-3xl shadow-2xl flex flex-col overflow-hidden">

                {{-- Modal Header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 bg-slate-50 flex-shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="h-8 w-8 bg-cyan-55 bg-cyan-50 border border-cyan-100 rounded-lg flex items-center justify-center text-cyan-600 shadow-sm">
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

                {{-- Search & Filter Bar --}}
                <div class="px-6 py-4 border-b border-slate-200 flex-shrink-0 flex flex-col sm:flex-row gap-3 bg-white">
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
                                        <span class="text-[10px] text-slate-400 font-mono">{{ number_format($manual->size / 1024, 1) }} KB</span>

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
                                download
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
                                <div class="bg-amber-50 border border-amber-200 text-amber-800 p-4 rounded-2xl text-xs font-mono mb-6 leading-relaxed max-w-3xl mx-auto flex-shrink-0 flex items-start gap-3 shadow-sm">
                                    <svg class="h-5 w-5 flex-shrink-0 mt-0.5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    <div>
                                        <strong>Vista previa del texto extraído:</strong> El asistente IA utiliza este contenido para responder consultas sobre esta máquina. Para ver el formato original (tablas, imágenes), descarga el archivo original.
                                    </div>
                                </div>
                                <div class="flex-1 max-w-3xl mx-auto w-full bg-white border border-slate-200 rounded-2xl p-6 sm:p-8 font-mono text-xs text-slate-700 leading-relaxed overflow-y-auto break-words whitespace-pre-wrap shadow-sm">
                                    @if(trim($viewingManual->text))
                                        {{ $viewingManual->text }}
                                    @else
                                        <span class="text-slate-400 italic">No se pudo extraer texto descriptivo de este documento.</span>
                                    @endif
                                </div>
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
            class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4 z-[90]"
            wire:click.self="closeErrorsModal"
        >
            <div class="w-full max-w-4xl max-h-[85vh] bg-white border border-slate-200 rounded-3xl shadow-2xl flex flex-col overflow-hidden">
                {{-- Modal Header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 bg-slate-50 flex-shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="h-8 w-8 bg-rose-50 border border-rose-200 rounded-lg flex items-center justify-center text-rose-600 shadow-sm">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-slate-800 uppercase tracking-wider">Historial de Incidencias y Errores (IA)</h3>
                            <p class="text-[10px] text-slate-505 text-slate-500 font-bold uppercase tracking-wider mt-0.5">{{ $machine->name }}</p>
                        </div>
                    </div>
                    <button wire:click="closeErrorsModal" class="text-slate-400 hover:text-slate-700 transition-colors">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Modal Body --}}
                <div class="flex-1 overflow-y-auto p-6 space-y-4 bg-slate-50/20">
                    @if($machineErrors->isEmpty())
                        <div class="py-20 text-center">
                            <div class="h-16 w-16 bg-slate-50 border border-slate-200 rounded-2xl flex items-center justify-center text-slate-455 text-slate-400 mx-auto mb-4">
                                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h4 class="text-xs font-mono text-slate-455 text-slate-400 uppercase tracking-widest">Sin errores registrados</h4>
                            <p class="text-[10px] text-slate-500 mt-1 max-w-sm mx-auto">Cualquier consulta en el chat IA que mencione un error o contenga una imagen del fallo se registrará aquí de forma automática.</p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 gap-4">
                            @foreach($machineErrors as $err)
                                <div class="relative bg-white border border-slate-200 rounded-2xl p-5 flex flex-col md:flex-row gap-4 shadow-sm group">
                                    {{-- Delete Button (maintenance/admin mode) --}}
                                    <button
                                        wire:click="deleteMachineError('{{ $err->id }}')"
                                        onclick="window.playAudio('click');"
                                        title="Eliminar registro"
                                        class="absolute top-4 right-4 text-slate-400 hover:text-red-650 text-red-600 transition-colors md:opacity-0 group-hover:opacity-100"
                                    >
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>

                                    {{-- Left Side: Image / Status badge --}}
                                    <div class="flex-shrink-0 flex flex-col items-center justify-start gap-2">
                                        @if($err->image_path)
                                            <div class="h-28 w-28 rounded-xl overflow-hidden border border-slate-200 shadow-sm bg-white cursor-zoom-in" onclick="window.open('{{ asset('storage/' . $err->image_path) }}', '_blank')">
                                                <img src="{{ asset('storage/' . $err->image_path) }}" class="h-full w-full object-cover" />
                                            </div>
                                        @else
                                            <div class="h-28 w-28 rounded-xl border border-dashed border-slate-200 bg-slate-50 flex flex-col items-center justify-center text-slate-300 shadow-inner">
                                                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                <span class="text-[8px] font-bold uppercase mt-1">Sin Imagen</span>
                                            </div>
                                        @endif
                                        <span class="text-[9px] font-mono text-slate-400 mt-1">{{ $err->created_at->format('d/m/Y H:i') }}</span>
                                    </div>

                                    {{-- Right Side: Details --}}
                                    <div class="flex-1 min-w-0 pr-6">
                                        {{-- Operario Message --}}
                                        <div class="mb-3">
                                            <span class="text-[9px] font-black tracking-widest text-slate-400 uppercase font-mono">Mensaje del Operario:</span>
                                            <p class="text-xs font-bold text-slate-800 leading-normal mt-0.5 bg-slate-50 border border-slate-100 rounded-xl px-3 py-2 shadow-sm italic">
                                                "{{ $err->user_message }}"
                                            </p>
                                        </div>
                                        
                                        {{-- AI Solución --}}
                                        <div>
                                            <span class="text-[9px] font-black tracking-widest text-cyan-600 uppercase font-mono">Propuesta de Solución (IA):</span>
                                            <div class="text-xs text-slate-700 leading-relaxed mt-1 bg-white border border-slate-200 rounded-xl px-4 py-3 shadow-sm whitespace-pre-wrap prose prose-xs">
                                                {!! nl2br(e($err->ai_response)) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Alpine.js scroll assistant -->
    <script>
        function scrollToBottom(id) {
            const el = document.getElementById(id);
            if (el) {
                el.scrollTop = el.scrollHeight;
            }
        }
    </script>
</div>
