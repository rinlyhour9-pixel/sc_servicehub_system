<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30', 'unique:users,phone'],
            'password' => ['required', 'confirmed', PasswordRule::min(8)],
            'device_name' => ['nullable', 'string', 'max:100'],
        ]);

        // Public registration is deliberately client-only: role input is ignored.
        $user = User::create(array_merge($data, ['role' => User::ROLE_CLIENT]));
        $token = $user->createToken($data['device_name'] ?? 'mobile-app')->plainTextToken;

        return response()->json(['user' => $this->user($user), 'token' => $token, 'token_type' => 'Bearer'], 201);
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:100'],
        ]);

        $user = User::where('email', $data['email'])->first();
        if (! $user || ! $user->is_active || ! Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages(['email' => ['The supplied credentials are invalid.']]);
        }

        $token = $user->createToken($data['device_name'] ?? 'mobile-app')->plainTextToken;

        return response()->json(['user' => $this->user($user), 'token' => $token, 'token_type' => 'Bearer']);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->noContent();
    }

    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => ['required', 'email']]);
        $status = Password::sendResetLink($request->only('email'));
        if ($status !== Password::RESET_LINK_SENT) {
            throw ValidationException::withMessages(['email' => [__($status)]]);
        }

        return response()->json(['message' => __($status)]);
    }

    public function resetPassword(Request $request)
    {
        $data = $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', PasswordRule::min(8)],
        ]);
        $status = Password::reset($data, function (User $user, string $password) {
            $user->forceFill(['password' => $password, 'remember_token' => Str::random(60)])->save();
            event(new PasswordReset($user));
        });
        if ($status !== Password::PASSWORD_RESET) {
            throw ValidationException::withMessages(['email' => [__($status)]]);
        }

        return response()->json(['message' => __($status)]);
    }

    private function user(User $user): array
    {
        return ['id' => $user->id, 'name' => $user->name, 'email' => $user->email, 'phone' => $user->phone, 'role' => $user->role, 'availability' => $user->availability];
    }
}
