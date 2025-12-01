<?php

use App\Http\Controllers\Auth\ForOAuthClientController;
use App\Http\Controllers\Dashboard\OAuthClientController;
use App\Http\Middleware\EnsureAccountPasswordIsMatched;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::middleware(['auth:api', 'can:manage_oauth_clients'])->group(function(){
Route::middleware(['auth', 'can:manage_oauth_clients'])->group(function(){
  Route::get('/oauth-client/', [OAuthClientController::class, 'index']);
  Route::post('/oauth-client/register', [OAuthClientController::class, 'register']);
  Route::get('/oauth-client/{client}', [OAuthClientController::class, 'read']);
  Route::post('/oauth-client/{client}/delete', [OAuthClientController::class, 'delete'])->middleware([EnsureAccountPasswordIsMatched::class]);
  Route::post('/oauth-client/{client}/update', [OAuthClientController::class, 'update'])->middleware([EnsureAccountPasswordIsMatched::class]);
});
