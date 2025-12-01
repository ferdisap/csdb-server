<?php

use App\Http\Controllers\Auth\ForOAuthClientController;
use App\Http\Controllers\Csdb\RepositoryController;
use App\Http\Controllers\Dashboard\OAuthClientController;
use App\Http\Middleware\PasssportCookieAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/** @deprecated all */
Route::get('/repository/new', [RepositoryController::class, 'new'])->middleware('auth:api')->name("repository.new");
Route::get('/repository/merge', [RepositoryController::class, 'merge'])->middleware('auth:api')->name("repository.merge");
Route::get('/repository/meta', [RepositoryController::class, 'meta'])->middleware('auth:api')->name("repository.meta");
Route::get('/repository/pull;', [RepositoryController::class, 'pull'])->middleware('auth:api')->name("repository.pull");