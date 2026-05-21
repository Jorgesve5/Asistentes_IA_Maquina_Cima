<div class="flex-1 flex flex-col max-w-[1400px] mx-auto w-full px-4 sm:px-6 py-6" x-data="{ init() { $nextTick(() => { this.scrollToBottom('chatbot-box'); this.scrollToBottom('supervisor-box'); }) } }">
    <!-- Back button -->
    <div class="mb-6 flex items-center justify-between">
        <a
            href="/"
            onclick="window.playAudio('click');"
            class="flex items-center gap-2 text-xs font-bold text-slate-500 hover:text-cyan-400 uppercase tracking-widest transition-colors group"
        >
            <svg class="h-4 w-4 group-hover:-translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
            <span>Volver a Planta</span>
        </a>
        <div class="flex items-center gap-1.5 font-mono text-[10px] text-slate-600">
            <span>UNIDAD: {{ strtoupper($machine->id) }}</span>
        </div>
    </div>

    <!-- ── Cuidado Warning Banner ── -->
    @if($machine->status !== 'online' && !$alertDismissed)
        <div class="mb-6 bg-red-950/20 border border-red-500/40 rounded-2xl p-4 flex items-start justify-between gap-4 shadow-lg animate-pulse">
            <div class="flex items-start gap-3">
                <div class="p-2 bg-red-500/10 rounded-xl text-red-400">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div>
                    <h4 class="text-xs font-black text-white uppercase tracking-wider">Cuidado</h4>
                    <p class="text-[11px] text-red-300/90 leading-relaxed mt-0.5">
                        ESTA MÁQUINA ESTÁ EN {{ strtoupper($machine->status === 'warning' ? 'Avería' : ($machine->status === 'maintenance' ? 'Mantenimiento' : 'Espera')) }}. TRABAJE CON EXTREMA PRECAUCIÓN.
                    </p>
                </div>
            </div>
            <button
                wire:click="dismissAlert"
                class="text-slate-500 hover:text-white transition-colors"
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
            <div class="bg-[#0a0e17]/80 border border-white/5 rounded-3xl p-5 shadow-2xl backdrop-blur-md">
                <div class="flex items-center justify-between border-b border-white/5 pb-4 mb-4">
                    <h2 class="text-sm font-black text-white tracking-widest uppercase font-outfit">Ficha Técnica</h2>
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
                    <span class="px-2.5 py-0.5 rounded-full text-[9px] font-bold border {{ $badgeClasses[$machine->status] ?? $badgeClasses['online'] }}">
                        {{ $statusNames[$machine->status] ?? 'Desconocido' }}
                    </span>
                </div>
                
                <h1 class="text-xl font-black text-white tracking-wide uppercase leading-tight font-outfit">
                    {{ $machine->name }}
                </h1>
                

            </div>

            <!-- Registrar Incidencia Card -->
            <div class="bg-[#0a0e17]/80 border border-white/5 rounded-3xl p-5 shadow-2xl backdrop-blur-md">
                <h2 class="text-sm font-black text-white tracking-widest uppercase mb-4 font-outfit">Reportar Incidencia</h2>
                
                @if(session()->has('incidence_success'))
                    <div class="mb-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-[10px] font-mono px-3 py-2 rounded-xl">
                        {{ session('incidence_success') }}
                    </div>
                @endif

                <form wire:submit.prevent="registerIncidence" class="space-y-4 font-mono text-[10px]">
                    <div>
                        <label class="block text-[9px] font-bold text-slate-500 uppercase tracking-widest mb-1.5">Nuevo Estado</label>
                        <select
                            wire:model.live="incidenceStatus"
                            class="w-full bg-white/3 border border-white/10 rounded-xl px-3.5 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-cyan-500/50 transition-colors"
                        >
                            <option value="online" class="bg-[#0f172a]">Operativa (Disponible)</option>
                            <option value="warning" class="bg-[#0f172a]">Avería</option>
                            <option value="maintenance" class="bg-[#0f172a]">Mantenimiento</option>
                            <option value="waiting" class="bg-[#0f172a]">En Espera</option>
                        </select>
                    </div>

                    @if($incidenceStatus !== 'online')
                        <div>
                            <label class="block text-[9px] font-bold text-slate-500 uppercase tracking-widest mb-1.5">Detalles del Motivo / Avería</label>
                            <textarea
                                wire:model="incidenceReason"
                                required
                                rows="3"
                                placeholder="Ej: Fuga de presión en cilindro neumático principal..."
                                class="w-full bg-white/3 border border-white/10 rounded-xl p-3 text-[11px] text-white placeholder-slate-600 focus:outline-none focus:border-cyan-500/50 transition-colors leading-relaxed"
                            ></textarea>
                        </div>
                    @endif

                    <button
                        type="submit"
                        onclick="window.playAudio('success');"
                        class="w-full py-2.5 bg-cyan-500 hover:bg-cyan-400 text-slate-950 text-[10px] font-black uppercase tracking-wider rounded-xl shadow-lg active:scale-95 transition-all"
                    >
                        Registrar Estado
                    </button>
                </form>
            </div>
        </div>

        <!-- Column 2: Chatbot Console (5 cols) -->
        <div class="lg:col-span-5 flex flex-col bg-[#0a0e17]/80 border border-white/5 rounded-3xl h-[560px] shadow-2xl backdrop-blur-md overflow-hidden relative">
            <!-- Console Header -->
            <div class="px-5 py-4 border-b border-white/5 flex items-center justify-between bg-[#0a0e17]/90">
                <div class="flex items-center gap-2">
                    <span class="h-2 w-2 rounded-full bg-cyan-400 animate-pulse"></span>
                    <span class="text-xs font-black text-white tracking-wider uppercase font-outfit">Asistente IA (RAG)</span>
                </div>
                <span class="text-[8px] font-mono text-cyan-400/80 bg-cyan-500/10 px-2 py-0.5 border border-cyan-500/20 rounded-full uppercase tracking-widest">
                    Asistente Activo
                </span>
            </div>

            <!-- Messages Logs -->
            <div
                id="chatbot-box"
                class="flex-1 overflow-y-auto p-5 space-y-4 font-sans text-xs scroll-smooth"
                x-init="$watch('$wire.chatMessages', () => { setTimeout(() => { $el.scrollTop = $el.scrollHeight }, 100) })"
            >
                @foreach($chatMessages as $msg)
                    <div class="flex {{ $msg['sender'] === 'user' ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-[85%] rounded-[18px] px-4 py-3 border {{ $msg['sender'] === 'user' ? 'bg-cyan-500/5 border-cyan-500/25 text-white' : 'bg-white/3 border-white/5 text-slate-200' }}">
                            @if($msg['sender'] === 'bot')
                                <!-- Bot Icon -->
                                <div class="flex items-center gap-1.5 mb-1.5 border-b border-white/4 pb-1 text-[9px] font-black tracking-widest text-cyan-400 uppercase">
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    <span>ASISTENTE VIRTUAL</span>
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

                <!-- Thinking Spinner Animation -->
                @if($isThinking)
                    <div class="flex justify-start" wire:init="getBotResponse">
                        <div class="bg-white/3 border border-white/5 rounded-[18px] px-4 py-3 text-slate-400 max-w-[80%]">
                            <div class="flex items-center gap-2">
                                <div class="flex gap-1">
                                    <span class="h-1.5 w-1.5 rounded-full bg-cyan-400 animate-bounce"></span>
                                    <span class="h-1.5 w-1.5 rounded-full bg-cyan-400 animate-bounce [animation-delay:0.2s]"></span>
                                    <span class="h-1.5 w-1.5 rounded-full bg-cyan-400 animate-bounce [animation-delay:0.4s]"></span>
                                </div>
                                <span class="text-[9px] font-mono text-slate-500 uppercase tracking-widest">Analizando manuales...</span>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Console Inputs -->
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
                        placeholder="Pregúntame o sube una imagen del fallo..."
                        class="flex-1 bg-white/3 border border-white/10 rounded-xl px-4 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-cyan-500/50 transition-colors"
                    />
                    <button
                        type="submit"
                        class="p-2.5 bg-gradient-to-r from-cyan-500 to-blue-500 hover:from-cyan-400 hover:to-blue-400 text-white rounded-xl shadow-lg shadow-cyan-500/10 hover:shadow-cyan-500/20 active:scale-95 transition-all flex items-center justify-center"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </button>
                </div>
            </form>
        </div>

        <!-- Column 3: Supervisor Chat Console (3 cols) -->
        <div class="lg:col-span-3 flex flex-col bg-[#0a0e17]/80 border border-white/5 rounded-3xl h-[560px] shadow-2xl backdrop-blur-md overflow-hidden relative">
            <!-- Chat Header -->
            <div class="px-5 py-4 border-b border-white/5 bg-[#0a0e17]/90 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg class="h-4 w-4 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" />
                    </svg>
                    <span class="text-xs font-black text-white tracking-wider uppercase font-outfit">Canal Supervisor</span>
                </div>
            </div>

            <!-- Messages Log -->
            <div
                id="supervisor-box"
                class="flex-1 overflow-y-auto p-4 space-y-3 text-xs scroll-smooth"
                x-init="$watch('$wire.supervisorMessages', () => { setTimeout(() => { $el.scrollTop = $el.scrollHeight }, 100) })"
            >
                @if($supervisorMessages->isEmpty())
                    <div class="py-12 text-center">
                        <svg class="h-6 w-6 text-slate-700 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        <p class="text-[10px] font-mono text-slate-600">Canal vacío. Escribe para dar aviso.</p>
                    </div>
                @else
                    @foreach($supervisorMessages as $msg)
                        <div class="group relative flex flex-col gap-1 rounded-2xl p-3 border {{ $msg->from === 'admin' ? 'bg-yellow-500/5 border-yellow-500/20' : 'bg-white/3 border-white/5' }}">
                            
                            <!-- Delete message on hover (restricted to admins) -->
                            @auth
                                <button
                                    style="cursor: pointer;"
                                    wire:click="deleteSupervisorMessage('{{ $msg->id }}')"
                                    onclick="window.playAudio('click');"
                                    class="absolute top-2 right-2 text-slate-500 hover:text-red-400 opacity-0 group-hover:opacity-100 transition-all"
                                >
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            @endauth

                            <div class="flex items-center justify-between border-b border-white/4 pb-1">
                                <span class="text-[9px] font-black uppercase tracking-wider {{ $msg->from === 'admin' ? 'text-yellow-400' : 'text-slate-400' }}">
                                    {{ $msg->senderName }}
                                </span>
                                <span class="text-[8px] font-mono text-slate-600">{{ $msg->timestamp }}</span>
                            </div>
                            <p class="text-[11px] leading-relaxed text-slate-200 mt-1 whitespace-pre-wrap">{{ $msg->text }}</p>
                        </div>
                    @endforeach
                @endif
            </div>

            <!-- Chat Inputs -->
            <form wire:submit.prevent="sendSupervisorMessage" class="p-3 border-t border-white/5 bg-[#0a0e17]/95 flex items-center gap-2">
                <input
                    type="text"
                    wire:model="supervisorInput"
                    placeholder="Escribe aviso..."
                    class="flex-1 bg-white/3 border border-white/10 rounded-xl px-3 py-2 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-yellow-500/40 transition-colors"
                />
                <button
                    type="submit"
                    class="p-2 bg-yellow-500 hover:bg-yellow-400 text-slate-950 font-bold rounded-xl shadow-lg active:scale-95 transition-all flex items-center justify-center"
                >
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </button>
            </form>
        </div>

    </div>

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
