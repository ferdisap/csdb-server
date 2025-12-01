<?php

use App\Http\Controllers\Auth\ForOAuthClientController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Middleware\OAuth\PassportAuthCheck;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::get('/register', [RegisterController::class, 'signUp'])->middleware(['guest'])->name("signup");
Route::post('/register', [RegisterController::class, 'register'])->middleware(['guest'])->name("register");

Route::get('/email/verify', [RegisterController::class, 'notice'])->middleware(['auth'])->name("verification.notice");
Route::get('/email/verify/{id}/{hash}', [RegisterController::class, 'verify'])->middleware(['auth', 'signed'])->name("verification.verify");
Route::post('/email/verification-notification', [RegisterController::class, 'send'])->middleware(['auth', 'throttle:6,1'])->name("verification.send");