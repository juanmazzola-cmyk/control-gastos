<?php
$dir = $argv[1] ?? __DIR__ . '/public/icons';
if (!is_dir($dir)) mkdir($dir, 0755, true);

foreach ([192, 512] as $size) {
    $img = imagecreatetruecolor($size, $size);
    $bg  = imagecolorallocate($img, 79, 70, 229);
    $fg  = imagecolorallocate($img, 255, 255, 255);
    imagefilledellipse($img, $size / 2, $size / 2, $size, $size, $bg);
    $font = 5;
    $x = (int)(($size - imagefontwidth($font)) / 2);
    $y = (int)(($size - imagefontheight($font)) / 2);
    imagestring($img, $font, $x, $y, '$', $fg);
    imagepng($img, $dir . '/icon-' . $size . '.png');
    imagedestroy($img);
    echo "icon-{$size}.png OK\n";
}
