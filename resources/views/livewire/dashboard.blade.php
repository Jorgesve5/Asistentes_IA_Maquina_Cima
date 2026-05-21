<div wire:poll.3s class="flex-1 flex flex-col relative">

    <!-- ── Counters Ribbon ── -->
    <div class="relative z-20 border-b border-slate-200 bg-white/70 backdrop-blur-md">
        <div class="max-w-[1400px] mx-auto px-6 py-3 flex flex-wrap gap-4 items-center justify-between text-xs">
            <div class="flex items-center gap-2">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Estado de Planta:</span>
            </div>
            <div class="flex flex-wrap items-center gap-3 sm:gap-6">
                <!-- Disponibles -->
                <div class="flex items-center gap-2 px-3 py-1 rounded-xl bg-emerald-50 border border-emerald-100">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                    <span class="text-slate-500 text-[11px] font-medium">Disponibles:</span>
                    <span class="text-slate-800 font-extrabold text-xs">{{ $countOnline }}</span>
                </div>
                <!-- Mantenimiento -->
                <div class="flex items-center gap-2 px-3 py-1 rounded-xl bg-orange-50 border border-orange-100">
                    <span class="h-1.5 w-1.5 rounded-full bg-orange-500 animate-pulse"></span>
                    <span class="text-slate-500 text-[11px] font-medium">Mantenimiento:</span>
                    <span class="text-slate-800 font-extrabold text-xs">{{ $countMaintenance }}</span>
                </div>
                <!-- En Espera -->
                <div class="flex items-center gap-2 px-3 py-1 rounded-xl bg-amber-50 border border-amber-100">
                    <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                    <span class="text-slate-500 text-[11px] font-medium">En Espera:</span>
                    <span class="text-slate-800 font-extrabold text-xs">{{ $countWaiting }}</span>
                </div>
                <!-- Avería -->
                <div class="flex items-center gap-2 px-3 py-1 rounded-xl bg-red-50 border border-red-100">
                    <span class="h-1.5 w-1.5 rounded-full bg-red-500 animate-bounce"></span>
                    <span class="text-slate-500 text-[11px] font-medium">Avería:</span>
                    <span class="text-slate-800 font-extrabold text-xs">{{ $countWarning }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Main SCADA Grid ── -->
    <main class="relative z-10 flex-1 flex flex-col px-4 sm:px-8 py-14">
        <div class="text-center mb-16">
            <h1 class="text-6xl sm:text-7xl font-extrabold tracking-tight text-slate-900 uppercase leading-none font-outfit">
                ZONA <span class="text-transparent bg-clip-text bg-gradient-to-r from-cyan-600 to-blue-600">MÁQUINAS</span>
            </h1>
            <p class="mt-4 text-[11px] text-slate-400 tracking-[0.3em] uppercase font-bold">
                Selecciona una unidad para acceder al asistente técnico
            </p>
        </div>

        <!-- 4-Column Grid -->
        <div class="max-w-[1280px] mx-auto w-full grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($columns as $colNum => $items)
                <div class="flex flex-col gap-6">
                    @foreach($items as $m)
                        @php
                            $statusConfigs = [
                                'online' => [
                                    'border' => 'border-slate-200/80',
                                    'bg' => 'bg-white hover:border-emerald-300',
                                    'badge' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                    'badgeDot' => 'bg-emerald-500 shadow-[0_0_6px_rgba(16,185,129,0.2)]',
                                    'label' => 'Operativa'
                                ],
                                'maintenance' => [
                                    'border' => 'border-slate-200/80',
                                    'bg' => 'bg-white hover:border-orange-300',
                                    'badge' => 'bg-orange-50 text-orange-700 border-orange-100',
                                    'badgeDot' => 'bg-orange-500 shadow-[0_0_6px_rgba(249,115,22,0.2)]',
                                    'label' => 'Mantenimiento'
                                ],
                                'waiting' => [
                                    'border' => 'border-slate-200/80',
                                    'bg' => 'bg-white hover:border-amber-300',
                                    'badge' => 'bg-amber-50 text-amber-700 border-amber-100',
                                    'badgeDot' => 'bg-amber-500 shadow-[0_0_6px_rgba(245,158,11,0.2)]',
                                    'label' => 'En Espera'
                                ],
                                'warning' => [
                                    'border' => 'border-slate-200/80',
                                    'bg' => 'bg-white hover:border-red-300',
                                    'badge' => 'bg-red-50 text-red-700 border-red-100',
                                    'badgeDot' => 'bg-red-500 shadow-[0_0_8px_rgba(239,68,68,0.25)]',
                                    'label' => 'Avería'
                                ]
                            ];
                            $c = $statusConfigs[$m->status] ?? $statusConfigs['online'];
                        @endphp

                        <!-- Machine Card -->
                        <a
                            href="/machines/{{ $m->id }}"
                            onclick="window.playAudio('click');"
                            class="relative flex flex-col justify-between h-[180px] p-5 rounded-[22px] border {{ $c['border'] }} {{ $c['bg'] }} shadow-sm hover:scale-[1.02] hover:shadow-md transition-all duration-300 group cursor-pointer"
                        >
                            <!-- Top metadata (Only badge on the right, serial number removed!) -->
                            <div class="flex items-start justify-end">
                                <div class="flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[9px] font-bold border {{ $c['badge'] }}">
                                    <div class="h-1.5 w-1.5 rounded-full {{ $c['badgeDot'] }}"></div>
                                    <span>{{ $c['label'] }}</span>
                                </div>
                            </div>

                            <!-- Mid title -->
                            <div class="my-auto">
                                <h3 class="text-sm font-extrabold text-slate-800 tracking-wide uppercase leading-tight group-hover:text-cyan-600 transition-colors">
                                    {{ $m->name }}
                                </h3>
                            </div>

                            <!-- Bottom stats -->
                            <div class="border-t border-slate-100 pt-3 mt-1 flex items-center justify-between">
                                <div class="min-w-0 flex-1">
                                    @if($m->status !== 'online' && $m->subLabel)
                                        <p class="text-[9px] font-bold text-orange-600 tracking-wider uppercase truncate">
                                            {{ $m->subLabel }}
                                        </p>
                                    @else
                                        <p class="text-[9px] text-slate-400 font-mono tracking-wider truncate">
                                            Sin incidencias activas
                                        </p>
                                    @endif
                                </div>
                                <div class="flex items-center text-xs font-bold text-cyan-600 group-hover:text-cyan-700 group-hover:translate-x-0.5 transition-all flex-shrink-0 ml-2">
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

    <!-- Footer -->
    <footer class="relative z-10 py-6 border-t border-slate-200 text-center bg-white/50">
        <p class="text-[10px] font-mono text-slate-400 tracking-[0.2em] uppercase">
            ARANCALO · Asistencia Técnica Inteligente
        </p>
    </footer>
</div>
