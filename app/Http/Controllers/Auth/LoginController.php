<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Middleware\OAuth\PassportAuthCheck;
use App\Models\User;
use Carbon\Carbon;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\Passport;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends Controller
{
  public function login()
  {
    // check jika user sudah authenticated menggunakan token based, maka tidak perlu login. Tinggal session regenerate saja.
    return view('auth.login.signIn');
  }

  /**
   * passport tidak bisa menangani token yang disimpan di cookie. 
   * Passport hanya murni menangani header 'Authorization' Bearer
   */
  public function authenticate(Request $request)
  {
    // $redirect_uri = $request->get('redirect_uri'); // save to db for callback
    $redirect_to = $request->get('redirect_to') ?? config("app.url") . "/"; // redirect after login success
    $request->validate([
      'email' => ['required', 'email'],
      'password' => ['required'],
      'remember_me' => 'boolean',
    ]);
    if(!Auth::attempt($request->only('email', 'password'), $request->remember_me)){
      return response()->json([
        'message' => 'Invalid credentials'
      ], 401);
    }
    $request->session()->regenerate();

    return response()->json([
      'message' => "login success",
      'redirect' => $redirect_to,
      'user' => Auth::user(),
    ]);
  }

  public function logout(Request $request)
  {
    $redirect_to = $request->get('redirect_to') ?? config("app.url") . "/"; // redirect after login success

    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return response()->json([
      'message' => 'Successfully logged out.',
      'redirect' => $redirect_to,
    ]);
  }

  // /**
  //  * passport tidak bisa menangani token yang disimpan di cookie. 
  //  * Passport hanya murni menangani header 'Authorization' Bearer
  //  */
  // public function authenticate_xxx(Request $request)
  // {
  //   // $redirect_uri = $request->get('redirect_uri'); // save to db for callback
  //   $redirect_to = $request->get('redirect_to') ?? config("app.url") . "/"; // redirect after login success
  //   $credentials = $request->validate([
  //     'email' => ['required', 'email'],
  //     'password' => ['required'],
  //     'remember_me' => 'boolean',
  //   ]);
  //   if (!Auth::validate($credentials)) {
  //     return response()->json([
  //       'message' => 'Invalid credentials'
  //     ], 401);
  //   }
  //   $user = User::where('email', $request->email)->first();
  //   $user->makeHidden("id");
  //   $tokenResult = $user->createToken('First Client Party Login Token', ['user:read', 'csdb:create', 'csdb:read', 'csdb:delete']);
  //   $tokenResult->token->expires_at = now()->addDay(1);
  //   $tokenResult->token->save();
  //   $token = $tokenResult->token;

  //   // $cookie = Cookie::make(
  //   //   'access_token',         // Nama cookie
  //   //   $tokenResult->accessToken, // Nilai
  //   //   floor(abs($tokenResult->token->expires_at->diffInMinutes(now()))), // Masa berlaku (menit) → 30 hari      
  //   //   '/',                     // Path
  //   //   null,                    // Domain (null = current domain)
  //   //   $request->getScheme() === 'https', // Secure: true di HTTPS
  //   //   true,                    // 🔑 HttpOnly: true (WAJIB!)
  //   //   false,                   // Raw (false = auto-encode)
  //   //   'strict'                 // 🔑 SameSite: 'strict' atau 'lax'
  //   // );

  //   // Payload harus sesuai format TokenGuard
  //   $payload = [
  //       'jti' => $token->id,
  //       'scopes' => $token->scopes ?? [],
  //       'exp' => $token->expires_at->timestamp,
  //       'iat' => time(),
  //   ];
    
  //   // Dapatkan key yang BENAR
  //   $key = Passport::tokenEncryptionKey(app('encrypter'));

  //   $jwt = JWT::encode($payload, $key, 'HS256');

  //   // Jika encryption aktif → enkripsi cookie dulu
  //   if (Passport::$decryptsCookies) {
  //     $jwt = app('encrypter')->encrypt($jwt, Passport::$unserializesCookies);
  //   }

  //   Cookie::queue(
  //     Passport::cookie(),  // nama cookie
  //     $jwt,
  //     60 * 24 * 7,         // 7 hari
  //     // floor(abs($tokenResult->token->expires_at->diffInMinutes(now()))),
  //     '/',
  //     null,
  //     true,                // https only
  //     true,                // httpOnly
  //     false,
  //     'Lax'
  //   );


  //   return response()->json([
  //     'message' => "login success",
  //     'redirect' => $redirect_to,
  //     // deprecated for token here karena sudah ada di cookie
  //     // 'token' => [
  //     //   'access_token' => $tokenResult->accessToken,
  //     //   'token_type' => 'Bearer',
  //     //   'expires_in' => $tokenResult->token->expires_at,
  //     // ],
  //     'user' => $user,
  //     // 'csrf_token' => csrf_token(),
  //   // ])->withCookie($cookie);
  //   ])->withCookie(
  //     Passport::cookie(),
  //     $token['access_token'],
  //     floor(abs($tokenResult->token->expires_at->diffInMinutes(now())))
  //   );
  //   // ]);
  //   // return back()->withErrors([
  //   //     'email' => 'The provided credentials do not match our records.',
  //   // ])->onlyInput('email');
  // }

  // /**
  //  * tidak membuat session
  //  */
  // public function authenticate_xx(Request $request)
  // {
  //     // $redirect_uri = $request->get('redirect_uri'); // save to db for callback
  //     $redirect_to = $request->get('redirect_to') ?? config("app.url") . "/"; // redirect after login success
  //     $credentials = $request->validate([
  //         'email' => ['required', 'email'],
  //         'password' => ['required'],
  //         'remember_me' => 'boolean',
  //     ]);
  //     if (!Auth::validate($credentials)) {
  //         return response()->json([
  //             'message' => 'Invalid credentials'
  //         ], 401);
  //     }
  //     $user = User::where('email', $request->email)->first();
  //     $user->makeHidden("id");
  //     $tokenResult = $user->createToken('First Client Party Login Token', ['user:read', 'csdb:create', 'csdb:read', 'csdb:delete']);
  //     $tokenResult->token->expires_at = now()->addDay(1);
  //     $tokenResult->token->save();
  //     // session dibuat agar jika ada third party client mau login sso, tidak perlu login lagi. 
  //     // Tapi konsekuensinya, jika session habis, first party tetap sudah login meskipun tidak ada session karena token based. 
  //     // Solusinya adalah sebelum response view login, server akan mengecek jika ada token dan match maka tidak perlu menampilkan view login
  //     return response()->json([
  //         'message' => "login success",
  //         'redirect' => $redirect_to,
  //         'token' => [
  //             'access_token' => $tokenResult->accessToken,
  //             'token_type' => 'Bearer',
  //             'expires_in' => $tokenResult->token->expires_at,
  //         ],
  //         'user' => $user,
  //         'csrf_token' => csrf_token(),
  //     ]);
  //     // return back()->withErrors([
  //     //     'email' => 'The provided credentials do not match our records.',
  //     // ])->onlyInput('email');
  // }

  // /**
  //  * passport tidak bisa menanganin token yang disimpan di cookie
  //  */
  // public function logout_xxx(Request $request)
  // {
  //   $user = $request->user();
  //   $token = $user->token();
  //   $token->revoke();

  //   DB::table('oauth_refresh_tokens')
  //     ->where('access_token_id', $token->id)
  //     ->update(['revoked' => true]);

  //   Auth::logout();
  //   $request->session()->invalidate();
  //   $request->session()->regenerateToken();

  //   // $cookie = cookie()->forget('access_token');
  //   // Clear cookie
  //   Cookie::queue(
  //       Cookie::forget(Passport::cookie())
  //   );

  //   return response()->json([
  //     'message' => 'Successfully logged out.',
  //   // ])->cookie($cookie);
  //   ]);
  // }

  public function logout_x(Request $request)
  {
    $isLogggedOut = false;
    dd(Auth::check());
    if (Auth::check()) {
      try {
        Auth::logout(); // bisa error karena Auth::check() juga ngecek token based, tapi Auth::logout() tidak untuk revoke token
      } catch (\Throwable $th) {
        //throw $th;
      }
      $request->session()->invalidate();
      $request->session()->regenerateToken();

      $isLogggedOut = true;
    }
    if (($accessToken = $this->getTokenFromBearer($request)) && $this->isAuthTokenExpired($accessToken)) {
      $accessToken->revoke();
      DB::table('oauth_refresh_tokens')
        ->where('access_token_id', $accessToken->id)
        ->update(['revoked' => true]);
      $isLogggedOut = true;
    }

    if ($isLogggedOut) {
      return response()->json([
        'message' => 'Successfully logged out.',
        'csrf_token' => csrf_token(),
      ]);
    } else {
      return response(null, Response::HTTP_UNAUTHORIZED);
    }
  }

  public function authenticate_x(Request $request)
  {
    // $url = "http://" . "www.fufufafa.com" . "?foo=bar";
    // $url = "http://username:password@www.fufufafa.com:8001/tes/aaa" . "?foo=bar&aa=bb" . "#aaa";
    // $url = "http://www.fufufafa.com" . "?foo=bar&aa=bb";
    // $queries = http_build_query([
    //     'client_id' => "aaa",
    //     'client_secret' => "bbb",
    // ]);

    // $parse_uri = parse_url($url);
    // $new_redirect_to = "";
    // if ($parse_uri["scheme"]) {
    //     $new_redirect_to .= $parse_uri["scheme"] . "://";
    //     if (isset($parse_uri["user"]) && isset($parse_uri["pass"])) {
    //         $new_redirect_to .= $parse_uri["user"] . ":" . $parse_uri["pass"] . "@";
    //     }
    //     $new_redirect_to .= $parse_uri["host"];
    //     if (isset($parse_uri["port"])) $new_redirect_to .= ":" . $parse_uri["port"];
    //     if (isset($parse_uri["path"])) $new_redirect_to .= $parse_uri["path"];
    //     $new_redirect_to .= "?" . $parse_uri["query"];
    //     if (isset($parse_uri["fragment"])) $new_redirect_to .= "#" . $parse_uri["fragment"];
    // }
    // dd('fufufafa', parse_url(($url)), $queries, $new_redirect_to);

    $redirect_to = $request->get('redirect_to');
    // return redirect($redirect_to) ?? redirect()->intended('dashboard');
    // dd($redirect_to, $request);

    $credentials = $request->validate([
      'email' => ['required', 'email'],
      'password' => ['required'],
    ]);

    if (Auth::attempt($credentials)) {
      $request->session()->regenerate();
      return $redirect_to ? redirect($redirect_to) : redirect()->intended('dashboard');
    }

    return back()->withErrors([
      'email' => 'The provided credentials do not match our records.',
    ])->onlyInput('email');
  }

  public function user(Request $request)
  {
    $user = $request->user();
    $user->makeHidden("id");
    return response($user, 200, ["content-type" => "application/json"]);
  }

  public function dashboard(Request $request)
  {
    return view("dashboard.dashboard");
  }
}
