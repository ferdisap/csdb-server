<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Middleware\OAuth\PassportAuthCheck;
use App\Models\User;
use Carbon\CarbonInterval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\ClientRepository;
use Illuminate\Support\Facades\Http;
use Laravel\Passport\Passport;
use Laravel\Passport\Token;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Symfony\Component\HttpFoundation\Response;

class ForOAuthClientController extends Controller
{
  /** @deprecated karena app ini login tidak menggunakan auth:api passport lagi dikarenakan kurang aman karena passport tidak bisa check token yang disimpan di cookie*/
  public function authenticate_x(Request $request)
  {
    $redirect_uri = $request->get('redirect_uri'); // save to db for callback
    $redirect_to = $request->get('redirect_to'); // redirect after login success
    $credentials = $request->validate([
      'email' => ['required', 'email'],
      'password' => ['required'],
      'remember_me' => 'boolean',
    ]);
    if ($redirect_uri) {
      if (Auth::attempt($credentials)) {
        $request->session()->regenerate();

        $user = Auth::user();
        // Creating an OAuth app client that belongs to the given user...
        if (!$request->client_id) {
          $client = app(ClientRepository::class)->createAuthorizationCodeGrantClient(
            user: $user,
            name: 'Example App',
            redirectUris: [$redirect_uri],
            confidential: false,
            enableDeviceFlow: true
          );
          $parse_uri = parse_url($redirect_to);
          $additional_queries = http_build_query([
            'client_id' => $client->id,
            'client_secret' => $client->plainSecret,
          ]);
          if (isset($parse_uri["query"])) {
            $parse_uri["query"] .= "&" . $additional_queries;
          } else {
            $parse_uri["query"] = $additional_queries;
          }
          $redirect_to = "";
          if (isset($parse_uri["scheme"])) {
            $redirect_to .= $parse_uri["scheme"] . "://";
            if (isset($parse_uri["user"]) && isset($parse_uri["pass"])) {
              $redirect_to .= $parse_uri["user"] . ":" . $parse_uri["pass"] . "@";
            }
            $redirect_to .= $parse_uri["host"];
            if (isset($parse_uri["port"])) $redirect_to .= ":" . $parse_uri["port"];
            if (isset($parse_uri["path"])) $redirect_to .= $parse_uri["path"];
            $redirect_to .= "?" . $parse_uri["query"];
            if (isset($parse_uri["fragment"])) $redirect_to .= "#" . $parse_uri["fragment"];
          }
        }

        return $redirect_to ? redirect($redirect_to) : redirect()->intended('dashboard');
      }
    }
    // login first party
    else {
      $response = Http::asForm()->post(config('app.url') . '/oauth/token', [
        'grant_type'    => 'password',
        'client_id'     => config('oauth_server.client_id'),
        'client_secret' => config('oauth_server.client_secret'),
        'username'      => $request->email,
        'password'      => $request->password,
        'scope'         => '', // bisa tambahkan scope kalau ada
      ]);

      if ($response->failed()) {
        return response()->json(['error' => 'Invalid credentials'], 401);
      }

      $data = $response->json();

      // Default expire = 1 jam
      // Kalau remember_me = true, pakai refresh_token untuk auto-renew
      if ($request->remember_me) {
        // frontend bisa simpan refresh_token di cookie/httpOnly
        Passport::tokensExpireIn(CarbonInterval::days(1));
        Passport::refreshTokensExpireIn(CarbonInterval::days(30));
        return response()->json([
          'access_token'  => $data['access_token'],
          'refresh_token' => $data['refresh_token'],
          'expires_in'    => $data['expires_in'], // biasanya 3600 (1 jam)
        ]);
      }

      return response()->json([
        'access_token' => $data['access_token'],
        'expires_in'   => $data['expires_in'],
      ]);
      // if (Auth::attempt($credentials)) {
      //     $user = Auth::user();
      //     $tokenResult = $user->createToken('Personal Access Token');
      //     $token = $tokenResult->token;
      //     if ($request->remember_me) {
      //         $token->expires_at = Carbon::now()->addWeeks(1); // contoh 1 minggu
      //     } else {
      //         $token->expires_at = Carbon::now()->addHours(1);
      //     }
      // }
    }

    return back()->withErrors([
      'email' => 'The provided credentials do not match our records.',
    ])->onlyInput('email');
  }

  /** @deprecated karena app ini login tidak menggunakan auth:api passport lagi dikarenakan kurang aman karena passport tidak bisa check token yang disimpan di cookie*/
  public function getCsrf(Request $request)
  {
    // Ambil CSRF token dari session
    $token = csrf_token();

    // Laravel otomatis akan set XSRF-TOKEN cookie
    return response()->json([
      'csrf_token' => $token,
    ])->withCookie(cookie('XSRF-TOKEN', $token, 120, null, null, false, false));
  }

  // to generate session by the user request Bearer token
  /** @deprecated karena app ini login tidak menggunakan auth:api passport lagi dikarenakan kurang aman karena passport tidak bisa check token yang disimpan di cookie*/
  public function sessionRegenerate(Request $request)
  {
    // true if $request header contain  header X-XSRF-TOKEN atau X-CSRF-TOKEN

    // $tokenRecord = User::getTokenFromBearer($request);

    // if ($tokenRecord === Response::HTTP_BAD_REQUEST || $tokenRecord === Response::HTTP_UNAUTHORIZED) {
    //   return response(null, $tokenRecord);
    // }

    // $user = User::find($tokenRecord->user_id);

    
    $user = $this->checkTowardsToken($request);
    if(!$user) return response(null, Response::HTTP_UNAUTHORIZED);

    Auth::login($user);
    return response()->json([
      "message" => "session has been regerated.",
      "csrf_token" => csrf_token()
    ]);

    // setelah ini, pindahkan cookie X-XSRF-TOKEN dan laravel-session ke request /oauth/authorize
  }

  /** @deprecated karena app ini login tidak menggunakan auth:api passport lagi dikarenakan kurang aman karena passport tidak bisa check token yang disimpan di cookie*/
  private function checkTowardsToken(Request $request) :User | null{
    $tokenRecord = User::getTokenFromBearer($request);

    if ($tokenRecord === Response::HTTP_BAD_REQUEST || $tokenRecord === Response::HTTP_UNAUTHORIZED) {
      return null;
    }

    return User::find($tokenRecord->user_id);
  }

  /** @deprecated karena app ini login tidak menggunakan auth:api passport lagi dikarenakan kurang aman karena passport tidak bisa check token yang disimpan di cookie*/
  public function isAuth(Request $request)
  {
    $user = $this->checkTowardsToken($request);
    dd($user);
    if ($user || Auth::check()) {
      return response([
        "message" => 'Authenticated'
      ], 200);
    } else {
      return response(null, Response::HTTP_UNAUTHORIZED);
    }
  }

  /** @deprecated karena app ini login tidak menggunakan auth:api passport lagi dikarenakan kurang aman karena passport tidak bisa check token yang disimpan di cookie*/
  public function authorizeView()
  {
    return view('auth.webAuthorize');
  }

  /** @deprecated karena app ini login tidak menggunakan auth:api passport lagi dikarenakan kurang aman karena passport tidak bisa check token yang disimpan di cookie*/
  public function refresh(Request $request)
  {
    $request->validate([
      'refresh_token' => 'required',
    ]);

    $response = Http::asForm()->post(config('app.url') . '/oauth/token', [
      'grant_type'    => 'refresh_token',
      'refresh_token' => $request->refresh_token,
      'client_id'     => config('oauth_server.client_id'),
      'client_secret' => config('oauth_server.client_secret'),
      'scope'         => '',
    ]);

    if ($response->failed()) {
      return response()->json(['error' => 'Invalid refresh token'], 401);
    }

    return $response->json();
  }
}
