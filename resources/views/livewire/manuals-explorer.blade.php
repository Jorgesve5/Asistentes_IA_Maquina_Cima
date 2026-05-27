<div class="flex-1 flex flex-col relative px-4 sm:px-8 py-10 max-w-[1400px] mx-auto w-full">
    
    <!-- ── Header ── -->
    <div class="mb-10 text-center sm:text-left">
        <h1 class="text-4xl sm:text-5xl font-extrabold tracking-tight text-slate-900 font-outfit uppercase">
            Explorador de <span class="text-transparent bg-clip-text bg-gradient-to-r from-cyan-600 to-blue-600">Manuales y Recursos</span>
        </h1>
        <p class="mt-2 text-xs text-slate-400 font-bold uppercase tracking-widest">
            Buscador global de documentación técnica y archivos de soporte
        </p>
    </div>

    <!-- ── Filters Section ── -->
    <div class="bg-white/80 backdrop-blur-md border border-slate-200/80 rounded-3xl p-6 shadow-sm mb-8 transition-all duration-300">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <!-- Search Input -->
            <div class="relative">
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5 pl-1">Búsqueda rápida</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                    <input 
                        type="text" 
                        wire:model.live.debounce.300ms="search" 
                        placeholder="Buscar por nombre, texto..." 
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-10 pr-4 py-2.5 text-xs text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500 transition-all font-medium"
                    />
                </div>
            </div>

            <!-- Machine Filter -->
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5 pl-1">Máquina / Unidad</label>
                <select 
                    wire:model.live="machineId" 
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500 transition-all font-medium"
                >
                    <option value="">Todas las máquinas</option>
                    @foreach($machines as $m)
                        <option value="{{ $m->id }}">{{ $m->name }}</option>
                    @endforeach
                </select>
            </div>


            <!-- File Type Filter -->
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5 pl-1">Tipo de Archivo</label>
                <div class="flex gap-2">
                    <select 
                        wire:model.live="fileType" 
                        class="flex-1 bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500 transition-all font-medium"
                    >
                        <option value="">Cualquier tipo</option>
                        <option value="pdf">PDF</option>
                        <option value="image">Imágenes</option>
                        <option value="excel">Excel (.xlsx, .xls)</option>
                        <option value="word">Word (.docx, .doc)</option>
                        <option value="other">Otros formatos</option>
                    </select>

                    @if($search || $machineId || $fileType)
                        <button 
                            wire:click="clearFilters" 
                            class="px-3 bg-red-50 text-red-600 border border-red-100 rounded-xl text-xs font-bold hover:bg-red-100 transition-colors flex items-center justify-center"
                            title="Limpiar filtros"
                        >
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    @endif
                </div>
            </div>

        </div>
    </div>

    <!-- ── Manuals Grid ── -->
    <div class="flex-1">
        @if($manuals->isEmpty())
            <div class="bg-white/80 border border-slate-200/80 rounded-3xl p-16 text-center shadow-sm">
                <div class="h-16 w-16 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-slate-200">
                    <svg class="h-8 w-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <h3 class="text-base font-extrabold text-slate-700 uppercase tracking-wider">No se encontraron archivos</h3>
                <p class="text-xs text-slate-400 mt-2 max-w-md mx-auto">
                    Intenta cambiar la palabra de búsqueda o ajustar los filtros de máquina y tipo de archivo.
                </p>
                @if($search || $machineId || $fileType)
                    <button wire:click="clearFilters" class="mt-5 px-4 py-2 bg-cyan-600 hover:bg-cyan-700 text-white text-xs font-bold rounded-xl shadow-md transition-colors">
                        Restablecer Filtros
                    </button>
                @endif
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8">
                @foreach($manuals as $manual)
                    @php
                        // Color styling depending on file type
                        $typeColors = [
                            'pdf' => [
                                'bg' => 'bg-red-50 border-red-100',
                                'text' => 'text-red-600',
                                'icon' => '<svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" /><path stroke-linecap="round" stroke-linejoin="round" d="M9 9h1.5m-1.5 3H12m-3 3h4" /></svg>'
                            ],
                            'image' => [
                                'bg' => 'bg-purple-50 border-purple-100',
                                'text' => 'text-purple-600',
                                'icon' => '<svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>'
                            ],
                            'excel' => [
                                'bg' => 'bg-emerald-50 border-emerald-100',
                                'text' => 'text-emerald-600',
                                'icon' => '<svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>'
                            ],
                            'word' => [
                                'bg' => 'bg-blue-50 border-blue-100',
                                'text' => 'text-blue-600',
                                'icon' => '<svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>'
                            ],
                            'other' => [
                                'bg' => 'bg-slate-50 border-slate-100',
                                'text' => 'text-slate-600',
                                'icon' => '<svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>'
                            ]
                        ];
                        
                        $type = $manual->file_type ?? 'pdf';
                        $tc = $typeColors[$type] ?? $typeColors['other'];
                    @endphp

                    <!-- Manual Card -->
                    <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-sm hover:shadow-md hover:scale-[1.01] transition-all duration-200 flex flex-col justify-between">
                        <div>
                            <!-- Header Info -->
                                <div class="px-2 py-0.5 rounded-full border text-[9px] font-bold uppercase {{ $manual->in_chat ? 'bg-emerald-50 border-emerald-100 text-emerald-600' : 'bg-slate-50 border-slate-100 text-slate-400' }}">
                                    {{ $manual->in_chat ? 'RAG' : 'Offline' }}
                                </div>

                            <!-- Title & Icon -->
                            <div class="flex gap-3 mb-4">
                                <div class="h-10 w-10 flex-shrink-0 rounded-xl flex items-center justify-center border {{ $tc['bg'] }} {{ $tc['text'] }}">
                                    {!! $tc['icon'] !!}
                                </div>
                                <div class="min-w-0">
                                    <h3 class="text-xs font-black text-slate-800 leading-snug truncate uppercase" title="{{ $manual->fileName }}">
                                        {{ $manual->fileName }}
                                    </h3>
                                    <p class="text-[10px] text-slate-400 font-bold tracking-wide mt-0.5">
                                        {{ $manual->machine ? $manual->machine->name : 'General' }}
                                    </p>
                                </div>
                            </div>

                            <!-- Snippet search match if available -->
                            @if($search && !empty($manual->text))
                                @php
                                    $pos = mb_strpos(mb_strtolower($manual->text), mb_strtolower($search));
                                    $snippet = '';
                                    if ($pos !== false) {
                                        $start = max(0, $pos - 40);
                                        $snippet = '...' . mb_substr($manual->text, $start, 90) . '...';
                                    }
                                @endphp
                                @if($snippet)
                                    <div class="bg-slate-50 border border-slate-100 rounded-xl p-2.5 mb-4 text-[10px] font-medium font-mono text-slate-500 leading-normal break-all">
                                        {!! preg_replace('/(' . preg_quote($search, '/') . ')/i', '<strong class="text-cyan-600 bg-cyan-50 px-0.5 rounded">$1</strong>', e($snippet)) !!}
                                    </div>
                                @endif
                            @endif
                        </div>

                        <!-- Footer Actions -->
                        <div class="border-t border-slate-100 pt-3 mt-2 flex items-center justify-between text-[10px] text-slate-400">
                            <span class="font-mono">
                                @if(is_numeric($manual->size))
                                    {{ number_format($manual->size / 1024, 1) }} KB
                                @else
                                    {{ $manual->size }}
                                @endif
                            </span>

                            <div class="flex items-center gap-1.5">
                                @if($manual->file_path)
                                    <a 
                                        href="{{ asset('storage/' . $manual->file_path) }}" 
                                        download="{{ $manual->fileName }}" 
                                        onclick="window.playAudio('click'); event.stopPropagation();" 
                                        class="h-7 w-7 flex items-center justify-center rounded-lg bg-slate-100 border border-slate-200 text-slate-500 hover:bg-slate-200 hover:text-slate-700 transition-colors"
                                        title="Descargar archivo original"
                                    >
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                        </svg>
                                    </a>
                                @endif
                                <button 
                                    wire:click="openViewer('{{ $manual->id }}')" 
                                    onclick="window.playAudio('click');" 
                                    class="px-3 py-1.5 bg-cyan-50 border border-cyan-100 text-cyan-700 font-bold hover:bg-cyan-100 rounded-lg transition-colors flex items-center gap-1"
                                >
                                    <span>Visualizar</span>
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $manuals->links() }}
            </div>
        @endif
    </div>

    <!-- ── Reusable Document Visor Modal ── -->
    @if($showViewerModal && $viewingManual)
        <div
            class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4 z-[100]"
            wire:click.self="closeViewer"
        >
            <div class="w-full max-w-5xl h-[85vh] bg-white border border-slate-200 rounded-3xl shadow-2xl flex flex-col overflow-hidden animate-in fade-in zoom-in-95 duration-200">
                
                <!-- Modal Header -->
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 bg-slate-50 flex-shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="h-8 w-8 bg-cyan-50 border border-cyan-100 rounded-lg flex items-center justify-center text-cyan-600 flex-shrink-0 shadow-sm">
                            <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-sm font-black text-slate-800 truncate uppercase max-w-xs sm:max-w-md" title="{{ $viewingManual->fileName }}">
                                {{ $viewingManual->fileName }}
                            </h3>
                            <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wider mt-0.5">
                                {{ $viewingManual->machine ? $viewingManual->machine->name : 'General' }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        @if($viewingManual->file_path)
                            <a 
                                href="{{ asset('storage/' . $viewingManual->file_path) }}" 
                                download="{{ $viewingManual->fileName }}" 
                                onclick="window.playAudio('click');" 
                                class="flex items-center gap-1.5 bg-cyan-50 border border-cyan-100 hover:bg-cyan-100 text-cyan-700 px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all duration-200 shadow-sm"
                            >
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                <span class="hidden sm:inline">Descargar Original</span>
                            </a>
                        @endif
                        <button 
                            wire:click="closeViewer" 
                            class="text-slate-400 hover:text-slate-700 transition-colors"
                        >
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Modal Body (Visor) -->
                <div class="flex-1 bg-slate-100 overflow-hidden relative flex flex-col items-center justify-center">
                    @if($viewingManual->file_path)
                        @if($viewingManual->file_type === 'pdf')
                            <!-- PDF iframe Viewer -->
                            <iframe 
                                src="{{ asset('storage/' . $viewingManual->file_path) }}#toolbar=1&navpanes=0" 
                                class="w-full h-full border-none"
                            ></iframe>
                        @elseif($viewingManual->file_type === 'image')
                            <!-- Image Viewer -->
                            <div class="w-full h-full p-6 flex items-center justify-center overflow-auto bg-slate-50">
                                <img 
                                    src="{{ asset('storage/' . $viewingManual->file_path) }}" 
                                    alt="{{ $viewingManual->fileName }}" 
                                    class="max-w-full max-h-full object-contain rounded-lg border border-slate-200 shadow-lg"
                                />
                            </div>
                        @elseif($viewingManual->file_type === 'word' || $viewingManual->file_type === 'excel')
                            <!-- Word/Excel Text Preview -->
                            <div class="w-full h-full p-6 sm:p-8 overflow-y-auto flex flex-col bg-slate-50">
                                <div class="bg-amber-50 border border-amber-200 text-amber-800 p-4 rounded-2xl text-xs font-mono mb-6 leading-relaxed max-w-3xl mx-auto flex-shrink-0 flex items-start gap-3 shadow-sm">
                                    <svg class="h-5 w-5 flex-shrink-0 mt-0.5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    <div>
                                        <strong>Vista previa del contenido de texto extraído:</strong> Esta máquina y el chat IA utilizan el texto a continuación para entender el manual. Para conservar las tablas, imágenes y formateo original, por favor descargue el archivo completo presionando el botón superior.
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
                            <!-- Generic / Other file content viewer -->
                            <div class="w-full h-full p-6 sm:p-8 overflow-y-auto flex flex-col items-center justify-center bg-slate-50">
                                <div class="text-center max-w-md">
                                    <div class="h-16 w-16 bg-white border border-slate-200 rounded-2xl flex items-center justify-center text-slate-400 mx-auto mb-4 shadow-sm">
                                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                    <h4 class="text-sm font-black text-slate-800 uppercase tracking-wider">Formato no visualizable en navegador</h4>
                                    <p class="text-xs text-slate-550 text-slate-500 mt-2 mb-6">
                                        El tipo de archivo no admite previsualización interactiva directa en el visor.
                                    </p>
                                    <a 
                                        href="{{ asset('storage/' . $viewingManual->file_path) }}" 
                                        download="{{ $viewingManual->fileName }}" 
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
                        <!-- Legacy record text visor fallback -->
                        <div class="w-full h-full p-6 sm:p-8 overflow-y-auto flex flex-col bg-slate-50">
                            <div class="max-w-3xl mx-auto w-full bg-white border border-slate-200 rounded-2xl p-6 sm:p-8 font-mono text-xs text-slate-700 leading-relaxed overflow-y-auto break-words whitespace-pre-wrap shadow-sm">
                                {{ $viewingManual->text }}
                            </div>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    @endif

</div>
