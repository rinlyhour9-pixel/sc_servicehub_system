<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        return response()->json(['user' => $this->user($request->user())]);
    }

    public function update(Request $request)
    {
        $user = $request->user();
        $data = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => ['sometimes', 'nullable', 'string', 'max:30', Rule::unique('users')->ignore($user->id)],
        ]);
        $user->update($data);

        return response()->json(['user' => $this->user($user->fresh())]);
    }

    private function user($user): array
    {
        return ['id' => $user->id, 'name' => $user->name, 'email' => $user->email, 'phone' => $user->phone, 'role' => $user->role, 'availability' => $user->availability];
    }
}
