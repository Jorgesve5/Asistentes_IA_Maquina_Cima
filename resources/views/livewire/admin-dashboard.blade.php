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
        <button
            wire:click="setTab('training')"
            onclick="window.playAudio('click');"
            class="px-5 py-2.5 rounded-xl text-xs font-black uppercase tracking-wider transition-all duration-200 {{ $activeTab === 'training' ? 'bg-cyan-500 text-slate-950 shadow-[0_4px_12px_rgba(6,182,212,0.15)]' : 'text-slate-500 hover:text-slate-800' }}"
        >
            Formación
        </button>
        <button
            wire:click="setTab('apiconfig')"
            onclick="window.playAudio('click');"
            class="px-5 py-2.5 rounded-xl text-xs font-black uppercase tracking-wider transition-all duration-200 flex items-center gap-1.5 {{ $activeTab === 'apiconfig' ? 'bg-cyan-500 text-slate-950 shadow-[0_4px_12px_rgba(6,182,212,0.15)]' : 'text-slate-500 hover:text-slate-800' }}"
        >
            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" /></svg>
            Configurar API
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
                                                    {{ is_numeric($man->size) ? number_format($man->size / 1024, 1) . ' KB' : $man->size }}
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
                                    <div class="flex justify-center items-center gap-2 my-2 group">
                                        <div class="px-3.5 py-1 bg-slate-100/85 border border-slate-200 text-slate-500 rounded-full text-[9px] font-mono tracking-wider uppercase font-bold shadow-sm">
                                            {{ $msg->text }}
                                        </div>
                                        <!-- Delete Message button for System Messages -->
                                        <button
                                            wire:click="deleteMessage('{{ $msg->id }}')"
                                            onclick="window.playAudio('click');"
                                            class="p-1.5 bg-white hover:bg-red-50 border border-slate-200 hover:border-red-200 text-slate-450 hover:text-red-600 rounded-full shadow-sm transition-all"
                                            title="Eliminar mensaje del sistema"
                                        >
                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
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
                                            
                                            <!-- Delete Message button -->
                                            <button
                                                wire:click="deleteMessage('{{ $msg->id }}')"
                                                onclick="window.playAudio('click');"
                                                class="absolute top-1/2 -translate-y-1/2 {{ $isAdmin ? '-left-8' : '-right-8' }} p-1.5 bg-white hover:bg-red-50 border border-slate-200 hover:border-red-200 text-slate-450 hover:text-red-600 rounded-full shadow-sm transition-all z-10"
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

    <!-- ── TAB: Training (Formación) ── -->
    @if($activeTab === 'training')
        <div style="display: flex; flex-direction: column; gap: 24px;" x-data="{
            quill: null,
            showTools: false,
            activeSubTab: 'manual',
            manualContent: @entangle('manualContent'),
            faqContent: @entangle('faqContent'),
            uploadingFile: false,
            
            init() {
                if (typeof Quill === 'undefined') {
                    let link = document.createElement('link');
                    link.rel = 'stylesheet';
                    link.href = 'https://cdn.quilljs.com/1.3.6/quill.snow.css';
                    document.head.appendChild(link);

                    let script = document.createElement('script');
                    script.src = 'https://cdn.quilljs.com/1.3.6/quill.js';
                    script.onload = () => this.initQuill();
                    document.head.appendChild(script);
                } else {
                    this.initQuill();
                }

                $wire.on('trainingContentUpdated', (e) => {
                    let data = Array.isArray(e) ? e[0] : (e.detail || e);
                    this.manualContent = data.manual || '';
                    this.faqContent = data.faq || '';
                    
                    if (this.quill) {
                        this.quill.root.innerHTML = this.activeSubTab === 'manual' ? this.manualContent : this.faqContent;
                    }
                });
            },
            initQuill() {
                this.quill = new Quill($refs.quillEditor, {
                    theme: 'snow',
                    modules: {
                        toolbar: {
                            container: [
                                [{ 'header': [1, 2, 3, false] }],
                                ['bold', 'italic', 'underline', 'strike'],
                                [{ 'color': [] }, { 'background': [] }],
                                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                                ['link', 'image', 'video'],
                                ['clean']
                            ],
                            handlers: {
                                image: () => {
                                    this.triggerFileUpload();
                                }
                            }
                        }
                    }
                });
                
                this.quill.root.innerHTML = this.manualContent || '';
                
                this.quill.root.addEventListener('paste', (e) => {
                    const clipboardData = e.clipboardData || window.clipboardData;
                    if (clipboardData && clipboardData.items) {
                        for (let i = 0; i < clipboardData.items.length; i++) {
                            if (clipboardData.items[i].type.indexOf('image') !== -1) {
                                e.preventDefault();
                                const file = clipboardData.items[i].getAsFile();
                                if (file) this.processFile(file);
                                return;
                            }
                        }
                    }
                });
                
                this.quill.root.addEventListener('drop', (e) => {
                    if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files.length) {
                        for (let i = 0; i < e.dataTransfer.files.length; i++) {
                            if (e.dataTransfer.files[i].type.indexOf('image') !== -1) {
                                e.preventDefault();
                                const file = e.dataTransfer.files[i];
                                if (file) this.processFile(file);
                                return;
                            }
                        }
                    }
                });
                
                this.quill.on('text-change', () => {
                    if (this.activeSubTab === 'manual') {
                        this.manualContent = this.quill.root.innerHTML;
                    } else {
                        this.faqContent = this.quill.root.innerHTML;
                    }
                });
            },
            switchTab(tab) {
                if (this.activeSubTab === 'manual') this.manualContent = this.quill.root.innerHTML;
                if (this.activeSubTab === 'faq') this.faqContent = this.quill.root.innerHTML;
                
                this.activeSubTab = tab;
                this.quill.root.innerHTML = tab === 'manual' ? (this.manualContent || '') : (this.faqContent || '');
            },
            save() {
                if (this.quill) {
                    if (this.activeSubTab === 'manual') this.manualContent = this.quill.root.innerHTML;
                    if (this.activeSubTab === 'faq') this.faqContent = this.quill.root.innerHTML;
                    
                    $wire.saveTrainingContent(this.manualContent, this.faqContent);
                }
            },
            triggerFileUpload() {
                this.$refs.trainingFileInput.click();
            },
            async handleFileUpload(event) {
                const file = event.target.files[0];
                if (!file) return;
                this.processFile(file);
                event.target.value = '';
            },
            async processFile(file) {
                this.uploadingFile = true;
                
                $wire.upload('trainingFile', file, 
                    async (uploadedFilename) => {
                        try {
                            const url = await $wire.storeTrainingFile();
                            if (url) {
                                const isImage = file.type.startsWith('image/');
                                let range = this.quill ? this.quill.getSelection(true) : null;
                                let index = range ? range.index : (this.quill ? this.quill.getLength() : 0);
                                if (isImage && this.quill) {
                                    this.quill.insertEmbed(index, 'image', url);
                                } else if (this.quill) {
                                    const ext = file.name ? file.name.split('.').pop().toUpperCase() : 'FILE';
                                    const icons = { PDF: '📄', DOC: '📝', DOCX: '📝', XLS: '📊', XLSX: '📊', ZIP: '📦', MP4: '🎬', default: '📎' };
                                    const icon = icons[ext] || icons.default;
                                    const kb = (file.size/1024).toFixed(0);
                                    const name = file.name || 'Archivo Pegado';
                                    const html = '<p><a href=\'' + url + '\' target=\'_blank\' rel=\'noopener\' style=\'display:inline-flex;align-items:center;gap:8px;padding:10px 18px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;text-decoration:none;color:#0f172a;font-weight:700;font-size:14px;transition:all 0.2s;\'>' + icon + ' ' + name + ' <span style=\'font-size:11px;color:#94a3b8;font-weight:400;\'>' + ext + ' · ' + kb + ' KB</span></a></p>';
                                    this.quill.clipboard.dangerouslyPasteHTML(index, html);
                                }
                            }
                        } catch(e) {
                            console.error(e);
                            alert('Error al subir la imagen. Es posible que el archivo sea demasiado pesado.');
                        } finally {
                            this.uploadingFile = false;
                        }
                    },
                    () => {
                        this.uploadingFile = false;
                        alert('Error de subida (Livewire). Comprueba que el archivo no supere el tamaño máximo permitido por el servidor.');
                    },
                    (event) => {}
                );
            },
            renderPreview(html) {
                return window.renderPdfPreview(html, '<div class=\'text-slate-400 text-center mt-10 italic\'>Sin contenido...</div>');
            },
            insertComponent(type) {
                if (!this.quill) return;
                
                let range = this.quill.getSelection(true);
                let index = range ? range.index : this.quill.getLength();
                let html = '';
                
                if (type === 'warning') {
                    html = '<div style=\'background-color: #fef2f2; border-left: 4px solid #ef4444; padding: 16px; margin: 16px 0; border-radius: 0 8px 8px 0;\'><strong style=\'color: #991b1b; display: block; margin-bottom: 8px; font-size: 16px;\'>⚠️ Advertencia de Seguridad</strong><p style=\'color: #7f1d1d; margin: 0; font-size: 14px;\'>Escribe aquí las precauciones antes de operar la máquina...</p></div><p><br></p>';
                } else if (type === 'info') {
                    html = '<div style=\'background-color: #eff6ff; border-left: 4px solid #3b82f6; padding: 16px; margin: 16px 0; border-radius: 0 8px 8px 0;\'><strong style=\'color: #1e3a8a; display: block; margin-bottom: 8px; font-size: 16px;\'>ℹ️ Información Importante</strong><p style=\'color: #1e40af; margin: 0; font-size: 14px;\'>Escribe aquí datos clave o aclaraciones operativas...</p></div><p><br></p>';
                } else if (type === 'step') {
                    html = '<div style=\'background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; margin: 16px 0;\'><h3 style=\'color: #0f172a; margin-top: 0; margin-bottom: 12px; font-size: 18px;\'>Paso 1: Título de la acción</h3><p style=\'color: #475569; margin-bottom: 0; font-size: 14px;\'>Describe detalladamente las instrucciones a realizar...</p></div><p><br></p>';
                } else if (type === 'accordion') {
                    html = '<h3><span style=\'color: #a855f7;\'>❓</span> <strong>Pregunta Frecuente (Edita este título)</strong></h3><p>Escribe aquí la respuesta a la pregunta...</p><p><br></p>';
                }
                
                this.quill.clipboard.dangerouslyPasteHTML(index, html);
                this.showTools = false;
            }
        }">
            
            <!-- Premium Clean Header -->
            <div style="background-color: #ffffff; border-radius: 32px; padding: 32px 40px; border: 1px solid #e2e8f0; box-shadow: 0 10px 40px -10px rgba(0,0,0,0.05);">
                <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 32px;">
                    
                    <!-- Left Side: Title and Text -->
                    <div style="flex: 1 1 500px; min-width: 0;">
                        <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 12px;">
                            <div style="height: 56px; width: 56px; flex-shrink: 0; border-radius: 16px; display: flex; align-items: center; justify-content: center; background-color: #ecfeff; border: 1px solid #cffafe; color: #0891b2;">
                                <svg style="height: 28px; width: 28px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                            </div>
                            <h2 style="font-size: 22px; font-weight: 900; color: #1e293b; letter-spacing: 0.1em; text-transform: uppercase; margin: 0; font-family: 'Outfit', sans-serif;">Gestión de Formación</h2>
                        </div>
                        <p style="font-size: 15px; letter-spacing: 0.025em; color: #64748b; line-height: 1.6; margin: 0; max-width: 800px; font-family: ui-sans-serif, system-ui, sans-serif;">
                            Diseña y organiza el material de ayuda visual para los operarios. Selecciona una unidad del sistema en el panel derecho para comenzar a crear o actualizar su documentación interactiva.
                        </p>
                    </div>

                    <!-- Right Side: Selector -->
                    <div style="flex: 0 0 320px; width: 100%;">
                        <label style="display: block; font-size: 11px; font-weight: 900; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 8px; padding-left: 4px;">Unidad Seleccionada</label>
                        <div style="position: relative;">
                            <select
                                wire:model.live="selectedMachineForTraining"
                                style="width: 100%; display: block; background-color: #f8fafc; border: 2px solid #e2e8f0; color: #0f172a; padding: 14px 16px; border-radius: 16px; font-size: 14px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; outline: none; appearance: none; cursor: pointer; transition: all 0.2s; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%2364748b%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E'); background-repeat: no-repeat; background-position: right 16px top 50%; background-size: 10px auto;"
                            >
                                @foreach($machines as $m)
                                    <option value="{{ $m->id }}">{{ $m->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                </div>
            </div>

            @if(session()->has('training_success'))
                <div style="background-color: #ecfdf5; border: 1px solid #a7f3d0; padding: 16px 24px; border-radius: 16px; display: flex; align-items: center; gap: 12px; margin-bottom: 24px;">
                    <div style="height: 32px; width: 32px; border-radius: 50%; background-color: #d1fae5; display: flex; align-items: center; justify-content: center; color: #059669;">
                        <svg style="height: 20px; width: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <span style="color: #065f46; font-size: 13px; font-weight: 900; letter-spacing: 0.1em; text-transform: uppercase;">{{ session('training_success') }}</span>
                </div>
            @endif

            <!-- Premium Sub Tabs -->
            <div style="display: flex; flex-wrap: wrap; gap: 16px; margin-bottom: 8px; padding: 8px; background-color: #f8fafc; border-radius: 24px; border: 1px solid #e2e8f0; max-width: max-content;">
                <button 
                    type="button" 
                    @click="switchTab('manual')" 
                    style="padding: 14px 28px; border-radius: 16px; font-size: 14px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.05em; cursor: pointer; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); border: 2px solid transparent; display: flex; align-items: center; gap: 10px;"
                    :style="{ 
                        background: activeSubTab === 'manual' ? 'linear-gradient(135deg, #0ea5e9, #2563eb)' : 'transparent', 
                        color: activeSubTab === 'manual' ? 'white' : '#64748b', 
                        boxShadow: activeSubTab === 'manual' ? '0 10px 25px -5px rgba(14, 165, 233, 0.4)' : 'none', 
                        transform: activeSubTab === 'manual' ? 'translateY(-2px)' : 'none' 
                    }"
                >
                    <svg style="height: 20px; width: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    <span style="white-space: nowrap;">Manual de Aprendizaje</span>
                </button>
                <button 
                    type="button" 
                    @click="switchTab('faq')" 
                    style="padding: 14px 28px; border-radius: 16px; font-size: 14px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.05em; cursor: pointer; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); border: 2px solid transparent; display: flex; align-items: center; gap: 10px;"
                    :style="{ 
                        background: activeSubTab === 'faq' ? 'linear-gradient(135deg, #8b5cf6, #c026d3)' : 'transparent', 
                        color: activeSubTab === 'faq' ? 'white' : '#64748b', 
                        boxShadow: activeSubTab === 'faq' ? '0 10px 25px -5px rgba(192, 38, 211, 0.4)' : 'none', 
                        transform: activeSubTab === 'faq' ? 'translateY(-2px)' : 'none' 
                    }"
                >
                    <svg style="height: 20px; width: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span style="white-space: nowrap;">Preguntas Frecuentes</span>
                </button>
            </div>

            <!-- Editor and Preview Section -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(450px, 1fr)); gap: 24px; margin-top: 16px;">
                
                <!-- Left: Editor -->
                <div style="background-color: #ffffff; border-radius: 32px; box-shadow: 0 10px 40px -10px rgba(0,0,0,0.08); overflow: hidden; border: 1px solid #e2e8f0; display: flex; flex-direction: column;">
                    <div style="border-bottom: 1px solid #f1f5f9; background-color: #f8fafc; padding: 20px 32px; display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 16px;">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div style="position: relative; display: flex; height: 12px; width: 12px;">
                                <span style="position: absolute; display: inline-flex; height: 100%; width: 100%; border-radius: 50%; background-color: #22d3ee; opacity: 0.75; animation: ping 1s cubic-bezier(0, 0, 0.2, 1) infinite;"></span>
                                <span style="position: relative; display: inline-flex; border-radius: 50%; height: 12px; width: 12px; background-color: #06b6d4;"></span>
                            </div>
                            <div>
                                <span style="display: block; font-size: 10px; font-weight: 900; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.1em; line-height: 1; margin-bottom: 4px;">Entorno de Autor</span>
                                <span style="display: block; font-size: 15px; font-weight: 700; color: #1e293b;">Editor de Contenido</span>
                            </div>
                        </div>
                        <div style="display: flex; align-items: center; gap: 16px;">
                            
                            <!-- Attach File Button -->
                            <button
                                type="button"
                                @click="triggerFileUpload()"
                                style="padding: 10px 20px; background-color: white; border: 1px solid #a5b4fc; color: #4f46e5; font-size: 13px; font-weight: 700; border-radius: 12px; display: flex; align-items: center; gap: 8px; cursor: pointer; transition: all 0.2s; white-space: nowrap; flex-shrink: 0;"
                                onmouseover="this.style.backgroundColor='#eef2ff'; this.style.borderColor='#818cf8';"
                                onmouseout="this.style.backgroundColor='white'; this.style.borderColor='#a5b4fc';"
                                :disabled="uploadingFile"
                            >
                                <template x-if="!uploadingFile">
                                    <svg style="height: 16px; width: 16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                    </svg>
                                </template>
                                <template x-if="uploadingFile">
                                    <svg style="height: 16px; width: 16px; animation: spin 1s linear infinite;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                </template>
                                <span x-text="uploadingFile ? 'Subiendo...' : 'Adjuntar Archivo'"></span>
                            </button>
                            <!-- Hidden file input -->
                            <input
                                type="file"
                                x-ref="trainingFileInput"
                                @change="handleFileUpload($event)"
                                class="hidden"
                            />

                            <!-- Dropdown Añadir Bloque -->
                            <div style="position: relative;" @click.away="showTools = false">
                                <button 
                                    @click="showTools = !showTools"
                                    type="button" 
                                    style="padding: 10px 20px; background-color: white; border: 1px solid #cbd5e1; color: #475569; font-size: 13px; font-weight: 700; border-radius: 12px; display: flex; align-items: center; gap: 8px; cursor: pointer; transition: all 0.2s; white-space: nowrap; flex-shrink: 0;"
                                    onmouseover="this.style.borderColor='#94a3b8'; this.style.color='#1e293b';"
                                    onmouseout="this.style.borderColor='#cbd5e1'; this.style.color='#475569';"
                                >
                                    <svg style="height: 16px; width: 16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    <span>Añadir Bloque</span>
                                    <svg style="height: 14px; width: 14px; transition: transform 0.2s;" x-bind:style="showTools ? 'transform: rotate(180deg);' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>

                                <!-- Dropdown Menu -->
                                <div x-show="showTools" x-transition.opacity.duration.200ms style="display: none; position: absolute; right: 0; top: 100%; margin-top: 8px; width: 250px; background-color: white; border-radius: 16px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1), 0 8px 10px -6px rgba(0,0,0,0.1); border: 1px solid #e2e8f0; z-index: 50; overflow: hidden; padding: 8px;">
                                    <div style="padding: 8px 12px; font-size: 10px; font-weight: 900; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.1em; border-bottom: 1px solid #f1f5f9; margin-bottom: 4px;">Componentes Profesionales</div>
                                    
                                    <button @click="insertComponent('accordion')" type="button" style="width: 100%; text-align: left; padding: 10px 12px; border: none; background: transparent; border-radius: 8px; display: flex; align-items: center; gap: 12px; cursor: pointer; transition: background 0.2s;" onmouseover="this.style.backgroundColor='#faf5ff';" onmouseout="this.style.backgroundColor='transparent';">
                                        <span style="font-size: 18px;">❓</span>
                                        <div>
                                            <div style="font-size: 13px; font-weight: 700; color: #7e22ce;">Pregunta Desplegable</div>
                                            <div style="font-size: 11px; color: #a855f7;">Acordeón para FAQs</div>
                                        </div>
                                    </button>
                                    <button @click="insertComponent('warning')" type="button" style="width: 100%; text-align: left; padding: 10px 12px; border: none; background: transparent; border-radius: 8px; display: flex; align-items: center; gap: 12px; cursor: pointer; transition: background 0.2s;" onmouseover="this.style.backgroundColor='#fef2f2';" onmouseout="this.style.backgroundColor='transparent';">
                                        <span style="font-size: 18px;">⚠️</span>
                                        <div>
                                            <div style="font-size: 13px; font-weight: 700; color: #991b1b;">Alerta Precaución</div>
                                            <div style="font-size: 11px; color: #f87171;">Destacar riesgos operativos</div>
                                        </div>
                                    </button>
                                    
                                    <button @click="insertComponent('info')" type="button" style="width: 100%; text-align: left; padding: 10px 12px; border: none; background: transparent; border-radius: 8px; display: flex; align-items: center; gap: 12px; cursor: pointer; transition: background 0.2s;" onmouseover="this.style.backgroundColor='#eff6ff';" onmouseout="this.style.backgroundColor='transparent';">
                                        <span style="font-size: 18px;">ℹ️</span>
                                        <div>
                                            <div style="font-size: 13px; font-weight: 700; color: #1e40af;">Nota Informativa</div>
                                            <div style="font-size: 11px; color: #60a5fa;">Aclaraciones y consejos útiles</div>
                                        </div>
                                    </button>
                                    
                                    <button @click="insertComponent('step')" type="button" style="width: 100%; text-align: left; padding: 10px 12px; border: none; background: transparent; border-radius: 8px; display: flex; align-items: center; gap: 12px; cursor: pointer; transition: background 0.2s;" onmouseover="this.style.backgroundColor='#f8fafc';" onmouseout="this.style.backgroundColor='transparent';">
                                        <span style="font-size: 18px;">📋</span>
                                        <div>
                                            <div style="font-size: 13px; font-weight: 700; color: #334155;">Paso de Guía</div>
                                            <div style="font-size: 11px; color: #94a3b8;">Bloque para tutoriales</div>
                                        </div>
                                    </button>
                                </div>
                            </div>

                            <button
                                type="button"
                                @click="save(); window.playAudio('success');"
                                style="padding: 12px 32px; background: linear-gradient(to right, #0891b2, #2563eb); color: white; font-size: 12px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.1em; border-radius: 16px; border: none; cursor: pointer; box-shadow: 0 8px 20px rgba(6,182,212,0.3); display: flex; align-items: center; gap: 8px; transition: all 0.2s;"
                                onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 12px 25px rgba(6,182,212,0.4)'"
                                onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 8px 20px rgba(6,182,212,0.3)'"
                            >
                                <svg style="height: 18px; width: 18px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                                </svg>
                                <span>Publicar Cambios</span>
                            </button>
                        </div>
                    </div>
                    
                    <div wire:ignore style="position: relative; background-color: #ffffff;">
                        <style>
                            .ql-toolbar.ql-snow {
                                border: none !important;
                                border-bottom: 1px solid #f1f5f9 !important;
                                padding: 16px 24px !important;
                                background-color: #f8fafc;
                                font-family: inherit;
                            }
                            .ql-container.ql-snow {
                                border: none !important;
                                font-family: 'Inter', sans-serif !important;
                                font-size: 15px !important;
                                color: #334155;
                            }
                            .ql-editor {
                                min-height: 500px;
                                padding: 32px 48px;
                                line-height: 1.7;
                            }
                            .ql-editor p {
                                margin-bottom: 1em;
                            }
                            .ql-editor h1, .ql-editor h2, .ql-editor h3 {
                                font-weight: 800;
                                color: #0f172a;
                                margin-top: 1.5em;
                                margin-bottom: 0.5em;
                            }
                        </style>
                        <div x-ref="quillEditor"></div>
                    </div>
                </div>

                <!-- Right: Live Preview -->
                <div style="background-color: #ffffff; border-radius: 32px; box-shadow: 0 10px 40px -10px rgba(0,0,0,0.08); overflow: hidden; border: 1px solid #e2e8f0; display: flex; flex-direction: column; min-height: 600px;">
                    <div style="border-bottom: 1px solid #f1f5f9; background-color: #f8fafc; padding: 20px 32px; display: flex; align-items: center; gap: 12px;">
                        <div style="position: relative; display: flex; height: 12px; width: 12px;">
                            <span style="position: absolute; display: inline-flex; height: 100%; width: 100%; border-radius: 50%; background-color: #a855f7; opacity: 0.75; animation: ping 1s cubic-bezier(0, 0, 0.2, 1) infinite;"></span>
                            <span style="position: relative; display: inline-flex; border-radius: 50%; height: 12px; width: 12px; background-color: #9333ea;"></span>
                        </div>
                        <div>
                            <span style="display: block; font-size: 10px; font-weight: 900; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.1em; line-height: 1; margin-bottom: 4px;">Previsualización en Directo</span>
                            <span style="display: block; font-size: 15px; font-weight: 700; color: #1e293b;">Vista del Operario</span>
                        </div>
                    </div>
                    
                    <div class="ql-editor prose prose-sm max-w-none text-slate-700" style="padding: 32px 48px; overflow-y: auto; flex: 1;" x-html="renderPreview(activeSubTab === 'manual' ? manualContent : faqContent)">
                    </div>
                </div>
                
            </div>
        </div>
    @endif

    <script>
        window.togglePdfViewer = window.togglePdfViewer || function(viewerId, btnId) {
            const v = document.getElementById(viewerId);
            const b = document.getElementById(btnId);
            if (!v) return;
            const eyeOpenSvg   = '<svg style="height:14px;width:14px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg><span>Visualizar</span>';
            const eyeClosedSvg = '<svg style="height:14px;width:14px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L6.59 6.59m7.532 7.532l3.29 3.29M3 3l18 18"/></svg><span>Ocultar</span>';

            if (v.style.display === 'none' || !v.style.display) {
                v.style.display = 'block';
                if (b) b.innerHTML = eyeClosedSvg;

                // --- SheetJS: cargar Excel si aplica y aún no se ha cargado ---
                var sheetOutput = v.querySelector('[id^="sheetjs-trn-"][id$="' + viewerId + '"]') ||
                                  v.querySelector('[id^="sheetjs-trn-"]');
                if (sheetOutput && !sheetOutput.dataset.loaded) {
                    sheetOutput.dataset.loaded = '1';
                    var sheetTabs = v.querySelector('[id^="sheetjs-trn-tabs-"]');
                    var fileUrl   = sheetOutput.dataset.src || (function() {
                        // Intentar obtener URL del enlace de descarga en el mismo widget
                        var card = v.closest('[class*="group/card"]') || v.parentElement;
                        var a = card ? card.querySelector('a[download]') : null;
                        return a ? a.href : null;
                    })();

                    sheetOutput.innerHTML = '<div style="padding:32px;text-align:center;color:#94a3b8;font-size:12px;">Cargando hoja de cálculo…</div>';

                    function doRender(url) {
                        function loadXLSX(cb) { if (window.XLSX) { cb(); return; } var s = document.createElement('script'); s.src='https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js'; s.onload=cb; document.head.appendChild(s); }
                        function renderSheet(wb, name) {
                            var ws = wb.Sheets[name];
                            var html = XLSX.utils.sheet_to_html(ws, {editable:false});
                            var cid = sheetOutput.id;
                            sheetOutput.innerHTML = '<style>#'+cid+' table{border-collapse:collapse;width:100%;font-size:12px;font-family:monospace}#'+cid+' td,#'+cid+' th{border:1px solid #e2e8f0;padding:4px 8px;white-space:nowrap}#'+cid+' tr:nth-child(even){background:#f8fafc}#'+cid+' tr:first-child{background:#1e293b;color:#fff;font-weight:bold}</style>'+html;
                        }
                        function renderTabs(wb, active) {
                            if (!sheetTabs) return;
                            sheetTabs.innerHTML='';
                            wb.SheetNames.forEach(function(name){
                                var btn=document.createElement('button');
                                btn.textContent=name;
                                btn.style.cssText='padding:4px 10px;font-size:11px;font-weight:700;border-radius:6px 6px 0 0;cursor:pointer;border:none;transition:all .15s;' + (name===active?'background:#fff;color:#0e7490;border:1px solid #e2e8f0;margin-bottom:-1px;':'background:#f1f5f9;color:#64748b;');
                                btn.onclick=function(){renderSheet(wb,name);renderTabs(wb,name);};
                                sheetTabs.appendChild(btn);
                            });
                        }
                        loadXLSX(function(){
                            fetch(url).then(function(r){return r.arrayBuffer();}).then(function(data){
                                var wb=XLSX.read(data,{type:'array'});
                                var first=wb.SheetNames[0];
                                renderTabs(wb,first);
                                renderSheet(wb,first);
                            }).catch(function(){
                                sheetOutput.innerHTML='<div style="padding:32px;text-align:center;color:#94a3b8;font-size:12px;">No se pudo cargar el Excel. Usa el botón Descargar.</div>';
                            });
                        });
                    }

                    if (fileUrl) {
                        doRender(fileUrl);
                    } else {
                        sheetOutput.innerHTML='<div style="padding:32px;text-align:center;color:#94a3b8;font-size:12px;">No se encontró la URL del archivo.</div>';
                    }
                }
                // -----------------------------------------------------------------
            } else {
                v.style.display = 'none';
                if (b) b.innerHTML = eyeOpenSvg;
            }
        };

        window.closePdfViewer = window.closePdfViewer || function(el) {
            const viewer = el.closest('[id^=pdf-viewer-]');
            if (!viewer) return;
            viewer.style.display = 'none';
            const btnId = 'pdf-btn-' + viewer.id;
            const b = document.getElementById(btnId);
            if (b) {
                b.innerHTML = '<svg style="height:14px;width:14px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg><span>Visualizar</span>';
            }
        };

        window.renderPdfPreview = window.renderPdfPreview || function(html, fallbackHtml = '') {
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
                
                let isExcel = ext === 'XLSX' || ext === 'XLS';
                let isImg   = ext === 'IMG'  || ext === 'PNG' || ext === 'JPG' || ext === 'JPEG' || ext === 'GIF' || ext === 'WEBP';
                let isPdf   = ext === 'PDF';

                // Build the viewer inner HTML depending on file type
                let viewerInner = '';
                if (isExcel) {
                    let sheetContainerId = 'sheetjs-trn-' + viewerId;
                    let sheetTabsId      = 'sheetjs-trn-tabs-' + viewerId;
                    viewerInner = `
                        <div class="w-full" style="height:600px; display:flex; flex-direction:column; background:#fff;">
                            <div id="${sheetTabsId}" style="display:flex;gap:4px;padding:8px 12px;background:#f8fafc;border-bottom:1px solid #e2e8f0;overflow-x:auto;flex-shrink:0;"></div>
                            <div style="flex:1;overflow:auto;"><div id="${sheetContainerId}" style="min-width:100%;padding:4px;"></div></div>
                        </div>`;
                } else if (isImg) {
                    viewerInner = `<div style="height:600px;display:flex;align-items:center;justify-content:center;padding:16px;background:#f8fafc;overflow:auto;"><img src="${href}" alt="${displayName}" style="max-width:100%;max-height:100%;object-fit:contain;border-radius:8px;border:1px solid #e2e8f0;box-shadow:0 4px 12px rgba(0,0,0,.08);" /></div>`;
                } else if (isPdf) {
                    viewerInner = `<iframe src="${href}#view=FitH&toolbar=1" class="w-full border-none rounded-lg bg-white shadow-inner" style="height:600px;" loading="lazy"></iframe>`;
                } else {
                    // Google Docs Viewer para Word, PPT, y otros (requiere URL pública)
                    let gdocsUrl = 'https://docs.google.com/viewer?url=' + encodeURIComponent(href) + '&embedded=true';
                    viewerInner = `<iframe src="${gdocsUrl}" class="w-full border-none rounded-lg bg-white shadow-inner" style="height:600px;" loading="lazy"></iframe>`;
                }

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
                            <a href="${href}" download="${displayName}" class="px-4 py-2 bg-slate-100 hover:bg-slate-800 text-slate-600 hover:text-white text-[11px] font-bold rounded-xl transition-all duration-200 flex items-center gap-2 no-underline shadow-sm border border-slate-200/40">
                                <svg class="h-3.5 w-3.5" width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                <span>Descargar</span>
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
                                <span class="text-[10px] font-black text-slate-300 tracking-wider uppercase">Vista Previa · ${displayName}</span>
                            </div>
                            <button type="button" onclick="window.closePdfViewer(this)" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white text-[10px] font-bold uppercase tracking-wider transition-all">
                                <svg class="h-3 w-3" width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                <span>Cerrar</span>
                            </button>
                        </div>
                        <div class="bg-slate-100 p-2">
                            ${viewerInner}
                        </div>
                        <div class="px-5 py-2.5 bg-slate-50 border-t border-slate-150 flex items-center justify-between">
                            <span class="text-[10px] text-slate-400 font-mono tracking-wide">Visor de Documento</span>
                            <a href="${href}" download="${displayName}" class="text-[10px] text-cyan-600 hover:text-cyan-700 font-bold flex items-center gap-1 no-underline hover:underline transition-all">
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

    {{-- ── TAB: Configurar API ── --}}
    @if($activeTab === 'apiconfig')
    <div
        style="max-width: 680px; margin: 0 auto; width: 100%;"
        x-data="{
            showKey: false,
            provider: '{{ $apiProvider }}',
            updateProvider(val) {
                this.provider = val;
            }
        }"
    >

        {{-- Header card --}}
        <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 24px; padding: 32px; box-shadow: 0 2px 12px rgba(0,0,0,0.04); margin-bottom: 20px;">

            {{-- Title --}}
            <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 24px;">
                <div style="height: 56px; width: 56px; flex-shrink: 0; border-radius: 16px; background: #f5f3ff; border: 1px solid #ede9fe; display: flex; align-items: center; justify-content: center;">
                    <svg style="height: 28px; width: 28px; color: #7c3aed;" fill="none" viewBox="0 0 24 24" stroke="#7c3aed" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                    </svg>
                </div>
                <div>
                    <h2 style="font-size: 18px; font-weight: 900; color: #0f172a; letter-spacing: 0.05em; text-transform: uppercase; margin: 0; font-family: 'Outfit', sans-serif;">Configurar API de IA</h2>
                    <p style="font-size: 12px; color: #64748b; margin: 4px 0 0 0; line-height: 1.5;">Elige el proveedor e introduce tu clave. Los cambios se aplican al instante.</p>
                </div>
            </div>

            {{-- Estado actual --}}
            @php
                $groqKey   = config('services.groq.key');
                $geminiKey = config('services.gemini.key');
                $openaiKey = config('services.openai.key');
                $activeProvider = null;
                $activeModel    = null;
                if ($groqKey)        { $activeProvider = 'Groq (Llama)';     $activeModel = 'llama-3.1-8b-instant'; }
                elseif ($geminiKey)  { $activeProvider = 'Google Gemini';    $activeModel = 'gemini-2.5-flash'; }
                elseif ($openaiKey)  { $activeProvider = 'OpenAI (GPT-4o)';  $activeModel = 'gpt-4o-mini'; }
            @endphp
            <div style="display: flex; align-items: center; gap: 10px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px; padding: 12px 16px; margin-bottom: 24px;">
                @if($activeProvider)
                    <span style="position: relative; display: inline-flex; height: 10px; width: 10px; flex-shrink: 0;">
                        <span class="animate-ping" style="position: absolute; display: inline-flex; height: 100%; width: 100%; border-radius: 50%; background: #34d399; opacity: 0.75;"></span>
                        <span style="position: relative; display: inline-flex; border-radius: 50%; height: 10px; width: 10px; background: #10b981;"></span>
                    </span>
                    <div>
                        <span style="font-size: 11px; font-weight: 900; color: #065f46; text-transform: uppercase; letter-spacing: 0.08em;">API Activa: {{ $activeProvider }}</span>
                        <span style="font-size: 10px; color: #94a3b8; font-family: monospace; margin-left: 8px;">Modelo: {{ $activeModel }}</span>
                    </div>
                @else
                    <span style="display: inline-flex; border-radius: 50%; height: 10px; width: 10px; background: #ef4444; flex-shrink: 0;"></span>
                    <div>
                        <span style="font-size: 11px; font-weight: 900; color: #dc2626; text-transform: uppercase; letter-spacing: 0.08em;">Sin API Configurada</span>
                        <span style="font-size: 10px; color: #94a3b8; font-family: monospace; margin-left: 8px;">El asistente IA funciona en modo simulación local</span>
                    </div>
                @endif
            </div>

            {{-- Flash messages --}}
            @if(session()->has('api_config_success'))
                <div style="display: flex; align-items: center; gap: 10px; background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; font-size: 12px; font-weight: 700; padding: 12px 16px; border-radius: 14px; margin-bottom: 20px;">
                    <svg style="height: 16px; width: 16px; flex-shrink: 0;" fill="none" viewBox="0 0 24 24" stroke="#10b981" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    {{ session('api_config_success') }}
                </div>
            @endif
            @if(session()->has('api_config_error'))
                <div style="display: flex; align-items: center; gap: 10px; background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; font-size: 12px; font-weight: 700; padding: 12px 16px; border-radius: 14px; margin-bottom: 20px;">
                    <svg style="height: 16px; width: 16px; flex-shrink: 0;" fill="none" viewBox="0 0 24 24" stroke="#ef4444" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                    {{ session('api_config_error') }}
                </div>
            @endif

            {{-- FORM --}}
            <form wire:submit.prevent="saveApiConfig" style="display: flex; flex-direction: column; gap: 20px;">

                {{-- Selector de proveedor --}}
                <div>
                    <p style="font-size: 10px; font-weight: 900; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.12em; margin-bottom: 12px;">Proveedor de IA</p>
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px;">

                        {{-- Groq --}}
                        <label style="cursor: pointer;" onclick="this.querySelector('input').click()">
                            <input type="radio" wire:model.live="apiProvider" value="groq" style="position: absolute; opacity: 0; width: 0; height: 0;"
                                @change="updateProvider('groq')">
                            <div
                                :style="provider === 'groq'
                                    ? 'border: 2px solid #7c3aed; background: #f5f3ff; border-radius: 16px; padding: 16px; text-align: center; transition: all 0.2s;'
                                    : 'border: 2px solid #e2e8f0; background: #fff; border-radius: 16px; padding: 16px; text-align: center; transition: all 0.2s;'"
                            >
                                <div style="font-size: 24px; margin-bottom: 6px;">⚡</div>
                                <div :style="provider === 'groq' ? 'font-size: 12px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.05em; color: #6d28d9;' : 'font-size: 12px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.05em; color: #334155;'">Groq</div>
                                <div style="font-size: 10px; color: #94a3b8; margin-top: 3px; font-family: monospace;">Llama · Rápido</div>
                            </div>
                        </label>

                        {{-- Gemini --}}
                        <label style="cursor: pointer;" onclick="this.querySelector('input').click()">
                            <input type="radio" wire:model.live="apiProvider" value="gemini" style="position: absolute; opacity: 0; width: 0; height: 0;"
                                @change="updateProvider('gemini')">
                            <div
                                :style="provider === 'gemini'
                                    ? 'border: 2px solid #7c3aed; background: #f5f3ff; border-radius: 16px; padding: 16px; text-align: center; transition: all 0.2s;'
                                    : 'border: 2px solid #e2e8f0; background: #fff; border-radius: 16px; padding: 16px; text-align: center; transition: all 0.2s;'"
                            >
                                <div style="font-size: 24px; margin-bottom: 6px;">✨</div>
                                <div :style="provider === 'gemini' ? 'font-size: 12px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.05em; color: #6d28d9;' : 'font-size: 12px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.05em; color: #334155;'">Gemini</div>
                                <div style="font-size: 10px; color: #94a3b8; margin-top: 3px; font-family: monospace;">Google · Potente</div>
                            </div>
                        </label>

                        {{-- OpenAI --}}
                        <label style="cursor: pointer;" onclick="this.querySelector('input').click()">
                            <input type="radio" wire:model.live="apiProvider" value="openai" style="position: absolute; opacity: 0; width: 0; height: 0;"
                                @change="updateProvider('openai')">
                            <div
                                :style="provider === 'openai'
                                    ? 'border: 2px solid #7c3aed; background: #f5f3ff; border-radius: 16px; padding: 16px; text-align: center; transition: all 0.2s;'
                                    : 'border: 2px solid #e2e8f0; background: #fff; border-radius: 16px; padding: 16px; text-align: center; transition: all 0.2s;'"
                            >
                                <div style="font-size: 24px; margin-bottom: 6px;">🤖</div>
                                <div :style="provider === 'openai' ? 'font-size: 12px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.05em; color: #6d28d9;' : 'font-size: 12px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.05em; color: #334155;'">OpenAI</div>
                                <div style="font-size: 10px; color: #94a3b8; margin-top: 3px; font-family: monospace;">GPT-4o · Premium</div>
                            </div>
                        </label>

                    </div>
                </div>

                {{-- Campo clave API --}}
                <div>
                    <p style="font-size: 10px; font-weight: 900; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.12em; margin-bottom: 10px;">Clave de API (API Key)</p>
                    <div style="position: relative;">
                        <input
                            x-show="!showKey"
                            type="password"
                            wire:model="apiKey"
                            placeholder="Pega aquí tu clave de API..."
                            style="width: 100%; background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 14px; padding: 14px 48px 14px 16px; font-size: 14px; font-family: monospace; color: #0f172a; outline: none; box-sizing: border-box; transition: border-color 0.2s;"
                            onfocus="this.style.borderColor='#7c3aed'"
                            onblur="this.style.borderColor='#e2e8f0'"
                            autocomplete="off"
                            spellcheck="false"
                        >
                        <input
                            x-show="showKey"
                            type="text"
                            wire:model="apiKey"
                            placeholder="Pega aquí tu clave de API..."
                            style="width: 100%; background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 14px; padding: 14px 48px 14px 16px; font-size: 14px; font-family: monospace; color: #0f172a; outline: none; box-sizing: border-box; transition: border-color 0.2s;"
                            onfocus="this.style.borderColor='#7c3aed'"
                            onblur="this.style.borderColor='#e2e8f0'"
                            autocomplete="off"
                            spellcheck="false"
                        >
                        <button
                            type="button"
                            @click="showKey = !showKey"
                            style="position: absolute; right: 14px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; padding: 4px; color: #94a3b8;"
                        >
                            <svg x-show="!showKey" style="height: 18px; width: 18px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg x-show="showKey" style="height: 18px; width: 18px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                            </svg>
                        </button>
                    </div>
                    <p style="font-size: 10px; color: #94a3b8; margin-top: 6px; margin-left: 4px;">
                        @if($apiProvider === 'groq') Obtén tu clave en <strong style="color: #7c3aed;">console.groq.com/keys</strong>
                        @elseif($apiProvider === 'gemini') Obtén tu clave en <strong style="color: #7c3aed;">aistudio.google.com/apikey</strong>
                        @elseif($apiProvider === 'openai') Obtén tu clave en <strong style="color: #7c3aed;">platform.openai.com/api-keys</strong>
                        @endif
                    </p>
                </div>

                {{-- Botón guardar --}}
                <button
                    type="submit"
                    onclick="window.playAudio('success');"
                    wire:loading.attr="disabled"
                    style="width: 100%; padding: 16px; background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%); color: white; font-size: 13px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.1em; border: none; border-radius: 14px; cursor: pointer; box-shadow: 0 8px 24px rgba(124,58,237,0.25); transition: all 0.2s; display: flex; align-items: center; justify-content: center; gap: 8px;"
                    onmouseover="this.style.background='linear-gradient(135deg, #6d28d9 0%, #5b21b6 100%)'"
                    onmouseout="this.style.background='linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%)'"
                >
                    <svg wire:loading.remove style="height: 16px; width: 16px;" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    <svg wire:loading style="height: 16px; width: 16px; animation: spin 1s linear infinite;" fill="none" viewBox="0 0 24 24">
                        <circle style="opacity: 0.25;" cx="12" cy="12" r="10" stroke="white" stroke-width="4"></circle>
                        <path style="opacity: 0.75;" fill="white" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span wire:loading.remove>Guardar Configuración</span>
                    <span wire:loading>Guardando...</span>
                </button>
            </form>
        </div>

        {{-- Guía de proveedores --}}
        <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 24px; padding: 24px; box-shadow: 0 2px 12px rgba(0,0,0,0.04);">
            <h3 style="font-size: 11px; font-weight: 900; color: #1e293b; text-transform: uppercase; letter-spacing: 0.1em; margin: 0 0 16px 0; font-family: 'Outfit', sans-serif;">Guía de Proveedores</h3>
            <div style="display: flex; flex-direction: column; gap: 10px;">
                <div style="display: flex; align-items: flex-start; gap: 12px; background: #f8fafc; border: 1px solid #f1f5f9; border-radius: 14px; padding: 14px;">
                    <span style="font-size: 20px; flex-shrink: 0;">⚡</span>
                    <div>
                        <p style="font-size: 12px; font-weight: 900; color: #1e293b; margin: 0 0 3px 0;">Groq <span style="background: #f5f3ff; color: #7c3aed; font-size: 9px; font-weight: 900; padding: 2px 6px; border-radius: 20px; margin-left: 4px; letter-spacing: 0.05em; text-transform: uppercase;">RECOMENDADO</span></p>
                        <p style="font-size: 10px; color: #64748b; margin: 0; line-height: 1.6;">Extremadamente rápido y gratuito. Modelos Llama de Meta. Ideal para producción. → <strong>console.groq.com/keys</strong></p>
                    </div>
                </div>
                <div style="display: flex; align-items: flex-start; gap: 12px; background: #f8fafc; border: 1px solid #f1f5f9; border-radius: 14px; padding: 14px;">
                    <span style="font-size: 20px; flex-shrink: 0;">✨</span>
                    <div>
                        <p style="font-size: 12px; font-weight: 900; color: #1e293b; margin: 0 0 3px 0;">Google Gemini</p>
                        <p style="font-size: 10px; color: #64748b; margin: 0; line-height: 1.6;">Gran capacidad de contexto, soporta imágenes. Clave gratuita → <strong>aistudio.google.com/apikey</strong></p>
                    </div>
                </div>
                <div style="display: flex; align-items: flex-start; gap: 12px; background: #f8fafc; border: 1px solid #f1f5f9; border-radius: 14px; padding: 14px;">
                    <span style="font-size: 20px; flex-shrink: 0;">🤖</span>
                    <div>
                        <p style="font-size: 12px; font-weight: 900; color: #1e293b; margin: 0 0 3px 0;">OpenAI (GPT-4o)</p>
                        <p style="font-size: 10px; color: #64748b; margin: 0; line-height: 1.6;">El modelo más reconocido. Requiere cuenta de pago → <strong>platform.openai.com/api-keys</strong></p>
                    </div>
                </div>
            </div>
        </div>

    </div>
    @endif

</div>
