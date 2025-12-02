<?php

use App\Http\Controllers\Account\UserController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
  return view('welcome');
});
Route::get('/upload', function () {
  return view('upload.upload');
});

require __DIR__ . "/part/authenticate.php";
require __DIR__ . "/part/register.php";
require __DIR__ . "/part/dashboard.php";
require __DIR__ . "/part/oauth_client.php";
require __DIR__ . "/part/oauth_token.php";
require __DIR__ . "/part/csdb_object.php";

// Route::get("/account/index", [UserController::class, 'index'])->middleware('auth');