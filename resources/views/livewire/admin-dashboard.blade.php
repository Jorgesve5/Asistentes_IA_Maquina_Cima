<div class="flex-1 flex flex-col max-w-[1400px] mx-auto w-full px-4 sm:px-6 py-6" x-data="{ init() { $nextTick(() => { const cb = document.getElementById('chatbot-box'); if(cb) cb.scrollTop = cb.scrollHeight; }) } }">

    <!-- Header bar -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-slate-200 pb-5 mb-6">
        <div>
            <div class="flex items-center gap-2">
                <span class="h-2 w-2 rounded-full bg-cyan-500 animate-pulse"></span>
                <span class="text-xs font-black text-slate-500 tracking-wider uppercase font-mono">Consola Supervisor</span>
            </div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight uppercase mt-1 font-outfit">Control de Planta</h1>
        </div>

        <div class="flex items-center gap-3">
            <!-- Logout -->
            <button
                wire:click="logout"
                onclick="window.playAudio('click');"
                class="px-4 py-2 text-xs font-bold bg-red-55 bg-red-50 border border-red-200 hover:bg-red-100 text-red-600 rounded-xl transition-all"
            >
                Cerrar Sesión
            </button>
        </div>
    </div>

    <!-- Tabs switchers -->
    <div class="flex flex-wrap gap-1.5 mb-8 bg-slate-100 border border-slate-200/80 rounded-2xl p-1.5 w-fit shadow-sm">
        <button
            wire:click="setTab('status')"
            onclick="window.playAudio('click');"
            class="px-5 py-2.5 rounded-xl text-xs font-black uppercase tracking-wider transition-all duration-200 {{ $activeTab === 'status' ? 'bg-cyan-500 text-slate-950 shadow-[0_4px_12px_rgba(6,182,212,0.15)]' : 'text-slate-500 hover:text-slate-800' }}"
        >
            Estado de Máquinas
        </button>
        <button
            wire:click="setTab('manuals')"
            onclick="window.playAudio('click');"
            class="px-5 py-2.5 rounded-xl text-xs font-black uppercase tracking-wider transition-all duration-200 {{ $activeTab === 'manuals' ? 'bg-cyan-500 text-slate-950 shadow-[0_4px_12px_rgba(6,182,212,0.15)]' : 'text-slate-500 hover:text-slate-800' }}"
        >
            Gestión de Recursos
        </button>
        <button
            wire:click="setTab('chatbot')"
            onclick="window.playAudio('click');"
            class="px-5 py-2.5 rounded-xl text-xs font-black uppercase tracking-wider transition-all duration-200 {{ $activeTab === 'chatbot' ? 'bg-cyan-500 text-slate-950 shadow-[0_4px_12px_rgba(6,182,212,0.15)]' : 'text-slate-500 hover:text-slate-800' }}"
        >
            Prueba / Config IA
        </button>
        <button
            wire:click="setTab('messages')"
            onclick="window.playAudio('click');"
            class="px-5 py-2.5 rounded-xl text-xs font-black uppercase tracking-wider transition-all duration-200 {{ $activeTab === 'messages' ? 'bg-cyan-500 text-slate-950 shadow-[0_4px_12px_rgba(6,182,212,0.15)]' : 'text-slate-500 hover:text-slate-800' }}"
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
                    <div class="bg-white border border-slate-200 rounded-3xl p-5 flex flex-col justify-between gap-4 shadow-sm">
                        <div>
                            <span class="text-[9px] font-mono text-slate-400 tracking-wider block mb-0.5">{{ $m->serial ?: 'SIN SERIAL' }}</span>
                            <h3 class="font-black text-base text-slate-900 tracking-wide uppercase leading-tight font-outfit">{{ $m->name }}</h3>
                            
                            <div class="mt-2.5 px-3 py-1.5 rounded-xl border text-[11px] font-bold flex items-center gap-2 {{ $badgeClasses[$m->status] }}">
                                <span class="h-1.5 w-1.5 rounded-full bg-current animate-pulse"></span>
                                {{ $statusNames[$m->status] }}
                            </div>
                            
                            @if($m->status !== 'online' && $m->subLabel)
                                <p class="text-[10px] font-mono text-slate-600 mt-2 bg-slate-50 border border-slate-200 rounded-lg px-2.5 py-1.5 leading-snug">
                                    {{ $m->subLabel }}
                                </p>
                            @endif
                        </div>

                        <div class="flex flex-col gap-1.5 mt-3 pt-3 border-t border-slate-100">
                            <button
                                wire:click="initiateStatusChange('{{ $m->id }}', 'online')"
                                onclick="window.playAudio('click');"
                                class="w-full flex items-center justify-between px-3 py-2 rounded-xl border text-[11px] font-bold transition-all duration-200
                                  {{ $m->status === 'online' ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : 'border-slate-200 text-slate-500 hover:border-slate-300 hover:text-slate-800' }}"
                            >
                                <span>Disponible</span>
                                @if($m->status === 'online') <span class="h-1.5 w-1.5 bg-emerald-500 rounded-full"></span> @endif
                            </button>
                            <button
                                wire:click="initiateStatusChange('{{ $m->id }}', 'maintenance')"
                                onclick="window.playAudio('click');"
                                class="w-full flex items-center justify-between px-3 py-2 rounded-xl border text-[11px] font-bold transition-all duration-200
                                  {{ $m->status === 'maintenance' ? 'bg-orange-50 border-orange-200 text-orange-700' : 'border-slate-200 text-slate-500 hover:border-slate-300 hover:text-slate-800' }}"
                            >
                                <span>Mantenimiento</span>
                                @if($m->status === 'maintenance') <span class="h-1.5 w-1.5 bg-orange-500 rounded-full"></span> @endif
                            </button>
                            <button
                                wire:click="initiateStatusChange('{{ $m->id }}', 'waiting')"
                                onclick="window.playAudio('click');"
                                class="w-full flex items-center justify-between px-3 py-2 rounded-xl border text-[11px] font-bold transition-all duration-200
                                  {{ $m->status === 'waiting' ? 'bg-amber-50 border-amber-200 text-amber-700' : 'border-slate-200 text-slate-500 hover:border-slate-300 hover:text-slate-800' }}"
                            >
                                <span>En Espera</span>
                                @if($m->status === 'waiting') <span class="h-1.5 w-1.5 bg-amber-500 rounded-full"></span> @endif
                            </button>
                            <button
                                wire:click="initiateStatusChange('{{ $m->id }}', 'warning')"
                                onclick="window.playAudio('click');"
                                class="w-full flex items-center justify-between px-3 py-2 rounded-xl border text-[11px] font-bold transition-all duration-200
                                  {{ $m->status === 'warning' ? 'bg-rose-50 border-rose-200 text-rose-700' : 'border-slate-200 text-slate-500 hover:border-slate-300 hover:text-rose-600' }}"
                            >
                                <span>Avería</span>
                                @if($m->status === 'warning') <span class="h-1.5 w-1.5 bg-rose-500 rounded-full"></span> @endif
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- ── TAB: Manuals (Resources Management) ── -->
    @if($activeTab === 'manuals')
        <div>
            <!-- Global Upload Settings Card -->
            <div class="bg-white border border-slate-200 rounded-3xl p-5 mb-6 shadow-sm">
                <h3 class="text-xs font-black text-slate-800 uppercase tracking-widest mb-3 flex items-center gap-2">
                    <svg class="h-4 w-4 text-cyan-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    </svg>
                    Ajustes para Nuevas Subidas
                </h3>
                <div class="flex items-center gap-3 text-xs">
                    <label class="relative flex items-center cursor-pointer select-none">
                        <input 
                            type="checkbox" 
                            wire:model="uploadInChat" 
                            class="sr-only peer"
                        />
                        <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-cyan-600"></div>
                        <span class="ml-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider font-mono">Habilitar en Chat (RAG) por defecto</span>
                    </label>
                </div>
            </div>

            <p class="text-xs text-slate-400 mb-6 tracking-wide uppercase font-mono">
                Sube recursos técnicos (.pdf, .docx, .xlsx, imágenes) por máquina. Modifica su visibilidad o categoría inline.
            </p>

            @if(session()->has('upload_success'))
                <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs px-4 py-3 rounded-2xl">
                    {{ session('upload_success') }}
                </div>
            @endif
            @if(session()->has('upload_error'))
                <div class="mb-4 bg-rose-50 border border-rose-200 text-rose-800 text-xs px-4 py-3 rounded-2xl">
                    {{ session('upload_error') }}
                </div>
            @endif

            <div class="grid grid-cols-1 gap-6">
                @foreach($machines as $m)
                    <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm flex flex-col gap-5">
                        
                        <!-- Top header card -->
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pb-4 border-b border-slate-100">
                            <div>
                                <span class="text-[9px] font-mono text-slate-400 tracking-wider block mb-0.5">{{ $m->serial ?: 'SIN SERIAL' }}</span>
                                <h3 class="font-black text-sm text-slate-800 tracking-wide uppercase font-outfit">{{ $m->name }}</h3>
                            </div>
                            
                            <!-- Upload trigger button -->
                            <div class="flex items-center gap-3">
                                <label
                                    onclick="window.playAudio('click'); @this.set('uploadingMachineId', '{{ $m->id }}')"
                                    class="flex items-center gap-2 bg-slate-50 hover:bg-cyan-50 border border-slate-200 hover:border-cyan-500/50 text-slate-700 hover:text-cyan-700 px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-wider cursor-pointer transition-all duration-200 shadow-sm whitespace-nowrap"
                                >
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                    </svg>
                                    Subir Archivos
                                    <input
                                        type="file"
                                        wire:model="uploadedFiles"
                                        multiple
                                        accept=".pdf,.docx,.xlsx,.png,.jpg,.jpeg"
                                        class="hidden"
                                    />
                                </label>
                                
                                @if($uploadingMachineId === $m->id)
                                    <div wire:loading wire:target="uploadedFiles" class="text-[10px] text-cyan-600 font-mono animate-pulse">
                                        Procesando recurso RAG...
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Resources Table/List -->
                        <div class="overflow-x-auto">
                            @if($m->manuals->isEmpty())
                                <span class="text-[10px] font-mono text-slate-400 block py-2">Sin recursos técnicos cargados para esta máquina.</span>
                            @else
                                <table class="w-full text-[11px] font-mono text-slate-700">
                                    <thead>
                                        <tr class="text-left text-slate-500 border-b border-slate-100 pb-2 text-[9px] uppercase tracking-wider">
                                            <th class="pb-2 font-bold">Tipo</th>
                                            <th class="pb-2 font-bold">Nombre de archivo</th>

                                            <th class="pb-2 font-bold text-center">Chat IA (RAG)</th>
                                            <th class="pb-2 font-bold text-right">Tamaño</th>
                                            <th class="pb-2 font-bold text-right">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @foreach($m->manuals as $man)
                                            @php
                                                $typeClasses = [
                                                    'pdf' => 'bg-red-50 text-red-700 border-red-100',
                                                    'image' => 'bg-purple-50 text-purple-700 border-purple-100',
                                                    'excel' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                                    'word' => 'bg-blue-50 text-blue-700 border-blue-100',
                                                    'other' => 'bg-slate-50 text-slate-700 border-slate-100',
                                                ];
                                                $type = $man->file_type ?? 'pdf';
                                                $class = $typeClasses[$type] ?? $typeClasses['other'];
                                            @endphp
                                            <tr class="hover:bg-slate-50/50 transition-colors">
                                                <td class="py-2.5">
                                                    <span class="px-2 py-0.5 rounded border text-[9px] font-bold uppercase {{ $class }}">
                                                        {{ strtoupper($type) }}
                                                    </span>
                                                </td>
                                                <td class="py-2.5 font-bold max-w-[200px] truncate pr-2 text-slate-800" title="{{ $man->fileName }}">
                                                    {{ $man->fileName }}
                                                </td>

                                                <td class="py-2.5 text-center">
                                                    <button
                                                        wire:click="toggleInChat('{{ $man->id }}')"
                                                        onclick="window.playAudio('click');"
                                                        class="px-2.5 py-1 rounded-xl text-[9px] font-black uppercase tracking-wider transition-all border
                                                          {{ $man->in_chat ? 'bg-emerald-50 border-emerald-100 text-emerald-700' : 'bg-slate-50 border-slate-200 text-slate-500 hover:text-slate-750 hover:text-slate-700' }}"
                                                    >
                                                        {{ $man->in_chat ? 'Activo' : 'Inactivo' }}
                                                    </button>
                                                </td>
                                                <td class="py-2.5 text-right pr-2 text-slate-500 font-mono">
                                                    {{ number_format($man->size / 1024, 1) }} KB
                                                </td>
                                                <td class="py-2.5 text-right">
                                                    <button
                                                        wire:click="deleteManual('{{ $man->id }}')"
                                                        onclick="window.playAudio('click');"
                                                        class="h-6 w-6 flex items-center justify-center bg-red-50 hover:bg-red-500 border border-red-200 hover:border-red-500 text-red-600 hover:text-white rounded transition-all ml-auto"
                                                        title="Eliminar recurso"
                                                    >
                                                        ✕
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
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
            <div class="lg:col-span-5 bg-white border border-slate-200 rounded-3xl p-5 shadow-sm">
                <h2 class="text-sm font-black text-slate-800 tracking-widest uppercase mb-4 font-outfit">Configuración de Prompt IA</h2>
                
                @if(session()->has('prompt_success'))
                    <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs px-3 py-2 rounded-xl">
                        {{ session('prompt_success') }}
                    </div>
                @endif

                <div class="space-y-4">
                    <div>
                        <label class="block text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Unidad a configurar</label>
                        <select
                            wire:model.live="selectedMachineForChat"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-cyan-500/50 transition-colors"
                        >
                            @foreach($machines as $m)
                                <option value="{{ $m->id }}">{{ $m->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <form wire:submit.prevent="saveCustomPrompt" class="space-y-4">
                        <div>
                            <label class="block text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Instrucciones del Sistema (Prompt)</label>
                            <textarea
                                wire:model="customPrompt"
                                rows="8"
                                placeholder="Escribe aquí las directrices específicas de la IA para esta máquina..."
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3.5 font-mono text-[10px] text-slate-800 placeholder-slate-400 focus:outline-none focus:border-cyan-500/50 transition-colors leading-relaxed"
                            ></textarea>
                        </div>

                        <button
                            type="submit"
                            onclick="window.playAudio('success');"
                            class="w-full py-2.5 bg-cyan-500 hover:bg-cyan-400 text-slate-950 text-xs font-black uppercase tracking-wider rounded-xl shadow-md transition-all active:scale-95"
                        >
                            Guardar Instrucciones
                        </button>
                    </form>

                    {{-- Restablecer IA Section --}}
                    <div class="mt-5 pt-5 border-t border-slate-100">
                        <label class="block text-[9px] font-bold text-rose-400 uppercase tracking-widest mb-2">Restablecer Memoria de IA</label>
                        <p class="text-[10px] text-slate-500 leading-relaxed mb-3">
                            Borra todo el contexto de conversación de la IA para esta máquina. Todos los operarios verán un chat limpio en su próxima visita.
                        </p>

                        @if(session()->has('ia_reset_success'))
                            <div class="mb-3 bg-emerald-50 border border-emerald-200 text-emerald-800 text-[10px] px-3 py-2 rounded-xl animate-pulse">
                                {{ session('ia_reset_success') }}
                            </div>
                        @endif

                        <button
                            wire:click="resetMachineIA('{{ $selectedMachineForChat }}')"
                            wire:confirm="¿Seguro que deseas restablecer la memoria de la IA para esta máquina? Se borrarán todas las conversaciones de todos los usuarios."
                            onclick="window.playAudio('click');"
                            class="w-full flex items-center justify-center gap-2 py-2.5 bg-rose-50 hover:bg-rose-100 border border-rose-200 hover:border-rose-300 text-rose-600 hover:text-rose-700 text-xs font-black uppercase tracking-wider rounded-xl transition-all active:scale-95 shadow-sm"
                        >
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Restablecer IA
                        </button>
                    </div>
                </div>
            </div>

            <!-- Chatbot Testing Column (7 cols) -->
            <div class="lg:col-span-7 flex flex-col bg-white border border-slate-200 rounded-3xl h-[560px] shadow-sm overflow-hidden relative">
                
                <div class="px-5 py-4 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span class="text-xs font-black text-slate-800 tracking-wider uppercase font-outfit">Consola de Prueba IA</span>
                    </div>
                </div>

                <!-- Message logs -->
                <div
                    id="chatbot-box"
                    class="flex-1 overflow-y-auto p-5 space-y-4 font-sans text-xs scroll-smooth bg-slate-50/30"
                    x-init="$watch('$wire.chatMessages', () => { setTimeout(() => { $el.scrollTop = $el.scrollHeight }, 100) })"
                >
                    @foreach($chatMessages as $msg)
                        <div class="flex {{ $msg['sender'] === 'user' ? 'justify-end' : 'justify-start' }}">
                            <div class="max-w-[85%] rounded-[18px] px-4 py-3 border {{ $msg['sender'] === 'user' ? 'bg-cyan-50/80 border-cyan-100 text-slate-800 shadow-sm' : 'bg-white border-slate-200/80 text-slate-700 shadow-sm' }}">
                                @if($msg['sender'] === 'bot')
                                    <div class="flex items-center gap-1.5 mb-1.5 border-b border-slate-100 pb-1 text-[9px] font-black tracking-widest text-cyan-600 uppercase">
                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                        <span>ASISTENTE MOCK ADMIN</span>
                                    </div>
                                @endif
                                @if(!empty($msg['image_url']))
                                    <div class="mb-2">
                                        <img src="{{ $msg['image_url'] }}" class="max-h-48 rounded-lg border border-slate-200 object-contain cursor-pointer" onclick="window.open('{{ $msg['image_url'] }}', '_blank')" />
                                    </div>
                                @endif
                                @if(!empty($msg['text']))
                                    <div class="leading-relaxed whitespace-pre-line prose prose-xs text-slate-705 text-slate-700">
                                        {!! nl2br(e($msg['text'])) !!}
                                    </div>
                                @endif
                                <span class="block text-[8px] font-mono text-slate-400 text-right mt-1.5">{{ $msg['timestamp'] }}</span>
                            </div>
                        </div>
                    @endforeach

                    @if($isThinking)
                        <div class="flex justify-start" wire:init="getBotResponse">
                            <div class="bg-white border border-slate-200 rounded-[18px] px-4 py-3 text-slate-500 max-w-[80%] shadow-sm">
                                <div class="flex items-center gap-2">
                                    <div class="flex gap-1">
                                        <span class="h-1.5 w-1.5 rounded-full bg-cyan-500 animate-bounce"></span>
                                        <span class="h-1.5 w-1.5 rounded-full bg-cyan-500 animate-bounce [animation-delay:0.2s]"></span>
                                        <span class="h-1.5 w-1.5 rounded-full bg-cyan-500 animate-bounce [animation-delay:0.4s]"></span>
                                    </div>
                                    <span class="text-[9px] font-mono text-slate-400 uppercase tracking-widest">Generando respuesta RAG...</span>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Inputs -->
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

                    <div class="flex items-center gap-3">
                        <!-- Image Upload Button -->
                        <label class="cursor-pointer p-2.5 bg-white hover:bg-slate-100 text-slate-500 hover:text-slate-800 rounded-xl border border-slate-200 transition-colors flex items-center justify-center relative shadow-sm">
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
                                <svg class="animate-spin h-4 w-4 text-cyan-550 text-cyan-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                        </label>

                        <input
                            type="text"
                            wire:model="userInput"
                            placeholder="Prueba una pregunta técnica o sube una imagen..."
                            class="flex-1 bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-cyan-500/50 transition-colors shadow-sm"
                        />
                        <button
                            type="submit"
                            class="p-2.5 bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-500 hover:to-blue-500 text-white rounded-xl active:scale-95 transition-all flex items-center justify-center shadow-md"
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
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 h-[600px]">
            <!-- Left panel: Machines List (col-span-4) -->
            <div class="lg:col-span-4 bg-white border border-slate-200 rounded-3xl flex flex-col overflow-hidden shadow-sm h-full">
                <!-- Panel Header -->
                <div class="px-5 py-4 border-b border-slate-200 bg-slate-50 flex items-center justify-between flex-shrink-0">
                    <h3 class="text-xs font-black text-slate-800 tracking-wider uppercase font-outfit">Canales de Máquinas</h3>
                </div>
                
                <!-- Machines List Body -->
                <div class="flex-1 overflow-y-auto divide-y divide-slate-100">
                    @foreach($machines as $mac)
                        @php
                            // Get the last message for this machine
                            $lastMsg = $messages->where('machine_id', $mac->id)->first();
                            // Count unread operator messages for this machine
                            $unreadCount = $messages->where('machine_id', $mac->id)
                                                    ->where('from', 'operator')
                                                    ->where('read', false)
                                                    ->count();
                            $isActive = $selectedMachineIdForMessages === $mac->id;
                            
                            $statusColors = [
                                'online' => 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]',
                                'maintenance' => 'bg-orange-500 shadow-[0_0_8px_rgba(249,115,22,0.5)]',
                                'waiting' => 'bg-amber-500 shadow-[0_0_8px_rgba(245,158,11,0.5)]',
                                'warning' => 'bg-red-500 shadow-[0_0_8px_rgba(239,68,68,0.5)]',
                            ];
                        @endphp
                        <button
                            wire:click="selectMachineForMessages('{{ $mac->id }}')"
                            onclick="window.playAudio('click');"
                            class="w-full text-left p-4 flex items-start gap-3 transition-all duration-200 hover:bg-slate-50 {{ $isActive ? 'bg-gradient-to-r from-cyan-50/70 to-blue-50/40 border-r-4 border-cyan-500' : '' }}"
                        >
                            <!-- Machine Status Indicator -->
                            <div class="relative shrink-0 mt-1">
                                <span class="h-2.5 w-2.5 rounded-full block animate-pulse {{ $statusColors[$mac->status] ?? 'bg-slate-400' }}"></span>
                                @if($unreadCount > 0)
                                    <span class="absolute -top-1.5 -right-1.5 h-2 w-2 rounded-full bg-red-600 animate-ping"></span>
                                @endif
                            </div>
                            
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-1.5">
                                    <h4 class="text-xs font-black text-slate-800 truncate uppercase font-outfit">{{ $mac->name }}</h4>
                                    @if($lastMsg)
                                        <span class="text-[9px] font-mono text-slate-400 shrink-0">{{ $lastMsg->timestamp }}</span>
                                    @endif
                                </div>
                                <p class="text-[10px] font-mono text-slate-500 truncate mt-0.5 uppercase">{{ $mac->serial ?: 'SIN SERIAL' }}</p>
                                
                                @if($lastMsg)
                                    <p class="text-[11px] text-slate-650 truncate mt-1.5 leading-snug {{ $unreadCount > 0 ? 'font-bold text-slate-900' : '' }}">
                                        {{ $lastMsg->from === 'admin' ? 'Tú: ' : ($lastMsg->from === 'system' ? '⚙️ ' : '') }}{{ $lastMsg->text }}
                                    </p>
                                @else
                                    <p class="text-[10px] italic text-slate-400 mt-1.5">Sin avisos en este canal</p>
                                @endif
                            </div>
                            
                            @if($unreadCount > 0)
                                <span class="shrink-0 bg-red-100 text-red-600 font-bold font-mono text-[9px] px-2 py-0.5 rounded-full">
                                    {{ $unreadCount }}
                                </span>
                            @endif
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Right panel: Active Chat (col-span-8) -->
            <div class="lg:col-span-8 bg-white border border-slate-200 rounded-3xl flex flex-col overflow-hidden shadow-sm h-full relative">
                @if($selectedMachineIdForMessages)
                    @php
                        $activeMachine = $machines->firstWhere('id', $selectedMachineIdForMessages);
                        $machineMessages = $messages->where('machine_id', $selectedMachineIdForMessages)->reverse();
                        
                        $statusNames = [
                            'online' => 'Disponible',
                            'maintenance' => 'Mantenimiento',
                            'waiting' => 'En Espera',
                            'warning' => 'Avería',
                        ];
                        
                        $statusBadge = [
                            'online' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                            'maintenance' => 'bg-orange-50 text-orange-700 border-orange-100',
                            'waiting' => 'bg-amber-50 text-amber-700 border-amber-100',
                            'warning' => 'bg-red-50 text-red-700 border-red-100',
                        ];
                    @endphp
                    
                    <!-- Chat Header -->
                    <div class="px-5 py-3 border-b border-slate-200 bg-slate-50 flex flex-col sm:flex-row sm:items-center justify-between gap-3 flex-shrink-0">
                        <div class="flex items-center gap-3">
                            <div>
                                <h3 class="text-xs font-black text-slate-800 uppercase tracking-wider font-outfit">{{ $activeMachine->name }}</h3>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="text-[9px] font-mono text-slate-400 uppercase">{{ $activeMachine->serial ?: 'SIN SERIAL' }}</span>
                                    <span class="px-1.5 py-0.5 rounded border text-[8px] font-bold {{ $statusBadge[$activeMachine->status] }}">
                                        {{ $statusNames[$activeMachine->status] }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Integrated Status Controls directly in Chat -->
                        <div class="flex items-center flex-wrap gap-1 bg-slate-200/60 p-1 rounded-xl border border-slate-300/40">
                            <button
                                wire:click="changeMachineStatusFromChat('online')"
                                onclick="window.playAudio('click');"
                                class="px-2.5 py-1.5 rounded-lg text-[9px] font-black uppercase tracking-wider transition-all flex items-center gap-1
                                  {{ $activeMachine->status === 'online' ? 'bg-emerald-500 text-white shadow-md' : 'text-slate-600 hover:bg-slate-100' }}"
                                title="Marcar como Disponible"
                            >
                                <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                Disp
                            </button>
                            <button
                                wire:click="changeMachineStatusFromChat('warning')"
                                onclick="window.playAudio('click');"
                                class="px-2.5 py-1.5 rounded-lg text-[9px] font-black uppercase tracking-wider transition-all flex items-center gap-1
                                  {{ $activeMachine->status === 'warning' ? 'bg-rose-500 text-white shadow-md' : 'text-slate-600 hover:bg-slate-100' }}"
                                title="Reportar Avería"
                            >
                                <span class="h-1.5 w-1.5 rounded-full bg-current animate-pulse"></span>
                                Avería
                            </button>
                            <button
                                wire:click="changeMachineStatusFromChat('maintenance')"
                                onclick="window.playAudio('click');"
                                class="px-2.5 py-1.5 rounded-lg text-[9px] font-black uppercase tracking-wider transition-all flex items-center gap-1
                                  {{ $activeMachine->status === 'maintenance' ? 'bg-orange-500 text-white shadow-md' : 'text-slate-600 hover:bg-slate-100' }}"
                                title="Marcar en Mantenimiento"
                            >
                                <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                Mant
                            </button>
                            <button
                                wire:click="changeMachineStatusFromChat('waiting')"
                                onclick="window.playAudio('click');"
                                class="px-2.5 py-1.5 rounded-lg text-[9px] font-black uppercase tracking-wider transition-all flex items-center gap-1
                                  {{ $activeMachine->status === 'waiting' ? 'bg-amber-500 text-white shadow-md' : 'text-slate-600 hover:bg-slate-100' }}"
                                title="Marcar en Espera"
                            >
                                <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                Espera
                            </button>
                        </div>
                    </div>
                    
                    <!-- Chat Messages List -->
                    <div 
                        id="admin-messages-box"
                        class="flex-1 overflow-y-auto p-5 space-y-4 bg-slate-50/20"
                        x-init="$nextTick(() => { $el.scrollTop = $el.scrollHeight }); $watch('$wire.messagesCount', () => { setTimeout(() => { $el.scrollTop = $el.scrollHeight }, 100) })"
                    >
                        @if($machineMessages->isEmpty())
                            <div class="py-16 text-center">
                                <svg class="h-10 w-10 text-slate-350 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                                <p class="text-xs font-mono text-slate-400 font-bold uppercase">Canal vacío</p>
                                <p class="text-[10px] text-slate-500 mt-1 uppercase font-bold">Escribe un aviso o instrucción para los operarios.</p>
                            </div>
                        @else
                            @foreach($machineMessages as $msg)
                                @php
                                    $isAdmin = $msg->from === 'admin';
                                    $isSystem = $msg->from === 'system';
                                @endphp
                                @if($isSystem)
                                    <!-- Centered System Message -->
                                    <div class="flex justify-center my-2">
                                        <div class="px-3.5 py-1 bg-slate-100/85 border border-slate-200 text-slate-500 rounded-full text-[9px] font-mono tracking-wider uppercase font-bold shadow-sm">
                                            {{ $msg->text }}
                                        </div>
                                    </div>
                                @else
                                    <div class="flex items-end gap-2.5 {{ $isAdmin ? 'justify-end' : 'justify-start' }}">
                                        @if(!$isAdmin)
                                            <!-- Operator Avatar -->
                                            <div class="h-7 w-7 rounded-full bg-gradient-to-tr from-cyan-500 to-blue-600 flex items-center justify-center text-white text-[9px] font-black shrink-0 shadow-sm" title="Operario de Planta">
                                                OP
                                            </div>
                                        @endif

                                        <div class="max-w-[75%] group relative">
                                            <!-- Message bubble -->
                                            <div class="rounded-2xl px-4 py-3 border shadow-sm {{ $isAdmin ? 'bg-gradient-to-r from-amber-500 to-orange-500 border-amber-500 text-white rounded-br-none' : 'bg-white border-slate-200 text-slate-800 rounded-bl-none' }}">
                                                <!-- Sender header -->
                                                <div class="flex items-center justify-between gap-3 border-b pb-1 mb-1.5 {{ $isAdmin ? 'border-amber-400/50 text-amber-100' : 'border-slate-100 text-slate-450 text-slate-400' }} text-[9px] font-mono uppercase font-bold">
                                                    <span>{{ $msg->senderName }}</span>
                                                    <span>{{ $msg->timestamp }}</span>
                                                </div>
                                                <!-- Message Text -->
                                                <p class="text-xs leading-relaxed whitespace-pre-wrap font-sans">{{ $msg->text }}</p>
                                            </div>
                                            
                                            <!-- Hover Delete Message button -->
                                            <button
                                                wire:click="deleteMessage('{{ $msg->id }}')"
                                                onclick="window.playAudio('click');"
                                                class="absolute top-1/2 -translate-y-1/2 {{ $isAdmin ? '-left-8' : '-right-8' }} p-1.5 bg-white hover:bg-red-50 border border-slate-200 hover:border-red-200 text-slate-450 hover:text-red-600 rounded-full shadow-sm opacity-0 group-hover:opacity-100 transition-all z-10"
                                                title="Eliminar mensaje"
                                            >
                                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>

                                        @if($isAdmin)
                                            <!-- Admin Avatar -->
                                            <div class="h-7 w-7 rounded-full bg-gradient-to-tr from-amber-500 to-yellow-500 flex items-center justify-center text-white text-[9px] font-black shrink-0 shadow-sm" title="Supervisor">
                                                SV
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            @endforeach
                        @endif
                    </div>
                    
                    <!-- Quick Replies & Footer Input -->
                    <div class="p-4 border-t border-slate-200 bg-slate-50 flex flex-col gap-3 flex-shrink-0">
                        <!-- Quick Replies chips -->
                        <div class="flex flex-wrap gap-1.5 items-center">
                            <span class="text-[9px] font-mono text-slate-450 uppercase tracking-wider mr-1">Respuestas Rápidas:</span>
                            @foreach([
                                '⚠️ Incidencia recibida. Técnico asignado.',
                                '🔧 Iniciando mantenimiento preventivo.',
                                '✅ Unidad autorizada para operar.',
                                '🔄 Por favor reinicia el panel de control.'
                            ] as $qr)
                                <button
                                    type="button"
                                    wire:click="sendQuickReply('{{ $qr }}')"
                                    onclick="window.playAudio('click');"
                                    class="px-2.5 py-1 bg-white hover:bg-cyan-50 border border-slate-200 hover:border-cyan-550/30 hover:border-cyan-500/30 text-slate-600 hover:text-cyan-700 rounded-full text-[10px] font-medium transition-all duration-200 shadow-sm whitespace-nowrap"
                                >
                                    {{ $qr }}
                                </button>
                            @endforeach
                        </div>
                        
                        <!-- Message form -->
                        <form wire:submit.prevent="sendAdminReply" class="flex items-end gap-2.5">
                            <!-- Copilot AI Suggestions Button -->
                            <div class="shrink-0 relative">
                                <button
                                    type="button"
                                    wire:click="generateAiMessageSuggestion"
                                    onclick="window.playAudio('click');"
                                    @if($isGeneratingSuggestion) disabled @endif
                                    class="p-2.5 bg-gradient-to-tr from-cyan-600 to-blue-600 hover:from-cyan-500 hover:to-blue-500 text-white rounded-xl active:scale-95 transition-all flex items-center justify-center shadow-md border border-cyan-400/20 relative group h-10 w-10 disabled:opacity-50"
                                    title="Sugerir respuesta con Copiloto IA (RAG)"
                                >
                                    @if($isGeneratingSuggestion)
                                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    @else
                                        <!-- Stars / Sparkles SVG -->
                                        <svg class="h-4.5 w-4.5 text-white animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                                        </svg>
                                    @endif
                                </button>
                            </div>

                            <div class="flex-1 relative">
                                <textarea
                                    wire:model="adminReplyInput"
                                    rows="2"
                                    @if($isGeneratingSuggestion) disabled placeholder="Copiloto IA analizando RAG y redactando..." @else placeholder="Escribe una respuesta para el operario..." @endif
                                    class="w-full bg-white border rounded-xl pl-4 pr-10 py-2.5 text-xs text-slate-800 placeholder-slate-400 focus:outline-none transition-all shadow-sm resize-none leading-relaxed {{ $adminReplyInput ? 'border-cyan-500 ring-2 ring-cyan-100' : 'border-slate-200 focus:border-cyan-500/50' }}"
                                    x-on:keydown.enter.prevent="if(!$event.shiftKey) { $wire.sendAdminReply() }"
                                ></textarea>
                                <span class="absolute bottom-2.5 right-3 text-[9px] font-mono text-slate-450 text-slate-400 hidden sm:inline">Enter para enviar</span>
                            </div>
                            
                            <button
                                type="submit"
                                onclick="window.playAudio('success');"
                                class="p-3 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-400 hover:to-orange-400 text-white rounded-xl shadow-md hover:shadow-lg active:scale-95 transition-all flex items-center justify-center shrink-0 h-10 w-10"
                                title="Enviar mensaje"
                            >
                                <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </button>
                        </form>
                        
                        @if($isGeneratingSuggestion)
                            <div class="text-[9px] font-mono text-cyan-600 animate-pulse flex items-center gap-1.5">
                                <span class="h-1 w-1 bg-cyan-500 rounded-full animate-ping"></span>
                                Analizando manuales técnicos de la máquina y redactando propuesta...
                            </div>
                        @elseif($adminReplyInput && str_contains($adminReplyInput, 'Técnico') || str_contains($adminReplyInput, 'mantenimiento') || str_contains($adminReplyInput, 'avería'))
                            <div class="text-[9px] font-mono text-cyan-500 flex items-center gap-1">
                                ✨ Sugerencia del Copiloto IA cargada. Puedes editarla libremente antes de enviar.
                            </div>
                        @endif
                    </div>
                @else
                    <div class="flex-1 flex flex-col items-center justify-center p-8 bg-slate-50/20">
                        <svg class="h-12 w-12 text-slate-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        <p class="text-xs font-mono text-slate-400 uppercase tracking-wider">Selecciona una máquina para abrir el canal de comunicación</p>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- ── Status change reason modal ── -->
    @if($showModal)
        <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4 z-50 animate-fade-in">
            <div class="w-full max-w-md bg-white border border-slate-200 rounded-3xl p-6 shadow-2xl relative overflow-hidden">
                <button
                    wire:click="$set('showModal', false)"
                    onclick="window.playAudio('click');"
                    class="absolute top-4 right-4 text-slate-400 hover:text-slate-700 transition-colors"
                >
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                
                <h3 class="text-base font-black text-slate-800 uppercase tracking-wider mb-4 font-outfit">Motivo de Inactividad</h3>
                
                <form wire:submit.prevent="saveStatusChange" class="space-y-4 font-mono text-xs">
                    <div>
                        <p class="text-slate-500 leading-relaxed mb-3">
                            Describe brevemente el motivo para cambiar el estado de esta unidad a <span class="text-slate-800 font-bold">{{ strtoupper($targetStatus === 'warning' ? 'Avería' : ($targetStatus === 'maintenance' ? 'Mantenimiento' : 'Espera')) }}</span>.
                        </p>
                        <input
                            type="text"
                            wire:model="reason"
                            required
                            placeholder="Ej: Fuga de presión en cilindro, revisión preventiva..."
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-800 placeholder-slate-400 focus:outline-none focus:border-cyan-500/50 transition-colors"
                        />
                    </div>

                    <button
                        type="submit"
                        onclick="window.playAudio('success');"
                        class="w-full py-2.5 bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-500 hover:to-blue-500 text-white font-black uppercase tracking-widest rounded-xl shadow-lg active:scale-95 transition-all"
                    >
                        Confirmar Cambio
                    </button>
                </form>
            </div>
        </div>
    @endif
</div>
