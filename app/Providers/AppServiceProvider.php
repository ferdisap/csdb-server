<?php

namespace App\Providers;

use App\Models\User;
use App\Policies\RolesPolicy;
use Carbon\CarbonInterval;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;
use Inertia\Inertia;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ### POLICY

        Gate::define('manage_oauth_clients', [RolesPolicy::class, 'manage_oauth_client']);

        // ### end of POLICY

        // #### PASSPORT

        // Passport::cookie('access_token');
        // Passport::enableCookieEncryption();        // Enkripsi cookie
        // Passport::cookie('passport_token');        // Nama cookie
        // Passport::ignoreCsrfToken();               // Untuk SPA
        // Passport::useCookieAuthentication();

        Passport::loadKeysFrom(__DIR__ . '/../../storage');

        Passport::tokensCan([
            'user:read' => 'Retrieve the user info',
            // 'csdb:create' => 'Post new csdb object', // csdb akan menggunakan API key server-to-server
            'csdb:read' => 'Get the csdb object file',
            // 'csdb:delete' => 'Get the csdb object file'
        ]);

        Passport::defaultScopes([
            'user:read',
        ]);

        Passport::tokensExpireIn(CarbonInterval::days(15));
        Passport::refreshTokensExpireIn(CarbonInterval::days(30));
        Passport::personalAccessTokensExpireIn(CarbonInterval::months(6));

        // By providing a view name...
        // Passport::authorizationView('auth.oauth.authorize');

        // Passport::authorizationView("vendor.passport.authorize");
        // By providing a closure...
        // dd(Inertia::render('Auth/OAuth/Authorize'));
        Passport::authorizationView(
            fn($parameters) => view('vendor.passport.authorize', [
                'request' => $parameters['request'],
                'authToken' => $parameters['authToken'],
                'client' => $parameters['client'],
                'user' => $parameters['user'],
                'scopes' => $parameters['scopes'],
            ])
        );

        // #### end of PASSPORT


        // ### EMAIL

        // email
        VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            return (new MailMessage)
                ->subject('Verify Email Address')
                ->line('Click the button below to verify your email address.')
                ->action('Verify Email Address', $url);
        });

        // ### end ofEMAIL
    }
}
