<?php

use App\Http\Controllers\Dashboard\OAuthTokenController;
use App\Http\Middleware\EnsureAccountPasswordIsMatched;
use Illuminate\Support\Facades\Route;

// Route::middleware('auth:api')->group(function(){
Route::middleware('auth')->group(function(){
  Route::get('/oauth-token/', [OAuthTokenController::class, 'index']);
  Route::get('/oauth-token/{token}', [OAuthTokenController::class, 'read']);
  Route::post('/oauth-token/{token}/delete', [OAuthTokenController::class, 'delete'])->middleware([EnsureAccountPasswordIsMatched::class]);
  Route::post('/oauth-token/{token}/update', [OAuthTokenController::class, 'update'])->middleware([EnsureAccountPasswordIsMatched::class]);
});
