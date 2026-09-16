<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showRegister()
    {
        return view('client.auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30', 'unique:customers,phone'],
            'email' => ['nullable', 'email', 'max:255'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $customer = Customer::create([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'email' => $data['email'] ?? null,
            'password' => Hash::make($data['password']),
        ]);

        Auth::guard('client')->login($customer);
        $request->session()->regenerate();

        return redirect()->route('client.dashboard');
    }

    public function create()
    {
        return view('client.auth.login');
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'phone' => ['required', 'string'],
            'password' => ['required'],
        ]);

        if (! Auth::guard('client')->attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'phone' => __('These credentials do not match our records.'),
            ]);
        }

        if (! Auth::guard('client')->user()->is_active) {
            Auth::guard('client')->logout();
            throw ValidationException::withMessages([
                'phone' => __('Your account has been deactivated.'),
            ]);
        }

        $request->session()->regenerate();

        // Not redirect()->intended(): the intended-URL session key is shared
        // across every guard, so a stale admin-guard redirect could otherwise
        // send a freshly-logged-in client to the admin dashboard instead.
        return redirect()->route('client.dashboard');
    }

    public function destroy(Request $request)
    {
        Auth::guard('client')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('client.login');
    }
}
