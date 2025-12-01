<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PasssportCookieAuth
{
  /**
   * Handle an incoming request.
   *
   * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
   */
  public function handle($request, Closure $next)
  {

    if ($token = $request->cookie('access_token')) {
      // dd($request->cookie('access_token'));
      $request->headers->set('Authorization', 'Bearer ' . $token);

      dd($request->user());
    }

    return $next($request);
  }
}
