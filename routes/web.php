<?php

use Illuminate\Support\Facades\Route;

Route::get('/snake', function () {
    return view('snake');
});
