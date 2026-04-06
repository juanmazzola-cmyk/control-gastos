<?php
// Script temporal para generar íconos de la PWA - BORRAR DESPUÉS
$secret = 'gastos2026';
if (($_GET['key'] ?? '') !== $secret) die('No autorizado.');

$dir = __DIR__ . '/icons';
if (!is_dir($dir)) mkdir($dir, 0755, true);

function makeIcon(int $size, string $path): void {
    $img = imagecreatetruecolor($size, $size);
    $bg = imagecolorallocate($img, 79, 70, 229);    // indigo-600
    $fg = imagecolorallocate($img, 255, 255, 255);  // blanco

    // Fondo redondeado (simulado con círculo)
    imagefilledellipse($img, $size/2, $size/2, $size, $size, $bg);

    // Símbolo $ centrado
    $fontSize = (int)($size * 0.45);
    $font = 5; // fuente built-in
    $text = '$';
    $tw = imagefontwidth($font) * strlen($text);
    $th = imagefontheight($font);
    // Escalar con imagettftext si hay fuentes, sino usar imagestring
    $x = (int)(($size - $tw) / 2);
    $y = (int)(($size - $th) / 2);
    imagestring($img, $font, $x, $y, $text, $fg);

    imagepng($img, $path);
    imagedestroy($img);
}

makeIcon(192, $dir . '/icon-192.png');
makeIcon(512, $dir . '/icon-512.png');

echo "Íconos generados en /icons/";
