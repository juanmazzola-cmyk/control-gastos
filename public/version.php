<?php
$dir = __DIR__ . '/icons';
echo "dir: $dir\n";
echo "dir existe: " . (is_dir($dir) ? 'SI' : 'NO') . "\n";

if (!is_dir($dir)) {
    $ok = mkdir($dir, 0755, true);
    echo "mkdir: " . ($ok ? 'OK' : 'FALLO') . "\n";
}

foreach ([192, 512] as $size) {
    $file = $dir . '/icon-' . $size . '.png';
    echo "\n--- icon-$size ---\n";
    echo "path: $file\n";
    echo "existe: " . (file_exists($file) ? 'SI (' . filesize($file) . ' bytes)' : 'NO') . "\n";

    $img = imagecreatetruecolor($size, $size);
    $bg  = imagecolorallocate($img, 79, 70, 229);
    $fg  = imagecolorallocate($img, 255, 255, 255);
    imagefilledellipse($img, $size / 2, $size / 2, $size, $size, $bg);
    imagestring($img, 5, (int)(($size - imagefontwidth(5)) / 2), (int)(($size - imagefontheight(5)) / 2), '$', $fg);
    $result = imagepng($img, $file);
    imagedestroy($img);
    echo "imagepng: " . ($result ? 'OK' : 'FALLO') . "\n";
    echo "existe ahora: " . (file_exists($file) ? 'SI (' . filesize($file) . ' bytes)' : 'NO') . "\n";
    echo "permisos: " . decoct(fileperms($file) & 0777) . "\n";
}
