<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ScoreController;

Route::get('/', function () {
    return view('landing');
});

Route::get('/snake', function () {
    return view('pages.snake');
});

Route::get('/high-scores', [ScoreController::class, 'index']);
