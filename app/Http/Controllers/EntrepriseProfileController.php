<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\EntrepriseProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EntrepriseProfileController extends Controller
{
    public function edit()
    {
        $profile = Auth::user()->entrepriseProfile;
        return view('entreprise.edit', compact('profile'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'nom'           => 'required|string|max:255',
            'adresse'       => 'nullable|string|max:500',
            'telephone'     => 'nullable|string|max:50',
            'email'         => 'nullable|email|max:255',
            'numero_fiscal' => 'nullable|string|max:100',
            'logo'          => 'nullable|image|mimes:jpeg,png,webp|max:2048',
        ]);

        $data = [
            'nom'           => $validated['nom'],
            'adresse'       => $validated['adresse'] ?? null,
            'telephone'     => $validated['telephone'] ?? null,
            'email'         => $validated['email'] ?? null,
            'numero_fiscal' => $validated['numero_fiscal'] ?? null,
        ];

        if ($request->hasFile('logo')) {
            // Supprimer l'ancien logo si existant
            $existing = Auth::user()->entrepriseProfile;
            if ($existing && $existing->logo_path) {
                Storage::disk('public')->delete($existing->logo_path);
            }
            $data['logo_path'] = $request->file('logo')->store('logos', 'public');
        }

        $profile = EntrepriseProfile::updateOrCreate(
            ['user_id' => Auth::id()],
            $data
        );

        // Log de l'activité
        ActivityLog::create([
            'user_id'      => Auth::id(),
            'action'       => 'entreprise.updated',
            'subject_type' => EntrepriseProfile::class,
            'subject_id'   => $profile->id,
            'description'  => 'Profil entreprise mis à jour',
        ]);

        return redirect()->route('entreprise.edit')
            ->with('success', 'Profil entreprise mis à jour avec succès.');
    }
}
