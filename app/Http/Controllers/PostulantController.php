<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Demande;
use App\Models\Publication;
use App\Models\Journal;
use App\Models\Manifestation;
use App\Models\SousCommission;

class PostulantController extends Controller
{
    /**
     * Dashboard postulant — résumé des demandes et statuts.
     */
    public function dashboard()
    {
        $personnel = Auth::user();
        $demandes  = Demande::with(['publication.journal', 'publication.manifestation', 'sousCommission'])
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

        $sousCommissions = SousCommission::all();

        return view('postulant.dashboard', compact('personnel', 'demandes', 'stats', 'sousCommissions'));
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
        $demandes        = Demande::with(['publication.journal', 'publication.manifestation', 'sousCommission', 'avis'])
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
            'type_publication'   => 'required|in:journal,manifestation',
            'titre'              => 'required|string|max:255',
            'auteur_principal'   => 'required|string|max:255',
            'date_publication'   => 'required|date',
            'resume'             => 'nullable|string',
            'id_sous_comm'       => 'required|exists:sous_commissions,id',
            // Champs Journal
            'nom_journal'        => 'nullable|string|max:255',
            'issn'               => 'nullable|string|max:20',
            'facteur_impact'     => 'nullable|numeric|min:0',
            'lien_url'           => 'nullable|url',
            // Champs Manifestation
            'nom_manifestation'  => 'nullable|string|max:255',
            'type_manifestation' => 'nullable|string|max:50',
            'date_event'         => 'nullable|date',
            'lieu'               => 'nullable|string|max:255',
            // PDF
            'pdf'                => 'required|file|mimes:pdf|max:10240',
        ]);

        // Vérification manuelle des champs requis selon le type
        if ($validated['type_publication'] === 'journal' && empty($validated['nom_journal'])) {
            return back()->withErrors(['nom_journal' => 'Le nom du journal est obligatoire.'])->withInput();
        }
        if ($validated['type_publication'] === 'manifestation' && empty($validated['nom_manifestation'])) {
            return back()->withErrors(['nom_manifestation' => 'Le nom de la manifestation est obligatoire.'])->withInput();
        }

        // 1. Créer la Demande
        $demande = Demande::create([
            'id_postulant' => $personnel->id,
            'id_sous_comm' => $validated['id_sous_comm'],
            'date_demande' => now(),
            'statut'       => 'en_attente',
        ]);

        // 2. Créer la Publication (base commune)
        $pdfPath = $request->file('pdf')->store('publications', 'public');

        $publication = Publication::create([
            'id_demande'       => $demande->id,
            'titre'            => $validated['titre'],
            'auteur_principal' => $validated['auteur_principal'],
            'date_publication' => $validated['date_publication'],
            'resume'           => $validated['resume'] ?? null,
            'pdf_path'         => $pdfPath,
        ]);

        // 3. Créer la spécialisation selon le type
        if ($validated['type_publication'] === 'journal') {
            Journal::create([
                'id_publication' => $publication->id,
                'nom_journal'    => $validated['nom_journal'],
                'issn'           => $validated['issn'] ?? null,
                'facteur_impact' => $validated['facteur_impact'] ?? null,
                'lien_url'       => $validated['lien_url'] ?? null,
            ]);
        } else {
            Manifestation::create([
                'id_publication'    => $publication->id,
                'nom_manifestation' => $validated['nom_manifestation'],
                'type_manifestation'=> $validated['type_manifestation'] ?? null,
                'date_event'        => $validated['date_event'] ?? null,
                'lieu'              => $validated['lieu'] ?? null,
            ]);
        }

        return redirect()->route('postulant.requests')
            ->with('success', 'Votre demande a été soumise avec succès. Numéro : #' . $demande->id);
    }
}
