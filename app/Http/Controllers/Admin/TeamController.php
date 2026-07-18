<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class TeamController extends Controller
{
    public function index()
    {
        return view('admin.team.index', ['members' => User::where(fn ($q) => $q->where('role', '!=', 'customer')->orWhere('is_admin', true))->orderBy('name')->get()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate(['name' => 'required|string|max:120', 'email' => 'required|email|max:255|unique:users,email', 'role' => 'required|in:administrator,manager,catalogue,fulfillment,support', 'password' => ['required', 'confirmed', Password::min(12)->letters()->mixedCase()->numbers()]]);
        User::forceCreate(['name' => $data['name'], 'email' => strtolower($data['email']), 'password' => Hash::make($data['password']), 'role' => $data['role'], 'is_admin' => $data['role'] === 'administrator', 'email_verified_at' => now()]);

        return back()->with('success', 'Staff account created. Share the temporary password securely and ask them to change it after sign-in.');
    }

    public function update(Request $request, User $user)
    {
        abort_if($user->is($request->user()), 422, 'You cannot change your own administrator role.');
        $data = $request->validate(['role' => 'required|in:administrator,manager,catalogue,fulfillment,support,customer']);
        if ($user->role === 'administrator' && $data['role'] !== 'administrator') {
            $administratorCount = User::where('role', 'administrator')->orWhere('is_admin', true)->count();
            abort_if($administratorCount <= 1, 422, 'The last administrator cannot be demoted.');
        }
        $user->update(['role' => $data['role'], 'is_admin' => $data['role'] === 'administrator']);

        return back()->with('success', 'Team permissions updated.');
    }
}
