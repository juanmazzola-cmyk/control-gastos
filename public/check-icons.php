<?php
$secret = 'gastos2026';
if (($_GET['key'] ?? '') !== $secret) die('No autorizado.');

$dir = __DIR__ . '/icons';
echo "Directorio: $dir\n";
echo "Existe: " . (is_dir($dir) ? 'SI' : 'NO') . "\n\n";

if (is_dir($dir)) {
    $files = scandir($dir);
    foreach ($files as $f) {
        if ($f === '.' || $f === '..') continue;
        $path = $dir . '/' . $f;
        echo "$f — " . filesize($path) . " bytes\n";
    }
} else {
    echo "Creando directorio y generando íconos...\n";
    mkdir($dir, 0755, true);

    function makeIcon(int $size, string $path): void {
        $img = imagecreatetruecolor($size, $size);
        $bg = imagecolorallocate($img, 79, 70, 229);
        $fg = imagecolorallocate($img, 255, 255, 255);
        imagefilledellipse($img, $size/2, $size/2, $size, $size, $bg);
        $font = 5;
        $text = '$';
        $tw = imagefontwidth($font) * strlen($text);
        $th = imagefontheight($font);
        $x = (int)(($size - $tw) / 2);
        $y = (int)(($size - $th) / 2);
        imagestring($img, $font, $x, $y, $text, $fg);
        imagepng($img, $path);
        imagedestroy($img);
    }

    makeIcon(192, $dir . '/icon-192.png');
    makeIcon(512, $dir . '/icon-512.png');
    echo "Generados!\n";
}
