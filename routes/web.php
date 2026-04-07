<?php

use App\Livewire\Dashboard;
use Illuminate\Support\Facades\Route;

Route::get('/', Dashboard::class);

Route::get('/icons/icon-{size}.png', function (int $size) {
    $img = imagecreatetruecolor($size, $size);
    $bg  = imagecolorallocate($img, 79, 70, 229);
    $fg  = imagecolorallocate($img, 255, 255, 255);
    imagefilledellipse($img, $size / 2, $size / 2, $size, $size, $bg);
    $font = 5;
    $text = '$';
    $x = (int)(($size - imagefontwidth($font)) / 2);
    $y = (int)(($size - imagefontheight($font)) / 2);
    imagestring($img, $font, $x, $y, $text, $fg);
    ob_start();
    imagepng($img);
    imagedestroy($img);
    $png = ob_get_clean();
    return response($png, 200)->header('Content-Type', 'image/png')->header('Cache-Control', 'public, max-age=31536000');
})->where('size', '192|512');
