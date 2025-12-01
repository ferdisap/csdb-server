<?php

use App\Http\Controllers\Account\UserController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::get("/dashboard/{any}", [LoginController::class, 'dashboard'])->where('any', '.*')->middleware('auth')->name("user.dashboard");