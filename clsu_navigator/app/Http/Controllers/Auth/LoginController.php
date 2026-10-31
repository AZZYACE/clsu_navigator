<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    // /login opens the homepage pop-up
    public function show(Request $request)
    {
        if (Auth::check()) {
            return redirect('/');
        }

        // After logging in, return to the page the visitor came from
        $previous = url()->previous();
        if ($previous !== url()->current() && str_starts_with($previous, url('/'))) {
            $request->session()->put('url.intended', $previous);
        }

        return redirect()->route('home')->with('open_login', true);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Limit repeated wrong passwords (5 per minute for each email and device)
        $limitKey = Str::lower($credentials['email']).'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($limitKey, 5)) {
            $seconds = RateLimiter::availableIn($limitKey);

            return back()
                ->withErrors(['email' => "Too many login attempts. Please try again in {$seconds} seconds."])
                ->onlyInput('email');
        }

        $user = User::where('email', $credentials['email'])->first();

        // Check the password first so account status stays private
        if (!$user || !Hash::check($credentials['password'], $user->password_hash)) {
            RateLimiter::hit($limitKey, 60);

            return back()
                ->withErrors(['email' => 'Incorrect email or password. Check your details, or ask your CLSU administrator.'])
                ->onlyInput('email');
        }

        $status = strtolower((string) $user->account_status);

        if ($status !== 'active') {
            $message = match ($status) {
                'pending' => 'Your account is waiting for activation. Contact your CLSU administrator.',
                'suspended' => 'Your account is suspended. Contact your CLSU administrator.',
                'deactivated' => 'Your account has been deactivated. Contact your CLSU administrator.',
                default => 'Your account is not active. Contact your CLSU administrator.',
            };

            return back()
                ->withErrors(['email' => $message])
                ->onlyInput('email');
        }

        RateLimiter::clear($limitKey);

        Auth::login($user);
        $request->session()->regenerate();

        $user->forceFill([
            'last_login_at' => now(),
        ])->save();

        // Stay on the page the login form was opened from (homepage or campus map)
        $previous = url()->previous();
        $fromLoginPage = str_ends_with((string) parse_url($previous, PHP_URL_PATH), '/login');

        return redirect()->intended($fromLoginPage ? '/' : $previous);
    }
}
