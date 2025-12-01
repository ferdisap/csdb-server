<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\Token as PassportToken;

function change_array_key(&$array, $old_key, $new_key)
{
  if (!array_key_exists($old_key, $array)) {
    return $array; // Old key doesn't exist, return original array
  }

  $keys = array_keys($array); // Get all keys
  $index = array_search($old_key, $keys); // Find the index of the old key
  $keys[$index] = $new_key; // Replace the old key with the new key at that index

  return $array = array_combine($keys, $array); // Combine the new keys with the original values
}

class OAuthTokenController extends Controller
{
  public function index(Request $request)
  {
    $tokens = $request->user()->tokens()->get();
    $tokens = $tokens->map(function (PassportToken $token) {
      if ($token->user) {
        $token->user->makeHidden(["id", "email_verified_at", "created_at", "updated_at"]); // only name and email
      }
      $token->makeHidden(['user_id', 'created_at', 'client']);
      return $token;
    });
    return response()->json([
      'tokens' => $tokens
    ]);
  }

  public function read(Request $request, PassportToken $token)
  {
    if ($token->user) {
      $token->user->makeHidden(["id", "email_verified_at", "created_at", "updated_at"]); // only name and email
    }
    $token->makeHidden(['user_id', 'created_at', 'client']);
    return response()->json([
      'token' => $token
    ]);
  }

  public function delete(Request $request, PassportToken $token)
  {
    $validated = $request->validate([
      "deleted" => "required|boolean",
    ]);
    if ($validated["deleted"]) {
      $token->delete();

      if ($token->user) {
        $token->user->makeHidden(["id", "email_verified_at", "created_at", "updated_at"]); // only name and email
      }
      $token->makeHidden(['user_id', 'created_at', 'client']);

      return response()->json([
        'message' => "One token has been deleted from database.",
        'token' => $token
      ]);
    } else {
      abort('400');
    }
  }

  public function update(Request $request, PassportToken $token)
  {
    $validated = $request->validate([
      "name" => "string",
      "expired" => "string|date|date_format:Y-m-d H:i:s",
      "revoked" => "boolean",
    ]);

    change_array_key($validated, "expired", "expires_at");

    foreach ($validated as $column => $value) {
      $token->$column = $value;
    }

    $token->save();

    if ($token->user) {
      $token->user->makeHidden(["id", "email_verified_at", "created_at", "updated_at"]); // only name and email
    }
    $token->makeHidden(['user_id', 'created_at', 'client']);

    return response()->json([
      'message' => "One token has been updated.",
      'token' => $token
    ]);
  }
}
