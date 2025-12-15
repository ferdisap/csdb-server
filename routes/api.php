<?php

use App\Http\Controllers\Auth\ForOAuthClientController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Dashboard\OAuthClientController;
use App\Http\Middleware\PasssportCookieAuth;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

Route::get('/user', function (Request $request) {
  return $request->user();
})->middleware('auth:api');

// require __DIR__ . "/../resources/views/upload-encrypt/routes.php";