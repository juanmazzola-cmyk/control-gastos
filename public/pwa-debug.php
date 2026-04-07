<?php
$secret = 'gastos2026';
if (($_GET['key'] ?? '') !== $secret) die('No autorizado.');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PWA Debug</title>
    <link rel="manifest" href="/manifest.json">
    <style>
        body { font-family: monospace; padding: 16px; background: #111; color: #0f0; }
        .ok { color: #0f0; } .fail { color: #f44; } .warn { color: #fa0; }
        h2 { color: #fff; margin-top: 24px; }
    </style>
</head>
<body>
<h2>PWA Debug</h2>
<div id="out"></div>

<script>
const log = (msg, type = '') => {
    const d = document.createElement('div');
    d.className = type;
    d.textContent = msg;
    document.getElementById('out').appendChild(d);
};

// 1. HTTPS
log('HTTPS: ' + (location.protocol === 'https:' ? '✓' : '✗ ' + location.protocol), location.protocol === 'https:' ? 'ok' : 'fail');

// 2. Display mode
const standalone = window.matchMedia('(display-mode: standalone)').matches;
log('Standalone ya instalado: ' + standalone, standalone ? 'warn' : 'ok');

// 3. Service Worker
if ('serviceWorker' in navigator) {
    log('ServiceWorker: soportado ✓', 'ok');
    navigator.serviceWorker.getRegistrations().then(regs => {
        if (regs.length === 0) {
            log('SW registrations: NINGUNA ✗', 'fail');
        } else {
            regs.forEach(r => log('SW registrado: ' + r.scope + ' — estado: ' + (r.active ? 'activo ✓' : r.installing ? 'instalando...' : 'esperando'), r.active ? 'ok' : 'warn'));
        }
    });
    navigator.serviceWorker.register('/sw.js').then(r => {
        log('SW register() OK ✓', 'ok');
    }).catch(e => {
        log('SW register() ERROR: ' + e.message, 'fail');
    });
} else {
    log('ServiceWorker: NO soportado ✗', 'fail');
}

// 4. Manifest
fetch('/manifest.json').then(r => {
    log('manifest.json HTTP: ' + r.status + (r.ok ? ' ✓' : ' ✗'), r.ok ? 'ok' : 'fail');
    return r.json();
}).then(m => {
    log('manifest name: ' + m.name, 'ok');
    log('manifest display: ' + m.display, m.display === 'standalone' ? 'ok' : 'fail');
    log('manifest start_url: ' + m.start_url, 'ok');
    log('manifest icons: ' + m.icons.length, m.icons.length >= 2 ? 'ok' : 'fail');
}).catch(e => log('manifest ERROR: ' + e, 'fail'));

// 5. Iconos
['/icons/icon.svg'].forEach(url => {
    fetch(url).then(r => log('Ícono ' + url + ': ' + r.status + (r.ok ? ' ✓' : ' ✗'), r.ok ? 'ok' : 'fail'))
              .catch(e => log('Ícono ' + url + ': ERROR ✗', 'fail'));
});

// 6. beforeinstallprompt
log('Esperando beforeinstallprompt...', 'warn');
window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    log('beforeinstallprompt: DISPARÓ ✓ ← TODO OK', 'ok');
});

window.addEventListener('appinstalled', () => log('appinstalled: DISPARÓ', 'ok'));
</script>
</body>
</html>
