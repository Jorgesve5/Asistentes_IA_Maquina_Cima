<div class="flex-1 flex items-center justify-center p-4">
    <div class="w-full max-w-[400px] bg-[#0a0e17]/80 border border-white/5 rounded-3xl p-7 shadow-2xl backdrop-blur-md relative overflow-hidden">
        
        <!-- lock banner icon -->
        <div class="flex flex-col items-center mb-6">
            <div class="h-12 w-12 bg-cyan-500/10 border border-cyan-500/30 rounded-2xl flex items-center justify-center shadow-[0_0_12px_rgba(34,211,238,0.15)] mb-3">
                <svg class="h-5 w-5 text-cyan-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                    <path d="M7 11V7a5 5 0 0110 0v4" />
                </svg>
            </div>
            <h2 class="text-lg font-black text-white uppercase tracking-wider font-outfit">Control de Planta</h2>
            <p class="text-[10px] text-slate-500 font-mono tracking-widest uppercase mt-1">Solo Supervisores Autorizados</p>
        </div>

        <form wire:submit.prevent="login" class="space-y-4 font-mono text-xs">
            <div>
                <label class="block text-slate-400 mb-1.5 font-bold uppercase tracking-wider">Usuario Supervisor</label>
                <input
                    type="text"
                    wire:model="username"
                    required
                    placeholder="admin"
                    class="w-full bg-white/3 border border-white/10 rounded-xl px-4 py-2.5 text-white placeholder-slate-600 focus:outline-none focus:border-cyan-500/50 transition-colors"
                />
            </div>

            <div>
                <label class="block text-slate-400 mb-1.5 font-bold uppercase tracking-wider">Contraseña</label>
                <input
                    type="password"
                    wire:model="password"
                    required
                    placeholder="••••••••"
                    class="w-full bg-white/3 border border-white/10 rounded-xl px-4 py-2.5 text-white placeholder-slate-600 focus:outline-none focus:border-cyan-500/50 transition-colors"
                />
            </div>

            @if(session()->has('login_error'))
                <div class="text-[10px] font-mono text-red-400 leading-tight">
                    {{ session('login_error') }}
                </div>
            @endif

            <button
                type="submit"
                class="w-full py-3 bg-gradient-to-r from-cyan-500 to-blue-500 hover:from-cyan-400 hover:to-blue-400 text-slate-950 font-black uppercase tracking-widest rounded-xl shadow-lg shadow-cyan-500/10 hover:shadow-cyan-500/20 active:scale-95 transition-all flex items-center justify-center gap-2"
            >
                <span>Desbloquear Consola</span>
            </button>
        </form>

        <div class="mt-6 border-t border-white/5 pt-4 text-center">
            <a
                href="/"
                class="text-[10px] font-bold text-slate-500 hover:text-cyan-400 uppercase tracking-widest transition-colors"
            >
                ← Regresar a Planta
            </a>
        </div>
    </div>
</div>
