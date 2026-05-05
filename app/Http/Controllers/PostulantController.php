<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Demande;
use App\Models\Publication;
use App\Models\Journal;
use App\Models\SousCommission;

class PostulantController extends Controller
{
    /**
     * Dashboard postulant — résumé des demandes et statuts.
     */
    public function dashboard()
    {
        $personnel = Auth::user();
        $demandes  = Demande::with(['publication.journal', 'sousCommission'])
            ->where('id_postulant', $personnel->id)
            ->latest()
            ->get();

        // Stats rapides
        $stats = [
            'total'     => $demandes->count(),
            'en_cours'  => $demandes->whereNotIn('statut', ['fini', 'transmis'])->count(),
            'favorables'=> $demandes->where('decision_finale', 'favorable')->count(),
            'attente'   => $demandes->where('statut', 'en_attente')->count(),
        ];

        return view('postulant.dashboard', compact('personnel', 'demandes', 'stats'));
    }

    /**
     * Profil du postulant.
     */
    public function profile()
    {
        $personnel = Auth::user();
        $postulant = $personnel->postulant;

        return view('postulant.profile', compact('personnel', 'postulant'));
    }

    /**
     * Mise à jour du profil.
     */
    public function updateProfile(Request $request)
    {
        $personnel = Auth::user();

        $validated = $request->validate([
            'nom'            => 'required|string|max:100',
            'prenom'         => 'required|string|max:100',
            'phone'          => 'nullable|string|max:20',
            'grade'          => 'nullable|string|max:50',
            'departement'    => 'nullable|string|max:100',
            'laboratoire'    => 'nullable|string|max:100',
            'grade_recherche'=> 'nullable|string|max:50',
            'password'       => 'nullable|string|min:8|confirmed',
        ]);

        // Mise à jour Personnel
        $personnel->update([
            'nom'         => $validated['nom'],
            'prenom'      => $validated['prenom'],
            'phone'       => $validated['phone'] ?? $personnel->phone,
            'grade'       => $validated['grade'] ?? $personnel->grade,
            'departement' => $validated['departement'] ?? $personnel->departement,
        ]);

        // Mise à jour profil Postulant (CTI)
        if ($personnel->postulant) {
            $personnel->postulant->update([
                'laboratoire'    => $validated['laboratoire'] ?? null,
                'grade_recherche'=> $validated['grade_recherche'] ?? null,
            ]);
        }

        // Changement de mot de passe
        if (!empty($validated['password'])) {
            $personnel->update(['password' => Hash::make($validated['password'])]);
        }

        return back()->with('success', 'Profil mis à jour avec succès.');
    }

    /**
     * Liste des demandes du postulant + formulaire de soumission.
     */
    public function showRequests()
    {
        $personnel       = Auth::user();
        $demandes        = Demande::with(['publication.journal', 'sousCommission', 'avis'])
            ->where('id_postulant', $personnel->id)
            ->latest()
            ->get();
        $sousCommissions = SousCommission::all();

        return view('postulant.requests', compact('personnel', 'demandes', 'sousCommissions'));
    }

    /**
     * Soumettre une nouvelle demande.
     */
    public function submitRequest(Request $request)
    {
        $personnel = Auth::user();

        $validated = $request->validate([
            'titre'            => 'required|string|max:255',
            'auteur_principal' => 'required|string|max:255',
            'date_publication' => 'required|date',
            'resume'           => 'nullable|string',
            'id_sous_comm'     => 'required|exists:sous_commissions,id',
            'nom_journal'      => 'nullable|string|max:255',
            'issn'             => 'nullable|string|max:20',
            'facteur_impact'   => 'nullable|numeric|min:0',
            'lien_url'         => 'nullable|url',
            'pdf'              => 'nullable|file|mimes:pdf|max:10240',
        ]);

        // 1. Créer la Demande
        $demande = Demande::create([
            'id_postulant' => $personnel->id,
            'id_sous_comm' => $validated['id_sous_comm'],
            'date_demande' => now(),
            'statut'       => 'en_attente',
        ]);

        // 2. Créer la Publication
        $pdfPath = null;
        if ($request->hasFile('pdf')) {
            $pdfPath = $request->file('pdf')->store('publications', 'public');
        }

        $publication = Publication::create([
            'id_demande'       => $demande->id,
            'titre'            => $validated['titre'],
            'auteur_principal' => $validated['auteur_principal'],
            'date_publication' => $validated['date_publication'],
            'resume'           => $validated['resume'] ?? null,
            'pdf_path'         => $pdfPath,
        ]);

        // 3. Créer le Journal si renseigné
        if (!empty($validated['nom_journal'])) {
            Journal::create([
                'id_publication'  => $publication->id,
                'nom_journal'     => $validated['nom_journal'],
                'issn'            => $validated['issn'] ?? null,
                'facteur_impact'  => $validated['facteur_impact'] ?? null,
                'lien_url'        => $validated['lien_url'] ?? null,
            ]);
        }

        return redirect()->route('postulant.requests')
            ->with('success', 'Votre demande a été soumise avec succès. Numéro : #' . $demande->id);
    }
}
