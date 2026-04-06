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
    // Crear el archivo SQLite correctamente
    $dbPath = __DIR__ . '/../database/database.sqlite';
    echo "=== Preparando base de datos ===\n";
    echo "Ruta: $dbPath\n";

    $dbDir = dirname($dbPath);
    echo "Permisos directorio antes: " . decoct(fileperms($dbDir) & 0777) . "\n";
    chmod($dbDir, 0775);
    echo "Permisos directorio después: " . decoct(fileperms($dbDir) & 0777) . "\n";

    if (file_exists($dbPath)) {
        unlink($dbPath);
        echo "Archivo viejo eliminado.\n";
    }

    // Crear con SQLite3 directamente
    $db = new SQLite3($dbPath);
    $db->exec('PRAGMA journal_mode=DELETE;');
    $db->close();
    chmod($dbPath, 0664);
    echo "Archivo SQLite creado correctamente.\n";
    echo "Permisos archivo: " . decoct(fileperms($dbPath) & 0777) . "\n";
    echo "Owner proceso: " . get_current_user() . "\n";

    define('LARAVEL_START', microtime(true));
    require __DIR__ . '/../vendor/autoload.php';
    $app = require __DIR__ . '/../bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

    echo "\n=== Corriendo migraciones ===\n";
    $status = $kernel->call('migrate', ['--force' => true]);
    echo $kernel->output();
    echo "Exit code: $status\n";

    echo "\n=== Limpiando caché ===\n";
    $kernel->call('config:clear');
    echo $kernel->output();
    $kernel->call('view:clear');
    echo $kernel->output();

    echo "\n=== LISTO - Borrá este archivo ===\n";
} catch (\Throwable $e) {
    echo "\n=== ERROR ===\n";
    echo $e->getMessage() . "\n";
    echo "Archivo: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo '</pre>';
