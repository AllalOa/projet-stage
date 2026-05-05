<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Demande;
use App\Models\Avis;
use App\Models\Personnel;
use App\Models\SousCommission;

class SubCommissionController extends Controller
{
    /**
     * Dashboard de la sous-commission.
     */
    public function dashboard()
    {
        $personnel     = Auth::user();
        $sousComm      = SousCommission::where('id_president_sc', $personnel->id)->first();

        $demandes = $sousComm
            ? Demande::with(['postulant', 'publication', 'avis'])
                ->where('id_sous_comm', $sousComm->id)
                ->latest()
                ->get()
            : collect();

        $stats = [
            'total'        => $demandes->count(),
            'en_attente'   => $demandes->where('statut', 'en_attente')->count(),
            'en_cours'     => $demandes->whereIn('statut', ['reception', 'recevabilite', 'proposition', 'avis'])->count(),
            'consolidees'  => $demandes->where('statut', 'consolidation')->count(),
            'transmises'   => $demandes->where('statut', 'transmis')->count(),
        ];

        return view('subcommission.dashboard', compact('personnel', 'sousComm', 'demandes', 'stats'));
    }

    /**
     * Liste des demandes à traiter.
     */
    public function requests()
    {
        $personnel = Auth::user();
        $sousComm  = SousCommission::where('id_president_sc', $personnel->id)->first();

        $demandes = $sousComm
            ? Demande::with(['postulant', 'publication.journal', 'avis'])
                ->where('id_sous_comm', $sousComm->id)
                ->latest()
                ->get()
            : collect();

        return view('subcommission.requests', compact('personnel', 'sousComm', 'demandes'));
    }


    /**
     * Page d'assignation d'examinateurs à une demande.
     */
    public function assign($id)
    {
        $personnel           = Auth::user();
        $demande             = Demande::with(['postulant', 'publication.journal', 'avis.examinateur'])->findOrFail($id);
        $examinateursAssignes = $demande->avis->pluck('id_examinateur')->toArray();
        $examinateurs        = Personnel::where('role', 'examiner')
            ->whereNotIn('id', $examinateursAssignes)
            ->get();

        return view('subcommission.assign', compact('personnel', 'demande', 'examinateurs', 'examinateursAssignes'));
    }

    /**
     * Assigner des examinateurs — crée les Avis vides et change le statut.
     * (L'étape de contrôle de recevabilité a été supprimée : on passe directement de
     *  'en_attente' à 'avis' lors de l'assignation.)
     */
    public function doAssign(Request $request, $id)
    {
        $request->validate([
            'examinateurs'   => 'required|array|min:1',
            'examinateurs.*' => 'exists:personnels,id',
        ]);

        $demande = Demande::findOrFail($id);

        foreach ($request->examinateurs as $examId) {
            Avis::firstOrCreate([
                'id_demande'      => $demande->id,
                'id_examinateur'  => $examId,
            ]);
        }

        // Passe directement à 'avis' quel que soit le statut précédent
        $demande->update(['statut' => 'avis']);

        return redirect()->route('subcommission.requests')
            ->with('success', count($request->examinateurs) . ' examinateur(s) assigné(s) à la demande #' . $id);
    }

    /**
     * Tous les avis reçus pour les demandes de la sous-commission.
     */
    public function reviews()
    {
        $personnel = Auth::user();
        $sousComm  = SousCommission::where('id_president_sc', $personnel->id)->first();

        $demandes = $sousComm
            ? Demande::with(['postulant', 'publication', 'avis.examinateur'])
                ->where('id_sous_comm', $sousComm->id)
                ->whereIn('statut', ['avis', 'consolidation'])
                ->latest()
                ->get()
            : collect();

        return view('subcommission.reviews', compact('personnel', 'sousComm', 'demandes'));
    }

    /**
     * Page de consolidation d'une demande.
     */
    public function consolidate($id)
    {
        $personnel = Auth::user();
        $demande   = Demande::with(['postulant', 'publication.journal', 'avis.examinateur'])->findOrFail($id);

        return view('subcommission.consolidate', compact('personnel', 'demande'));
    }

    /**
     * Enregistrer la décision consolidée.
     */
    public function doConsolidate(Request $request, $id)
    {
        $validated = $request->validate([
            'decision_finale' => 'required|in:favorable,defavorable,reserve',
        ]);

        $demande = Demande::findOrFail($id);
        $demande->update([
            'statut'          => 'consolidation',
            'decision_finale' => $validated['decision_finale'],
            'date_decision'   => now(),
        ]);

        return redirect()->route('subcommission.transmit')
            ->with('success', 'Décision consolidée enregistrée pour la demande #' . $id);
    }

    /**
     * Dossiers prêts à transmettre au président.
     */
    public function transmit()
    {
        $personnel = Auth::user();
        $sousComm  = SousCommission::where('id_president_sc', $personnel->id)->first();

        $dossiers = $sousComm
            ? Demande::with(['postulant', 'publication', 'avis'])
                ->where('id_sous_comm', $sousComm->id)
                ->where('statut', 'consolidation')
                ->latest()
                ->get()
            : collect();

        $transmis = $sousComm
            ? Demande::with(['postulant', 'publication'])
                ->where('id_sous_comm', $sousComm->id)
                ->where('statut', 'transmis')
                ->latest()
                ->get()
            : collect();

        return view('subcommission.transmit', compact('personnel', 'sousComm', 'dossiers', 'transmis'));
    }

    /**
     * Transmettre un dossier au président du conseil.
     */
    public function doTransmit(Request $request, $id)
    {
        $demande = Demande::findOrFail($id);
        $demande->update(['statut' => 'transmis']);

        return redirect()->route('subcommission.transmit')
            ->with('success', 'Dossier #' . $id . ' transmis au Conseil Scientifique.');
    }
}
