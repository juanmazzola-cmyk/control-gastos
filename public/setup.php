<?php
// Archivo temporal para correr migraciones - BORRAR DESPUÉS DE USAR
$secret = 'gastos2026';

if (($_GET['key'] ?? '') !== $secret) {
    die('No autorizado.');
}

define('LARAVEL_START', microtime(true));
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

echo '<pre>';
echo "=== Corriendo migraciones ===\n";
$kernel->call('migrate', ['--force' => true]);
echo $kernel->output();

echo "\n=== Limpiando caché ===\n";
$kernel->call('config:clear');
echo $kernel->output();

$kernel->call('view:clear');
echo $kernel->output();

echo "\n=== LISTO - Borrá este archivo ahora ===\n";
echo '</pre>';
