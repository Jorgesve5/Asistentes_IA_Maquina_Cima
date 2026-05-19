<div wire:poll.3s="pollUpdates">
    <!-- ── Floating 5s Toast Notification Alert ── -->
    @if($toast)
        <div
            wire:key="{{ $toast['id'] }}"
            x-data="{ show: true }"
            x-show="show"
            x-init="setTimeout(() => { show = false; $wire.dismissToast(); }, 5000)"
            x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="translate-y-10 opacity-0"
            x-transition:enter-end="translate-y-0 opacity-100"
            x-transition:leave="transition ease-in duration-200 transform"
            x-transition:leave-start="translate-y-0 opacity-100"
            x-transition:leave-end="-translate-y-10 opacity-0"
            class="fixed bottom-6 right-6 z-50 w-full max-w-sm bg-[#0a0e17] border border-white/10 rounded-2xl p-4 shadow-2xl"
        >
            <div class="flex items-start gap-3">
                <!-- Icon per type -->
                @if($toast['type'] === 'warning')
                    <div class="p-1.5 bg-red-500/10 rounded-lg border border-red-500/20 text-red-400">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                @elseif($toast['type'] === 'maintenance')
                    <div class="p-1.5 bg-orange-500/10 rounded-lg border border-orange-500/20 text-orange-400">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        </svg>
                    </div>
                @elseif($toast['type'] === 'waiting')
                    <div class="p-1.5 bg-amber-500/10 rounded-lg border border-amber-500/20 text-amber-400">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                @else
                    <div class="p-1.5 bg-emerald-500/10 rounded-lg border border-emerald-500/20 text-emerald-400">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                @endif

                <div class="flex-1 min-w-0">
                    <p class="text-xs font-bold text-white uppercase tracking-wide">
                        {{ $toast['machineName'] }}
                    </p>
                    <p class="text-[11px] text-slate-400 leading-snug mt-0.5">
                        Ha cambiado a estado: <span class="font-bold text-white">{{ $toast['status'] }}</span>
                    </p>
                    <p class="text-[10px] text-slate-500 font-mono italic mt-1 border-t border-white/5 pt-1">
                        Motivo: {{ $toast['reason'] }}
                    </p>
                </div>
                <button
                    wire:click="dismissToast"
                    class="text-slate-500 hover:text-white transition-colors"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <!-- Progress bar -->
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-white/5 rounded-b-2xl overflow-hidden">
                <div class="h-full bg-cyan-400 animate-[progress_5s_linear]"></div>
            </div>
        </div>
    @endif
    
    <!-- Alpine.js helper to play sounds -->
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('play-sound', (event) => {
                if (window.playAudio) {
                    window.playAudio(event[0].type);
                }
            });
        });
    </script>
    
    <style>
        @keyframes progress {
            from { width: 100%; }
            to { width: 0%; }
        }
    </style>
</div>
