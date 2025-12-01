<?php

namespace App\Http\Middleware\Csdb;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * the request will be appended csdb_resource_owner where is instance of User model
 */
class EnsureResourceOwner
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request &$request, Closure $next): Response
    {
        $isAuthUserModel = ($request->user() && $request->user() instanceof User);
        // try to access request user. If machine-to-machine request, the valis will be false
        if($isAuthUserModel){
            $request->merge(["csdb_resource_owner" => $request->user()]);
            return $next($request);
        }
        // try to access csdb_resource_owner_email
        else if($userEmail = $request->get('csdb_resource_owner_email')){
            $user = User::where('email', $userEmail)->first();
            if($user){
                $request->merge(["csdb_resource_owner" => $user]);
            }
            return $next($request);
        }
        return abort(401, "Unauthorized.");
    }
}
