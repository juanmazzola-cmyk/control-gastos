<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class IconController extends Controller
{
    public function __invoke(int $size): Response
    {
        $img = imagecreatetruecolor($size, $size);
        $bg  = imagecolorallocate($img, 79, 70, 229);
        $fg  = imagecolorallocate($img, 255, 255, 255);
        imagefilledellipse($img, $size / 2, $size / 2, $size, $size, $bg);
        $font = 5;
        $x = (int)(($size - imagefontwidth($font)) / 2);
        $y = (int)(($size - imagefontheight($font)) / 2);
        imagestring($img, $font, $x, $y, '$', $fg);
        ob_start();
        imagepng($img);
        imagedestroy($img);
        $png = ob_get_clean();

        return response($png, 200)
            ->header('Content-Type', 'image/png')
            ->header('Cache-Control', 'public, max-age=31536000');
    }
}
