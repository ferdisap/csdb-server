<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\PassportClientResource;
use App\Models\PassportClient;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\Client;
use Laravel\Passport\ClientRepository;

use function PHPUnit\Framework\isBool;

// function change_array_key(&$array, $old_key, $new_key)
// {
//   if (!array_key_exists($old_key, $array)) {
//     return $array; // Old key doesn't exist, return original array
//   }

//   $keys = array_keys($array); // Get all keys
//   $index = array_search($old_key, $keys); // Find the index of the old key
//   $keys[$index] = $new_key; // Replace the old key with the new key at that index

//   return $array = array_combine($keys, $array); // Combine the new keys with the original values
// }

class OAuthClientController extends Controller
{
  public function index(Request $request)
  {
    $clients = PassportClient::all();
    return response()->json([
      'clients' => $clients
    ]);
  }

  public function read(Request $request, Client $client)
  {
    return response()->json([
      'client' => new PassportClientResource($client)
    ]);
  }

  public function register(Request $request)
  {
    $validated = $request->validate([
      "app_name" => "required|max:20",
      "client_type" => [
        function (string $attribute, mixed $value, Closure $fail): void {
        if (!($value != 'client_credential' || $value != 'password_grant')) {
          $fail('The :attribute must be "client_credential" or "password_grant".');
        }
      }],
      "redirect_uris" => [
        'string','max:255',
        function (string $attribute, mixed $value, Closure $fail) use($request): void {
          if($request->client_type != 'password_grant'){            
            if (!($value && $value != '')) {
              $fail('The :attribute must be "client_credential" or "password_grant".');
            }
          }
        }
      ],
      "confidential" => [
        'boolean:strict',
        function (string $attribute, mixed $value, Closure $fail) use($request): void {
          if($request->client_type != 'password_grant'){            
            if (!isBool($value)) {
              $fail('The :attribute must be "client_credential" or "password_grant".');
            }
          }
        }
      ],
      "device_flow" => [
        'boolean:strict',
        function (string $attribute, mixed $value, Closure $fail) use($request): void {
          if($request->client_type != 'password_grant'){            
            if (!isBool($value)) {
              $fail('The :attribute must be "client_credential" or "password_grant".');
            }
          }
        }
      ],
    ]);

    // Creating an OAuth app client that belongs to the given user...
    if ($validated["client_type"] === 'password_grant') {
      $client = app(ClientRepository::class)->createAuthorizationCodeGrantClient(
        user: $request->user(), // User model or Admin model if login can be authenticated by admin provider,
        name: $validated["app_name"],
        redirectUris: array_map(fn($v) => trim($v), explode(",", $validated["redirect_uris"])), //['https://third-party-app.com/callback'],
        confidential: $validated["confidential"], // bool // true jika untuk backend, false jika untuk frontend,
        enableDeviceFlow: $validated["device_flow"], // bool //  default false saja
      );
      $client->provider = 'users';
    } else if ($validated["client_type"] === 'client_credential') {
      $client = app(ClientRepository::class)->createClientCredentialsGrantClient($validated["app_name"]);
    } else {
      abort(400);
    }

    $client->save();

    if ($client->owner) {
      $client->owner->makeHidden(["id", "email_verified_at", "created_at", "updated_at"]); // only name and email
    }
    $client->is_public = $client->plainSecret ? false : true;
    $client->makeHidden(['owner_type', 'owner_id', 'secret']);

    // Retrieving all the OAuth app clients that belong to the user...
    // $clients = $user->oauthApps()->get();

    return response()->json([
      "message" => "One OAuth Client has been registered.",
      'client' => new PassportClientResource($client),
      "secret" => $client->plainSecret
    ]);
  }

  public function delete(Request $request, Client $client)
  {
    $validated = $request->validate([
      "deleted" => "required|boolean",
    ]);
    if ($validated["deleted"]) {
      $client->delete();

      return response()->json([
        'message' => "One has been deleted from database.",
        'client' => new PassportClientResource($client),
      ]);
    } else {
      abort('400');
    }
  }

  public function update(Request $request, Client $client)
  {
    $validated = $request->validate([
      "app_name" => "max:20",
      "revoked" => "boolean",
    ]);

    change_array_key($validated, "app_name", "name");

    foreach ($validated as $column => $value) {
      $client->$column = $value;
    }

    $client->save();

    if ($client->owner) {
      $client->owner->makeHidden(["id", "email_verified_at", "created_at", "updated_at"]); // only name and email
    }
    $client->is_public = $client->secret ? false : true;
    $client->makeHidden(['owner_type', 'owner_id', 'secret']);

    return response()->json([
      'message' => "One has been updated.",
      'client' => new PassportClientResource($client)
    ]);
  }
}
