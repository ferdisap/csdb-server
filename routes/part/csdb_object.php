<?php

use App\Http\Controllers\Csdb\CObjectController;
use App\Http\Controllers\Csdb\XmlController;
use App\Http\Controllers\Dashboard\OAuthTokenController;
use App\Http\Middleware\Csdb\EnsureResourceOwner;
use App\Http\Middleware\EnsureAccountPasswordIsMatched;
use Illuminate\Support\Facades\Route;
use Laravel\Passport\Http\Middleware\CheckTokenForAnyScope;
use Laravel\Passport\Http\Middleware\EnsureClientIsResourceOwner;

Route::middleware([
  // 'auth:api', 
  // CheckTokenForAnyScope::using(['csdb:create', 'csdb:read', 'csdb:delete']),
  'auth', 
  EnsureResourceOwner::class,
])->group(function(){
  Route::get('/csdb-object/', [CObjectController::class, 'index']);
  Route::get('/csdb-object/trash', [CObjectController::class, 'index_trash']);
  Route::get('/csdb-object/{cobject:filename}', [CObjectController::class, 'read']);
  Route::get('/csdb-object/trash/{trash:filename}', [CObjectController::class, 'read_trash']);
  Route::post('/csdb-object/trash/{trash:filename}/delete', [CObjectController::class, 'delete_trash']);
  Route::post('/csdb-object/xml/upload', [CObjectController::class, 'upload']);
  Route::post('/csdb-object/{csdbObject:filename}/delete', [CObjectController::class, 'delete']);
  Route::post('/csdb-object/{csdbObject:filename}/restore', [CObjectController::class, 'restore']);
  // Route::post('/csdb-object/{token}/delete', [OAuthTokenController::class, 'delete'])->middleware([EnsureAccountPasswordIsMatched::class]);
  // Route::post('/csdb-object/{token}/update', [OAuthTokenController::class, 'update'])->middleware([EnsureAccountPasswordIsMatched::class]);
});
