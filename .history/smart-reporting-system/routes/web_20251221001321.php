<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DataController;
use App\Http\Controllers\ForecastController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/', [DataController::class, 'index'])->name('home');
Route::post('/upload', [DataController::class, 'uploadFile'])->name('uploadFile');
Route::get('/results', [DataController::class, 'showResults'])->name('showResults');
Route::get('/download', [DataController::class, 'downloadPdf'])->name('downloadPdf');
Route::get('/forecast', [ForecastController::class, 'forecast'])->name('forecast');
