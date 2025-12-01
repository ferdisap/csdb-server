<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

class RegisterController extends Controller
{
  public function signUp()
  {
    return view('auth.register.signUp');
  }

  public function register(Request $request)
  {
    $redirect_to = $request->redirect_to;
    // dd('ff', Auth::check());

    $data = $request->validate([
      'name' => ['required', 'string', 'max:255'],
      'email' => ['required', 'email', 'max:255', 'unique:users'],
      'password' => ['required', 'confirmed', 'min:8'],  // you can use stronger rules
    ]);

    // check user in db
    if (User::where('email', $data['email'])->first()) {
      return response(null, 302)->json([
        "message" => $data['email'] . " has been registered.",
        "redirect_to" => config("app.url") . "/login?redirect_to=" . $redirect_to ?? config("app.url") . "/dashboard"
      ]);
    }

    event(new Registered(
      User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password)
      ])
    ));

    return (new LoginController)->authenticate($request);
  }

  public function notice()
  {
    return view('auth.register.verify');
  }

  public function verify(EmailVerificationRequest $request)
  {
    $request->fulfill();
    return view('auth.register.verified');
  }

  public function send(Request $request)
  {
    if ($request->user()->hasVerifiedEmail()) {
      return response(null, 200)->json([
        "message" => "Your account has been verified."
      ]);
    } else {
      $request->user()->sendEmailVerificationNotification();
      return response(null, 202)->json([
        "message" => "Verification link sent to your email!"
      ]);
    }
  }
}
