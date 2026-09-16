<?php

namespace App\Http\Controllers\Technician;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function create()
    {
        return view('technician.auth.login');
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'phone' => ['required', 'string'],
            'password' => ['required'],
        ]);

        if (! Auth::guard('technician')->attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'phone' => __('These credentials do not match our records.'),
            ]);
        }

        if (! Auth::guard('technician')->user()->is_active) {
            Auth::guard('technician')->logout();
            throw ValidationException::withMessages([
                'phone' => __('Your account has been deactivated.'),
            ]);
        }

        $request->session()->regenerate();

        // Not using redirect()->intended() here: Laravel stores the intended URL
        // under one session key shared by every guard, so a stale admin-guard
        // redirect could otherwise send a freshly-logged-in technician back to
        // the admin dashboard instead of their own.
        return redirect()->route('technician.dashboard');
    }

    public function destroy(Request $request)
    {
        Auth::guard('technician')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('technician.login');
    }
}
