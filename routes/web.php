<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\Onboarding\ConnectChessAccountController;
use App\Http\Controllers\PlayerAnalysisController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/home', [HomeController::class, 'index']);
Route::post('/home/refresh', [HomeController::class, 'refresh'])
    ->middleware('auth')
    ->name('home.refresh');
Route::view('/analysis', 'analysis')->name('analysis');
Route::get('/analysis/player/{slug?}', [PlayerAnalysisController::class, 'show'])->name('analysis.player');
Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
Route::get('/profile/export-pgn', [ProfileController::class, 'exportPgn'])
    ->middleware('auth')
    ->name('profile.export-pgn');
Route::view('/theme', 'welcome')->name('theme');
Route::view('/loading', 'loading')->name('loading');

Route::get('/onboarding/connect', [ConnectChessAccountController::class, 'create'])->name('onboarding.connect');
Route::post('/onboarding/connect/check', [ConnectChessAccountController::class, 'check'])->name('onboarding.connect.check');
Route::post('/onboarding/connect', [ConnectChessAccountController::class, 'store'])
    ->middleware('auth')
    ->name('onboarding.connect.store');

require __DIR__.'/auth.php';
