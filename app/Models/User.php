<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\Csdb\CObject;
use App\Models\Csdb\Trash;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Http\Request;
use Laravel\Passport\Token;
use DateInterval;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function roles(): HasOne
    {
        return $this->hasOne(Role::class, 'user_id');
    }

    public function cobjects(){
        return $this->morphMany(CObject::class, 'owner');
    }
    public function trashes(){
        return $this->morphMany(Trash::class, 'owner');
    }

    /**
     * true if expired
     */
    public static function isAuthTokenExpired(Request | Token $requestOrToken): bool
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

    public static function getTokenFromBearer(Request $request): Token | null
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
