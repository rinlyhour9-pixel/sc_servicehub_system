<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Technician;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ManageUsersController extends Controller
{
    public function index(Request $request)
    {
        $tab = in_array($request->input('tab'), ['administrator', 'technician', 'client'], true)
            ? $request->input('tab')
            : 'administrator';

        $administrators = $tab === 'administrator' ? User::orderBy('name')->paginate(15) : null;
        $technicians = $tab === 'technician' ? Technician::orderBy('name')->paginate(15) : null;
        $clients = $tab === 'client' ? Customer::orderBy('name')->paginate(15) : null;

        return view('manage-users.index', compact('tab', 'administrators', 'technicians', 'clients'));
    }

    public function resetPassword(Request $request, string $role, int $id)
    {
        $model = match ($role) {
            'administrator' => User::findOrFail($id),
            'technician' => Technician::findOrFail($id),
            'client' => Customer::findOrFail($id),
            default => abort(404),
        };

        $data = $request->validate([
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $model->update(['password' => Hash::make($data['password'])]);

        return back()->with('status', ucfirst($role).' password updated.');
    }
}
