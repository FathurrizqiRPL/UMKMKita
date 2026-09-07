<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $user = User::where('google_id', $googleUser->getId())->first();

            if (!$user) {
                $user = User::where('email', $googleUser->getEmail())->first();

                if ($user) {
                    $user->google_id = $googleUser->getId();

                    if (!$user->email_verified_at) {
                        $user->email_verified_at = now();
                    }

                    $user->save();
                } else {
                    $user = new User();
                    $user->name = $googleUser->getName() ?: 'Pengguna UMKMKita';
                    $user->email = $googleUser->getEmail();
                    $user->google_id = $googleUser->getId();
                    $user->password = Str::random(64);
                    $user->email_verified_at = now();
                    $user->save();
                }
            }

            Auth::login($user, true);
            $request->session()->regenerate();

            return redirect()->intended(route('home'));
        } catch (\Throwable $e) {
            report($e);

            return redirect()
                ->route('login')
                ->withErrors([
                    'google' => 'Login dengan Google gagal. Silakan coba kembali.',
                ]);
        }
    }
}
