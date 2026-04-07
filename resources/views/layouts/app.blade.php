<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Control de Gastos' }}</title>
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#4f46e5">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Gastos">
    <link rel="apple-touch-icon" href="/pwa-icon/192">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js');
        }

        let deferredPrompt = null;
        const isIOS = /iphone|ipad|ipod/i.test(navigator.userAgent);
        const isStandalone = window.matchMedia('(display-mode: standalone)').matches || navigator.standalone;

        // Si ya está instalada, ocultar banner
        if (isStandalone) {
            document.getElementById('pwa-install-banner').classList.add('hidden');
        } else if (isIOS) {
            document.getElementById('pwa-install-text').textContent = 'Instalá: Safari → compartir → "Agregar a inicio"';
            document.getElementById('pwa-install-btn').classList.add('hidden');
        }

        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
            document.getElementById('pwa-install-text').textContent = 'Instalá la app en tu celular';
            document.getElementById('pwa-install-btn').classList.remove('hidden');
            document.getElementById('pwa-install-btn').textContent = 'Instalar';
        });

        function installPWA() {
            if (!deferredPrompt) {
                alert('Chrome bloqueó el prompt automático.\n\nUsá el menú ⋮ → "Agregar a pantalla de inicio"');
                return;
            }
            deferredPrompt.prompt();
            deferredPrompt.userChoice.then(() => {
                deferredPrompt = null;
                document.getElementById('pwa-install-banner').classList.add('hidden');
            });
        }

        window.addEventListener('appinstalled', () => {
            document.getElementById('pwa-install-banner').classList.add('hidden');
        });
    </script>
</head>
<body class="bg-gray-50 min-h-screen">

    <header class="bg-white border-b border-gray-200 sticky top-0 z-10 shadow-sm">
        <div class="max-w-6xl mx-auto px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h1 class="text-lg font-semibold text-gray-900">Control de Gastos</h1>
            </div>
            <span class="text-sm text-gray-400">{{ now()->locale('es')->isoFormat('D [de] MMMM YYYY') }}</span>
        </div>
    </header>

    <div id="pwa-install-banner" class="bg-indigo-600 text-white px-4 py-3 flex items-center justify-between gap-3">
        <div class="flex items-center gap-2 text-sm">
            <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            <span id="pwa-install-text">Instalá la app en tu celular</span>
        </div>
        <button id="pwa-install-btn" onclick="installPWA()" class="bg-white text-indigo-600 text-sm font-semibold px-3 py-1 rounded-lg shrink-0">
            Instalar
        </button>
    </div>

    <main class="max-w-2xl mx-auto px-4 py-5 pb-24">
        {{ $slot }}
    </main>

    @livewireScripts
</body>
</html>
