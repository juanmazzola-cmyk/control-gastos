<?php

use App\Http\Controllers\IconController;
use App\Livewire\Dashboard;
use Illuminate\Support\Facades\Route;

Route::get('/', Dashboard::class);
Route::get('/pwa-icon/{size}', IconController::class)->where('size', '192|512');
