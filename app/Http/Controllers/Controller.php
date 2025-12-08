<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Passport\Token;
use DateInterval;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;

class Controller
{
  /**
   * @deprecated
   * dipindah ke Model\User
   * true if expired
   */
  public function isAuthTokenExpired(Request | Token $requestOrToken): bool
  {
    $token = ($requestOrToken instanceof Token) ? $requestOrToken : self::getTokenFromBearer($requestOrToken);
    // $now = new \DateTime($token->created_at); // Your first date/time
    $now = now(); // Your first date/time
    $expiredAt = $token->expires_at; // Your second date/time        
    // dd($expiredAt0>sub, $token);
    // $expiredAtMinus1minute = clone $expiredAt; // Clone to avoid modifying original
    $expiredAtMinus1minute = $expiredAt; // Clone to avoid modifying original
    $expiredAtMinus1minute->sub(new \DateInterval('PT1M')); // Subtract 1 minutes        
    return !($now < $expiredAtMinus1minute);
  }

  /**
   * @deprecated
   * dipindah ke Model\User
   */
  public function getTokenFromBearer(Request $request): Token | null
  {
    $bearerToken = $request->bearerToken();
    if (!$bearerToken) return null;

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
}
