<?php
$dir = __DIR__ . '/icons';
if (!is_dir($dir)) mkdir($dir, 0755, true);

foreach ([192, 512] as $size) {
    $file = $dir . '/icon-' . $size . '.png';
    if (!file_exists($file)) {
        $img = imagecreatetruecolor($size, $size);
        $bg  = imagecolorallocate($img, 79, 70, 229);
        $fg  = imagecolorallocate($img, 255, 255, 255);
        imagefilledellipse($img, $size / 2, $size / 2, $size, $size, $bg);
        $font = 5;
        $x = (int)(($size - imagefontwidth($font)) / 2);
        $y = (int)(($size - imagefontheight($font)) / 2);
        imagestring($img, $font, $x, $y, '$', $fg);
        imagepng($img, $file);
        imagedestroy($img);
    }
}

echo "OK";
