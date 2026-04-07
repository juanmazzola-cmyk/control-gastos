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
        imagestring($img, 5,
            (int)(($size - imagefontwidth(5)) / 2),
            (int)(($size - imagefontheight(5)) / 2),
            '$', $fg);
        ob_start();
        imagepng($img);
        imagedestroy($img);

        return response(ob_get_clean(), 200)
            ->header('Content-Type', 'image/png')
            ->header('Cache-Control', 'public, max-age=31536000');
    }
}
