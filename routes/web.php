<?php

use App\Http\Controllers\IconController;
use App\Livewire\Dashboard;
use Illuminate\Support\Facades\Route;

Route::get('/', Dashboard::class);

Route::get('/icons/icon-{size}.png', IconController::class)->where('size', '192|512');
