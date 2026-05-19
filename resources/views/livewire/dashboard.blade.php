<div wire:poll.3s class="flex-1 flex flex-col relative">

    <!-- Audio synthetics -->



    <!-- ── Counters Ribbon ── -->
    <div class="relative z-20 border-b border-white/5 bg-[#0a0e1a]/60 backdrop-blur-md">
        <div class="max-w-[1400px] mx-auto px-6 py-2.5 flex flex-wrap gap-4 items-center justify-between text-xs">
            <div class="flex items-center gap-2">
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Estado de Planta:</span>
            </div>
            <div class="flex flex-wrap items-center gap-3 sm:gap-6">
                <!-- Disponibles -->
                <div class="flex items-center gap-2 px-3 py-1 rounded-xl bg-emerald-500/5 border border-emerald-500/15">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                    <span class="text-slate-400 text-[11px] font-bold">Disponibles:</span>
                    <span class="text-white font-black text-xs">{{ $countOnline }}</span>
                </div>
                <!-- Mantenimiento -->
                <div class="flex items-center gap-2 px-3 py-1 rounded-xl bg-orange-500/5 border border-orange-500/15">
                    <span class="h-1.5 w-1.5 rounded-full bg-orange-400 animate-pulse"></span>
                    <span class="text-slate-400 text-[11px] font-bold">Mantenimiento:</span>
                    <span class="text-white font-black text-xs">{{ $countMaintenance }}</span>
                </div>
                <!-- En Espera -->
                <div class="flex items-center gap-2 px-3 py-1 rounded-xl bg-amber-500/5 border border-amber-500/15">
                    <span class="h-1.5 w-1.5 rounded-full bg-amber-400"></span>
                    <span class="text-slate-400 text-[11px] font-bold">En Espera:</span>
                    <span class="text-white font-black text-xs">{{ $countWaiting }}</span>
                </div>
                <!-- Avería -->
                <div class="flex items-center gap-2 px-3 py-1 rounded-xl bg-red-500/5 border border-red-500/15">
                    <span class="h-1.5 w-1.5 rounded-full bg-red-400 animate-bounce"></span>
                    <span class="text-slate-400 text-[11px] font-bold">Avería:</span>
                    <span class="text-white font-black text-xs">{{ $countWarning }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Main SCADA Grid ── -->
    <main class="relative z-10 flex-1 flex flex-col px-4 sm:px-8 py-12">
        <div class="text-center mb-14">
            <h1 class="text-5xl sm:text-6xl font-black tracking-tight text-white uppercase leading-none font-outfit">
                ZONA <span class="text-transparent bg-clip-text bg-gradient-to-r from-cyan-300 to-blue-400">MÁQUINAS</span>
            </h1>
            <p class="mt-4 text-[11px] text-slate-500 tracking-[0.3em] uppercase font-medium">
                Selecciona una unidad para acceder al asistente técnico
            </p>
        </div>

        <!-- 4-Column Grid -->
        <div class="max-w-[1280px] mx-auto w-full grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @foreach($columns as $colNum => $items)
                <div class="flex flex-col gap-5">
                    @foreach($items as $m)
                        @php
                            $statusConfigs = [
                                'online' => [
                                    'border' => 'border-emerald-500/40',
                                    'bg' => 'bg-gradient-to-br from-slate-900 via-slate-900 to-emerald-950/30',
                                    'badge' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/25',
                                    'badgeDot' => 'bg-emerald-400 shadow-[0_0_6px_rgba(52,211,153,0.8)]',
                                    'label' => 'Operativa'
                                ],
                                'maintenance' => [
                                    'border' => 'border-orange-500/40',
                                    'bg' => 'bg-gradient-to-br from-slate-900 via-slate-900 to-orange-950/30',
                                    'badge' => 'bg-orange-500/10 text-orange-400 border-orange-500/25',
                                    'badgeDot' => 'bg-orange-400 shadow-[0_0_6px_rgba(251,146,60,0.8)]',
                                    'label' => 'Mantenimiento'
                                ],
                                'waiting' => [
                                    'border' => 'border-amber-500/40',
                                    'bg' => 'bg-gradient-to-br from-slate-900 via-slate-900 to-amber-950/30',
                                    'badge' => 'bg-amber-500/10 text-amber-400 border-amber-500/25',
                                    'badgeDot' => 'bg-amber-400 shadow-[0_0_6px_rgba(251,191,36,0.8)]',
                                    'label' => 'En Espera'
                                ],
                                'warning' => [
                                    'border' => 'border-red-500/50',
                                    'bg' => 'bg-gradient-to-br from-slate-900 via-slate-900 to-red-950/40',
                                    'badge' => 'bg-red-500/10 text-red-400 border-red-500/25',
                                    'badgeDot' => 'bg-red-400 shadow-[0_0_8px_rgba(248,113,113,0.9)]',
                                    'label' => 'Avería'
                                ]
                            ];
                            $c = $statusConfigs[$m->status] ?? $statusConfigs['online'];
                        @endphp

                        <!-- Machine Card -->
                        <a
                            href="/machines/{{ $m->id }}"
                            onclick="window.playAudio('click');"
                            class="relative flex flex-col justify-between h-[190px] p-5 rounded-[22px] border {{ $c['border'] }} {{ $c['bg'] }} backdrop-blur-md shadow-2xl hover:scale-[1.02] hover:shadow-[0_0_20px_rgba(34,211,238,0.06)] transition-all duration-300 group cursor-pointer"
                        >
                            <!-- Top metadata -->
                            <div class="flex items-start justify-between">
                                <span class="text-[9px] font-mono text-slate-500 tracking-wider">
                                    {{ $m->serial ?: '—' }}
                                </span>
                                <div class="flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[9px] font-bold border {{ $c['badge'] }}">
                                    <div class="h-1.5 w-1.5 rounded-full {{ $c['badgeDot'] }}"></div>
                                    <span>{{ $c['label'] }}</span>
                                </div>
                            </div>

                            <!-- Mid title -->
                            <div class="my-auto">
                                <h3 class="text-sm font-black text-white tracking-wide uppercase leading-tight group-hover:text-cyan-400 transition-colors">
                                    {{ $m->name }}
                                </h3>
                                @if($m->indicator)
                                    <span class="inline-block mt-1 text-[9px] font-black font-mono text-cyan-400/70">
                                        {{ $m->indicator }}
                                    </span>
                                @endif
                            </div>

                            <!-- Bottom stats -->
                            <div class="border-t border-white/5 pt-3 mt-1 flex items-center justify-between">
                                <div class="min-w-0 flex-1">
                                    @if($m->status !== 'online' && $m->subLabel)
                                        <p class="text-[9px] font-black text-yellow-500/80 tracking-wider uppercase truncate">
                                            {{ $m->subLabel }}
                                        </p>
                                    @else
                                        <p class="text-[9px] text-slate-600 font-mono tracking-wider truncate">
                                            Sin incidencias activas
                                        </p>
                                    @endif
                                </div>
                                <div class="flex items-center text-xs font-bold text-cyan-400/50 group-hover:text-cyan-400 group-hover:translate-x-1 transition-all flex-shrink-0 ml-2">
                                    <span>Abrir</span>
                                    <svg class="h-3 w-3 ml-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                    </svg>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endforeach
        </div>
    </main>

    <!-- Footer SCADA footer -->



    <!-- Footer SCADA footer -->
    <footer class="relative z-10 py-6 border-t border-white/5 text-center bg-[#070b14]/50">
        <p class="text-[10px] font-mono text-slate-600 tracking-[0.2em] uppercase">
            CIMA CABLEADOS · Asistencia Técnica Inteligente
        </p>
    </footer>
</div>
