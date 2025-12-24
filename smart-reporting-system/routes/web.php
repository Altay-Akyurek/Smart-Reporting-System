<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DataController;
use App\Http\Controllers\ForecastController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/', [DataController::class, 'index'])->name('home');
Route::post('/upload', [DataController::class, 'uploadFile'])->name('uploadFile');
Route::get('/select-columns', [DataController::class, 'showColumnSelection'])->name('selectColumns');
Route::post('/analyze', [DataController::class, 'processSelectedColumns'])->name('processAnalysis');
Route::get('/results', [DataController::class, 'showResults'])->name('showResults');
Route::post('/download-report', [DataController::class, 'downloadPdf'])->name('downloadPdf');
Route::get('/forecast', [ForecastController::class, 'forecast'])->name('forecast');
