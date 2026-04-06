<?php
// Archivo temporal para correr migraciones - BORRAR DESPUÉS DE USAR
ini_set('display_errors', 1);
error_reporting(E_ALL);

$secret = 'gastos2026';
if (($_GET['key'] ?? '') !== $secret) {
    die('No autorizado.');
}

echo '<pre>';

try {
    define('LARAVEL_START', microtime(true));
    require __DIR__ . '/../vendor/autoload.php';
    $app = require __DIR__ . '/../bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

    echo "=== Corriendo migraciones ===\n";
    $status = $kernel->call('migrate', ['--force' => true]);
    echo $kernel->output();
    echo "Exit code: $status\n";

    echo "\n=== Limpiando caché ===\n";
    $kernel->call('config:clear');
    echo $kernel->output();
    $kernel->call('view:clear');
    echo $kernel->output();

    echo "\n=== LISTO ===\n";
} catch (\Throwable $e) {
    echo "\n=== ERROR ===\n";
    echo $e->getMessage() . "\n";
    echo "Archivo: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo '</pre>';
