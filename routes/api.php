<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ScoreController;

Route::post('/scores', [ScoreController::class, 'store']);
Route::get('/scores', [ScoreController::class, 'index']);
