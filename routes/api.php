<?php

use App\Http\Controllers\Auth\ForOAuthClientController;
use App\Http\Controllers\Dashboard\OAuthClientController;
use App\Http\Middleware\PasssportCookieAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

// Route::get("bar", function(){
//   dd('aaa', (request()->cookie("access_token")));
// });
// })->middleware(['passport.cookie', 'auth:cookie-token']);
// })->middleware(['auth:api']);
// require __DIR__ . "/part/oauth_client.php";