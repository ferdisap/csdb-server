<?php

use App\Http\Controllers\Auth\ForOAuthClientController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Middleware\PasssportCookieAuth;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::get('/login', [LoginController::class, 'login'])->middleware('guest')->name("login");
Route::post('/login', [LoginController::class, 'authenticate'])->middleware('guest')->name("authenticate");
Route::get('/logout', [LoginController::class, 'logout'])->middleware('auth')->name("logout");
Route::get('/auth/user', [LoginController::class, 'user'])->middleware("auth")->name("auth.user");
Route::get('/oauth/user', [LoginController::class, 'user'])->middleware("auth:api")->name("oauth.user");

// Route::get('/logout', [LoginController::class, 'logout'])->middleware('auth:api')->name("logout");

Route::get('/csrf-cookie', [ForOAuthClientController::class, 'getCsrf']);
// Route::post('/session-regenerate', [ForOAuthClientController::class, 'sessionRegenerate']);
// Route::get("/web/authorize", [ForOAuthClientController::class, 'authorizeView']);
// Route::get('/is-auth', [ForOAuthClientController::class, 'isAuth']);

// Route::get("foo", function(){
//   dd('aaa', (request()->cookie("access_token")));
// // });
// })->middleware(['auth:api']);
// })->middleware([PasssportCookieAuth::class]);
// })->middleware(['auth:api',PasssportCookieAuth::class]);