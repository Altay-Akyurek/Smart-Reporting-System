<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DataController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/', [DataController::class, 'index'])->name('home');
Route::post('/upload', [DataController::class, 'uploadFile'])->name('uploadFile');
Route::get('/results', [DataController::class, 'showResults'])->name('showResults');
