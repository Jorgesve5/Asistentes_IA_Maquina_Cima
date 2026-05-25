<div class="relative z-50">
    @php
        $isLight = true;
    @endphp
    <header wire:poll.3s="updateAlerts" class="border-b transition-all duration-300 {{ $isLight ? 'border-slate-200/80 bg-white/80 backdrop-blur-md' : 'border-white/5 bg-[#070b14]/90 backdrop-blur-xl' }}">
        <div class="max-w-[1400px] mx-auto px-6 py-3.5 flex items-center justify-between gap-4">

            <!-- Brand -->
            <a href="/" class="flex items-center gap-3 flex-shrink-0 group">
                <div class="h-8 w-8 bg-gradient-to-br transition-colors duration-300 {{ $isLight ? 'from-cyan-100 to-cyan-200/30 border-cyan-300/60' : 'from-cyan-500/30 to-cyan-600/10 border-cyan-500/40' }} border rounded-xl flex items-center justify-center shadow-[0_0_12px_rgba(34,211,238,0.15)] group-hover:border-cyan-400/60">
                    <svg class="h-4 w-4 {{ $isLight ? 'text-cyan-600' : 'text-cyan-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <rect x="4" y="4" width="16" height="16" rx="2" />
                        <path d="M9 9h6v6H9z" />
                        <path d="M9 1v3M15 1v3M9 20v3M15 20v3M20 9h3M20 15h3M1 9h3M1 15h3" />
                    </svg>
                </div>
                <div>
                    <span class="text-sm font-black tracking-[0.12em] transition-colors {{ $isLight ? 'text-slate-800 group-hover:text-cyan-600' : 'text-white group-hover:text-cyan-400' }} uppercase">ARANCALO</span>
                    <div class="flex items-center gap-1.5 mt-0.5">
                        <div class="h-1.5 w-1.5 rounded-full bg-emerald-400 animate-pulse"></div>
                        <span class="text-[11px] font-mono tracking-widest {{ $isLight ? 'text-slate-500' : 'text-slate-500' }}">Sistema Online</span>
                    </div>
                </div>
            </a>

            <!-- Navigation Links -->
            <div class="flex items-center gap-4 sm:gap-6">
                <a href="/" class="text-xs font-bold uppercase tracking-wider transition-all duration-200 py-1 px-2.5 rounded-lg {{ request()->is('/') ? ($isLight ? 'text-cyan-600 bg-cyan-50' : 'text-cyan-400 bg-white/5') : ($isLight ? 'text-slate-500 hover:text-slate-800 hover:bg-slate-50' : 'text-slate-400 hover:text-white hover:bg-white/5') }}">
                    Planta
                </a>
                <a href="/manuals" class="text-xs font-bold uppercase tracking-wider transition-all duration-200 py-1 px-2.5 rounded-lg {{ request()->is('manuals') ? ($isLight ? 'text-cyan-600 bg-cyan-50' : 'text-cyan-400 bg-white/5') : ($isLight ? 'text-slate-500 hover:text-slate-800 hover:bg-slate-50' : 'text-slate-400 hover:text-white hover:bg-white/5') }}">
                    Manuales
                </a>
            </div>

            <!-- Right buttons -->
            <div class="flex items-center gap-2">
                
                <!-- Help -->
                <button
                    wire:click="$set('showHelp', true)"
                    onclick="window.playAudio('click');"
                    class="flex items-center gap-2 px-3.5 py-2 rounded-xl transition-all duration-200 group border
                      {{ $isLight ? 'bg-slate-100/60 border-slate-200 hover:border-cyan-500/30 hover:bg-cyan-50/50' : 'bg-white/3 border-white/8 hover:border-cyan-500/30 hover:bg-cyan-500/5' }}"
                >
                    <svg class="h-4 w-4 transition-colors {{ $isLight ? 'text-slate-500 group-hover:text-cyan-600' : 'text-slate-500 group-hover:text-cyan-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10" />
                        <path d="M9.09 9a3 3 0 015.83 1c0 2-3 3-3 3M12 17h.01" />
                    </svg>
                    <span class="text-[11px] font-bold tracking-wider uppercase hidden sm:block transition-colors {{ $isLight ? 'text-slate-500 group-hover:text-cyan-600' : 'text-slate-500 group-hover:text-cyan-400' }}">Ayuda</span>
                </button>

                <!-- Bell Alerts dropdown -->
                <div class="relative">
                    <button
                        wire:click="$toggle('showAlerts')"
                        onclick="window.playAudio('click');"
                        class="relative flex items-center justify-center h-9 w-9 rounded-xl border transition-all duration-200
                          {{ $unreadCount > 0 ? 'bg-red-500/10 border-red-500/30 hover:border-red-500/50' : ($isLight ? 'bg-slate-100/60 border-slate-200 hover:border-slate-300' : 'bg-white/3 border-white/8 hover:border-white/15') }}"
                    >
                        <svg class="h-4 w-4 {{ $unreadCount > 0 ? 'text-red-400' : 'text-slate-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 01-3.46 0" />
                        </svg>
                        @if($unreadCount > 0)
                            <span class="absolute -top-1 -right-1 h-4 w-4 bg-red-500 text-white text-[9px] font-black rounded-full flex items-center justify-center shadow-[0_0_8px_rgba(239,68,68,0.6)]">
                                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                            </span>
                        @endif
                    </button>

                    <!-- Alert Dropdown -->
                    @if($showAlerts)
                        <div class="absolute top-full right-0 mt-2 w-80 rounded-2xl shadow-2xl overflow-hidden z-50 border transition-all duration-300
                          {{ $isLight ? 'bg-white border-slate-200 text-slate-800' : 'bg-[#0d1117] border-white/8 text-white' }}">
                            <div class="flex items-center justify-between px-4 py-3 border-b {{ $isLight ? 'border-slate-100' : 'border-white/5' }}">
                                <span class="text-xs font-black tracking-wider uppercase {{ $isLight ? 'text-slate-900' : 'text-white' }}">Alertas del Sistema</span>
                                @if($alerts->count() > 0)
                                    <button wire:click="clearAlerts" class="text-[10px] font-bold tracking-wide transition-colors {{ $isLight ? 'text-slate-400 hover:text-red-500' : 'text-slate-500 hover:text-red-400' }}">
                                        Limpiar
                                    </button>
                                @endif
                            </div>
                            <div class="max-h-72 overflow-y-auto">
                                @if($alerts->isEmpty())
                                    <div class="py-8 text-center">
                                        <svg class="h-8 w-8 text-slate-400 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <p class="text-xs text-slate-400 font-mono">Sin alertas activas</p>
                                    </div>
                                @else
                                    @foreach($alerts as $a)
                                        <div
                                            wire:click="markAlertRead('{{ $a->id }}')"
                                            class="flex items-start gap-3 px-4 py-3 border-b cursor-pointer transition-colors 
                                              {{ $isLight ? 'border-slate-100' : 'border-white/4' }} 
                                              {{ $a->read ? 'opacity-50' : ($isLight ? 'hover:bg-slate-50' : 'hover:bg-white/3') }}"
                                        >
                                            <!-- Alert status icon -->
                                            @if($a->type === 'warning')
                                                <svg class="h-4 w-4 text-red-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                </svg>
                                            @elseif($a->type === 'maintenance')
                                                <svg class="h-4 w-4 text-orange-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                                    <circle cx="12" cy="12" r="3" />
                                                </svg>
                                            @elseif($a->type === 'waiting')
                                                <svg class="h-4 w-4 text-amber-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            @else
                                                <svg class="h-4 w-4 text-cyan-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            @endif
                                            <div class="flex-1 min-w-0">
                                                <p class="text-xs leading-snug font-medium {{ $isLight ? 'text-slate-800' : 'text-slate-200' }}">{{ $a->message }}</p>
                                                <p class="text-[10px] font-mono mt-0.5 {{ $isLight ? 'text-slate-400' : 'text-slate-600' }}">{{ $a->timestamp }}</p>
                                            </div>
                                            @if(!$a->read)
                                                <div class="h-2 w-2 rounded-full bg-red-500 flex-shrink-0 mt-1"></div>
                                            @endif
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Admin panel link -->
                <a
                    href="/admin"
                    onclick="window.playAudio('click');"
                    class="group flex items-center gap-2 px-4 py-2 rounded-xl transition-all duration-300 border
                      {{ $isLight ? 'bg-slate-100/60 border-slate-200 hover:border-cyan-500/40 hover:bg-cyan-50/50' : 'bg-white/3 border-white/8 hover:border-cyan-500/40 hover:bg-cyan-500/5' }}"
                >
                    <svg class="h-3.5 w-3.5 transition-colors {{ $isLight ? 'text-slate-500 group-hover:text-cyan-600' : 'text-slate-500 group-hover:text-cyan-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    <span class="text-[10px] font-bold tracking-[0.15em] uppercase transition-colors hidden sm:block {{ $isLight ? 'text-slate-500 group-hover:text-cyan-600' : 'text-slate-500 group-hover:text-cyan-400' }}">Admin</span>
                </a>
            </div>
        </div>
    </header>

    <!-- ── Help Dialog Modal ── -->
    @if($showHelp)
        <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4 z-[100]">
            <div class="w-full max-w-md rounded-3xl p-6 shadow-2xl relative overflow-hidden border transition-all duration-300
              {{ $isLight ? 'bg-white border-slate-200 text-slate-800' : 'bg-[#0d1117] border-white/8 text-white' }}">
                <button
                    wire:click="$set('showHelp', false)"
                    class="absolute top-4 right-4 transition-colors {{ $isLight ? 'text-slate-400 hover:text-slate-850' : 'text-slate-500 hover:text-white' }}"
                >
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <div class="flex items-center gap-3 mb-4">
                    <div class="h-10 w-10 bg-cyan-500/10 border border-cyan-500/30 rounded-2xl flex items-center justify-center">
                        <svg class="h-5 w-5 text-cyan-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-base font-black uppercase tracking-wider {{ $isLight ? 'text-slate-900' : 'text-white' }}">Centro de Asistencia</h3>
                </div>
                <div class="space-y-3.5 text-xs font-mono {{ $isLight ? 'text-slate-600' : 'text-slate-400' }}">
                    <p>Bienvenido al portal SCADA de Arancalo.</p>
                    <p>• Selecciona cualquier máquina para consultar su manual técnico e interactuar con el asistente virtual IA.</p>
                    <p>• Los códigos de color indican:</p>
                    <div class="grid grid-cols-2 gap-2 pl-2">
                        <div class="flex items-center gap-1.5"><div class="h-2 w-2 rounded-full bg-emerald-500"></div><span>Operativa</span></div>
                        <div class="flex items-center gap-1.5"><div class="h-2 w-2 rounded-full bg-orange-500"></div><span>Mantenimiento</span></div>
                        <div class="flex items-center gap-1.5"><div class="h-2 w-2 rounded-full bg-amber-500"></div><span>En Espera</span></div>
                        <div class="flex items-center gap-1.5"><div class="h-2 w-2 rounded-full bg-red-500"></div><span>Avería</span></div>
                    </div>
                    <p class="pt-2 text-[10px] border-t {{ $isLight ? 'border-slate-100 text-slate-400' : 'border-white/5 text-slate-500' }}">ARANCALO-2026</p>
                </div>
            </div>
        </div>
    @endif
</div>
