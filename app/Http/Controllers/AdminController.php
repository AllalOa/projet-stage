<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Personnel;
use App\Models\Postulant;
use App\Models\Examinateur;
use App\Models\PresidentSousCommission;
use App\Models\MembreConseil;
use App\Models\SousCommission;

class AdminController extends Controller
{
    /**
     * Liste de tous les utilisateurs / personnels.
     */
    public function users(Request $request)
    {
        $personnel = Auth::user();
        $role      = $request->get('role', '');
        $search    = $request->get('search', '');

        $personnels = Personnel::query()
            ->when($role, fn($q) => $q->where('role', $role))
            ->when($search, fn($q) => $q->where(function ($q) use ($search) {
                $q->where('nom', 'like', "%$search%")
                  ->orWhere('prenom', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%");
            }))
            ->orderBy('nom')
            ->paginate(20);

        $roles = [
            'postulant'        => 'Postulant',
            'examiner'         => 'Examinateur',
            'president-sub'    => 'Président Sous-Commission',
            'president-council'=> 'Président Conseil',
            'admin'            => 'Admin',
        ];

        return view('admin.users', compact('personnel', 'personnels', 'roles', 'role', 'search'));
    }

    /**
     * Créer un nouvel utilisateur avec son profil CTI.
     */
    public function createUser(Request $request)
    {
        $validated = $request->validate([
            'nom'         => 'required|string|max:100',
            'prenom'      => 'required|string|max:100',
            'email'       => 'required|email|unique:personnels,email',
            'password'    => 'required|string|min:8',
            'role'        => 'required|in:postulant,examiner,president-sub,president-council,admin',
            'grade'       => 'nullable|string|max:50',
            'departement' => 'nullable|string|max:100',
            'phone'       => 'nullable|string|max:20',
        ]);

        // 1. Créer le Personnel de base
        $p = Personnel::create([
            'nom'         => $validated['nom'],
            'prenom'      => $validated['prenom'],
            'email'       => $validated['email'],
            'password'    => Hash::make($validated['password']),
            'role'        => $validated['role'],
            'grade'       => $validated['grade'] ?? null,
            'departement' => $validated['departement'] ?? null,
            'phone'       => $validated['phone'] ?? null,
        ]);

        // 2. Créer le profil CTI selon le rôle
        match ($validated['role']) {
            'postulant'        => Postulant::create(['id_personnel' => $p->id]),
            'examiner'         => Examinateur::create(['id_personnel' => $p->id]),
            'president-sub'    => PresidentSousCommission::create(['id_personnel' => $p->id, 'date_nomination' => now()]),
            'president-council','admin' => null,
            default            => null,
        };

        return redirect()->route('admin.users')
            ->with('success', 'Utilisateur ' . $p->prenom . ' ' . $p->nom . ' créé avec succès.');
    }

    /**
     * Supprimer un utilisateur.
     */
    public function deleteUser($id)
    {
        $p = Personnel::findOrFail($id);

        // Empêcher la suppression de son propre compte
        if (Auth::id() === $p->id) {
            return back()->withErrors(['delete' => 'Vous ne pouvez pas supprimer votre propre compte.']);
        }

        $name = $p->prenom . ' ' . $p->nom;
        $p->delete();

        return redirect()->route('admin.users')
            ->with('success', 'Utilisateur ' . $name . ' supprimé avec succès.');
    }

    /**
     * Gestion des sous-commissions.
     */
    public function commissions()
    {
        $personnel       = Auth::user();
        $sousCommissions = SousCommission::with(['president', 'demandes'])->get();
        $presidents      = Personnel::where('role', 'president-sub')->get();

        return view('admin.subcommissions', compact('personnel', 'sousCommissions', 'presidents'));
    }

    /**
     * Créer une nouvelle sous-commission.
     */
    public function createCommission(Request $request)
    {
        $validated = $request->validate([
            'nom'             => 'required|string|max:100',
            'domaine'         => 'nullable|string|max:100',
            'id_president_sc' => 'nullable|exists:personnels,id',
        ]);

        // Récupérer ou créer le conseil scientifique par défaut
        $conseil = \Illuminate\Support\Facades\DB::table('conseil_scientifiques')->first();

        SousCommission::create([
            'nom'             => $validated['nom'],
            'domaine'         => $validated['domaine'] ?? null,
            'id_conseil'      => $conseil ? $conseil->id : 1,
            'id_president_sc' => $validated['id_president_sc'] ?? null,
            'date_creation'   => now(),
        ]);

        return redirect()->route('admin.commissions')
            ->with('success', 'Sous-commission "' . $validated['nom'] . '" créée.');
    }

    /**
     * Gestion du personnel (vue dédiée admin).
     */
    public function personnelManagement()
    {
        $personnel  = Auth::user();
        $personnels = Personnel::orderBy('role')->orderBy('nom')->get();

        return view('admin.personnel-management', compact('personnel', 'personnels'));
    }
}
