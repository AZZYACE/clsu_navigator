<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    // /login opens the homepage pop-up
    public function show()
    {
        if (Auth::check()) {
            return redirect('/');
        }

        return redirect()->route('home')->with('open_login', true);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        // Check the password first so account status stays private
        if (!$user || !Hash::check($credentials['password'], $user->password_hash)) {
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

        Auth::login($user);
        $request->session()->regenerate();

        $user->forceFill([
            'last_login_at' => now(),
        ])->save();

        return redirect()->intended('/');
    }
}
