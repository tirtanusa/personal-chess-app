<?php

use App\Http\Controllers\PlayerAnalysisController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');
Route::view('/home', 'home');
Route::view('/analysis', 'analysis')->name('analysis');
Route::get('/analysis/player/{slug?}', [PlayerAnalysisController::class, 'show'])->name('analysis.player');
Route::view('/profile', 'profile')->name('profile');
Route::view('/theme', 'welcome')->name('theme');

Route::view('/login', 'auth.login')->name('login');
Route::view('/register', 'auth.register')->name('register');
Route::view('/onboarding/connect', 'onboarding.connect-account')->name('onboarding.connect');
