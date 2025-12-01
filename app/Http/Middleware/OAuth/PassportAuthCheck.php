<?php

namespace App\Http\Middleware\OAuth;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\Token;
use Carbon\Carbon;
use DateInterval;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Throwable;

/**
 * @deprecated
 * fungsi dipindah ke App\Http\Controllers\Controller
 * will be redirected to url if authenticated by session or token, 
 * or will be go to next request if not authenticated, ( ini === middleware 'guest')
 * or will return abort unauthenticated (ini === middleware 'auth' tapi ditambah abort)
 * this middleware would make any request will be authenticated in session based
 */
class PassportAuthCheck
{
    /**
     * true if expired
     */
    public static function isTokenExpired(Request | Token $requestOrToken) :bool
    {
        $token = ($requestOrToken instanceof Token) ? $requestOrToken : self::getTokenFromBearer($requestOrToken);
        // $now = new \DateTime($token->created_at); // Your first date/time
        $now = now(); // Your first date/time
        $expiredAt = $token->expires_at; // Your second date/time        
        // dd($expiredAt0>sub, $token);
        // $expiredAtMinus1minute = clone $expiredAt; // Clone to avoid modifying original
        $expiredAtMinus1minute = $expiredAt; // Clone to avoid modifying original
        $expiredAtMinus1minute->sub(new \DateInterval('PT1M')); // Subtract 1 minutes        
        return !($now < $expiredAtMinus1minute) ;
    }

    public static function getTokenFromBearer(Request $request) :Token | null
    {
        $bearerToken = $request->bearerToken();
        if(!$bearerToken) return null;

        // $config = Configuration::forUnsecuredSigner(); // or use proper signer with Passport keys
        $publicKeyPath = storage_path('oauth-public.key');
        $publicKey = InMemory::file($publicKeyPath);

        $privateKeyPath = storage_path('oauth-private.key');
        $privateKey = InMemory::file($privateKeyPath);

        // 2. Configure JWT for verification
        $config = Configuration::forAsymmetricSigner(
            new Sha256(),
            // InMemory::plainText(''),   // No private key needed here
            $privateKey,
            $publicKey
        );

        $token = $config->parser()->parse($bearerToken); // Lcobucci\JWT\Token\Plain
        $tokenId = $token->claims()->get('jti'); // 👈 "jti" = the token ID used in oauth_access_tokens table

        // Lookup in DB
        $tokenRecord = Token::find($tokenId);
        return $tokenRecord;
    }

    public static function isAuthByToken(Request $request): int | Token | null
    {
        $tokenRecord = self::getTokenFromBearer($request);
        if(!$tokenRecord){
            return null;
        }
        if($tokenRecord->revoked || self::isTokenExpired($tokenRecord)){
            return Response::HTTP_UNAUTHORIZED;
        }
        return $tokenRecord;
    }

    /**
     * Handle an incoming request.
     * Jika ada (bearer token dan masih valid) atau (Auth session true) maka request diteruskan
     * Jika 
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response | Throwable
    {
        $redirect_to = $request->get('redirect_to'); // redirect after login success
        if (!$redirect_to) {
            $redirect_to = config("app.url");
        }
        return redirect($redirect_to);

        // if request is authenticated by session
        $isAuth = Auth::check();
        // Auth::logout();
        // $request->session()->invalidate();
        // $request->session()->regenerateToken();
        // dd($isAuth, $request->hasSession());
        if ($isAuth) {
            return redirect($redirect_to);
        }

        // if request is authenticated by token
        $tokenRecord = self::isAuthByToken($request);
        if($tokenRecord === null){
            return $next($request);
        }
        if ($tokenRecord === Response::HTTP_UNAUTHORIZED) {
            return abort(Response::HTTP_UNAUTHORIZED, 'Unauthorized.');
        }
        if ($tokenRecord->revoked || Carbon::now()->greaterThan($tokenRecord->expires_at)) {
            return abort(Response::HTTP_UNAUTHORIZED, 'Token expired or revoked.');
        }

        $user = User::find($tokenRecord->user_id);
        Auth::login($user);

        return redirect($redirect_to);
    }
}
