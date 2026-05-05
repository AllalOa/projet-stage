<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Demande;
use App\Models\SousCommission;
use App\Models\Personnel;

class PresidentController extends Controller
{
    /**
     * Dashboard président du conseil — vue globale.
     */
    public function dashboard()
    {
        $personnel = Auth::user();

        $stats = [
            'transmis'    => Demande::where('statut', 'transmis')->count(),
            'en_cours'    => Demande::whereNotIn('statut', ['fini', 'transmis', 'en_attente'])->count(),
            'favorables'  => Demande::where('decision_finale', 'favorable')->count(),
            'total'       => Demande::count(),
        ];

        $dernieresDemandes = Demande::with(['postulant', 'publication', 'sousCommission'])
            ->where('statut', 'transmis')
            ->latest()
            ->take(5)
            ->get();

        return view('president.dashboard', compact('personnel', 'stats', 'dernieresDemandes'));
    }

    /**
     * Tous les dossiers transmis.
     */
    public function dossiers(Request $request)
    {
        $personnel = Auth::user();
        $statut    = $request->get('statut', 'transmis');

        $dossiers = Demande::with(['postulant', 'publication.journal', 'sousCommission', 'avis'])
            ->when($statut !== 'tous', fn($q) => $q->where('statut', $statut))
            ->latest()
            ->paginate(15);

        return view('president.dossiers', compact('personnel', 'dossiers', 'statut'));
    }

    /**
     * Détail d'un dossier avec tous les avis.
     */
    public function dossierDetail($id)
    {
        $personnel = Auth::user();
        $demande   = Demande::with([
            'postulant.postulant',
            'publication.journal',
            'sousCommission',
            'avis.examinateur',
        ])->findOrFail($id);

        return view('president.dossier-detail', compact('personnel', 'demande'));
    }

    /**
     * Enregistrer la décision finale du président.
     */
    public function decide(Request $request, $id)
    {
        $validated = $request->validate([
            'decision_finale' => 'required|in:favorable,defavorable,reserve',
        ]);

        $demande = Demande::findOrFail($id);
        $demande->update([
            'decision_finale' => $validated['decision_finale'],
            'date_decision'   => now(),
            'statut'          => 'fini',
        ]);

        return redirect()->route('president.dossiers')
            ->with('success', 'Décision finale enregistrée pour le dossier #' . $id);
    }

    /**
     * Gestion des sous-commissions.
     */
    public function commissions()
    {
        $personnel      = Auth::user();
        $sousCommissions = SousCommission::with(['president', 'demandes'])->get();
        $presidentsDisponibles = Personnel::where('role', 'president-sub')->get();

        return view('president.commissions', compact('personnel', 'sousCommissions', 'presidentsDisponibles'));
    }
}
