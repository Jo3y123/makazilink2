<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        $users = User::latest()->get();
        return view('settings.users', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'phone'    => 'nullable|string|max:20',
            'role'     => 'required|in:admin,agent,accountant,caretaker,tenant',
            'password' => 'required|string|min:6',
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'role'     => $request->role,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('settings.users')
            ->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        return view('settings.edit-user', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'role'  => 'required|in:admin,agent,accountant,caretaker,tenant',
        ]);

        $user->update([
            'name'      => $request->name,
            'phone'     => $request->phone,
            'role'      => $request->role,
            'is_active' => $request->boolean('is_active'),
        ]);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        return redirect()->route('settings.users')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('settings.users')
                ->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('settings.users')
            ->with('success', 'User deleted successfully.');
    }
}