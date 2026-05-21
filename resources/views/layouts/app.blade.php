<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Arancalo' }}</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;600;700&family=Outfit:wght@400;600;800;900&display=swap" rel="stylesheet">

    <!-- CSS & JS Assets (Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Livewire Styles -->
    @livewireStyles

    <!-- Web Audio API Synth Sound Generator -->
    <script>
        window.playAudio = function(type) {
            try {
                const AudioContext = window.AudioContext || window.webkitAudioContext;
                if (!AudioContext) return;
                const audioCtx = new AudioContext();
                const osc = audioCtx.createOscillator();
                const gain = audioCtx.createGain();
                osc.connect(gain);
                gain.connect(audioCtx.destination);
                if (type === 'click') {
                    osc.frequency.setValueAtTime(600, audioCtx.currentTime);
                    gain.gain.setValueAtTime(0.02, audioCtx.currentTime);
                    osc.start();
                    osc.stop(audioCtx.currentTime + 0.05);
                } else {
                    osc.frequency.setValueAtTime(523.25, audioCtx.currentTime);
                    osc.frequency.setValueAtTime(659.25, audioCtx.currentTime + 0.08);
                    gain.gain.setValueAtTime(0.03, audioCtx.currentTime);
                    osc.start();
                    osc.stop(audioCtx.currentTime + 0.2);
                }
            } catch (e) { console.log('Audio error', e); }
        }
    </script>
</head>
<body class="{{ $bodyClass ?? 'bg-slate-50 text-slate-900' }} font-sans min-h-screen relative overflow-x-hidden antialiased transition-colors duration-300">

    <!-- Grid Background -->
    <div class="fixed inset-0 pointer-events-none select-none z-0">
        <div class="absolute inset-0 bg-[linear-gradient(to_right,#00000003_1px,transparent_1px),linear-gradient(to_bottom,#00000003_1px,transparent_1px)] bg-[size:40px_40px]"></div>
        <div class="absolute top-[-80px] left-[15%] h-[500px] w-[700px] rounded-full bg-cyan-500/5 blur-[180px]"></div>
    </div>

    <!-- Main Content wrapper -->
    <div class="relative z-10 flex flex-col min-h-screen">
        <!-- Global Header with Notifications -->
        <livewire:global-header />

        {{ $slot }}
    </div>

    <!-- Global Toast Alerts -->
    <livewire:global-toast />

    <!-- Livewire Scripts -->
    @livewireScripts
</body>
</html>
