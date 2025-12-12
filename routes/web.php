<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TopController;

Route::get('/', function () {
    return view('top');
});

// Route::get('/', [TopController::class, 'index']);
