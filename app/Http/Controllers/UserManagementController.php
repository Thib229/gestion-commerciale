<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    public function index(Request $request): View
    {
        $admin = $request->user();

        $users = User::query()
            ->where('entreprise_id', $admin->entreprise_id)
            ->orderByRaw("CASE WHEN role = 'admin' THEN 0 ELSE 1 END")
            ->orderBy('name')
            ->get();

        return view('users.index', compact('users'));
    }

    public function store(Request $request): RedirectResponse
    {
        $admin = $request->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'confirmed', Password::defaults()],
            'staff_role' => ['required', 'string', Rule::in(['comptable', 'secretaire', 'caissier', 'commercial', 'autre'])],
        ]);

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'entreprise_id' => $admin->entreprise_id,
            'role' => 'staff',
            'staff_role' => $data['staff_role'],
            'subscription_status' => $admin->subscription_status,
        ]);

        return back()->with('success', 'Employé ajouté avec succès.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        $admin = $request->user();

        abort_if($user->entreprise_id !== $admin->entreprise_id, 403);
        abort_if($user->id === $admin->id, 422, 'Vous ne pouvez pas supprimer votre propre compte.');
        abort_if($user->role === 'admin', 422, 'Suppression d’un administrateur interdite.');

        $user->delete();

        return back()->with('success', 'Employé supprimé avec succès.');
    }
}
