<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Avis;
use App\Models\Demande;

class ExaminerController extends Controller
{
    /**
     * Dashboard examinateur — avis à rendre et complétés.
     */
    public function dashboard()
    {
        $personnel = Auth::user();

        $avisEnAttente = Avis::with(['demande.publication.journal', 'demande.postulant'])
            ->where('id_examinateur', $personnel->id)
            ->whereNull('resultat')
            ->latest()
            ->get();

        $avisComplets = Avis::with(['demande.publication.journal', 'demande.postulant'])
            ->where('id_examinateur', $personnel->id)
            ->whereNotNull('resultat')
            ->latest()
            ->get();

        $stats = [
            'en_attente' => $avisEnAttente->count(),
            'complets'   => $avisComplets->count(),
            'favorables' => $avisComplets->where('resultat', 'favorable')->count(),
        ];

        return view('examiner.dashboard', compact('personnel', 'avisEnAttente', 'avisComplets', 'stats'));
    }

    /**
     * Notifications — nouvelles demandes assignées.
     */
    public function notifications()
    {
        $personnel = Auth::user();

        $nouvelles = Avis::with(['demande.publication', 'demande.postulant'])
            ->where('id_examinateur', $personnel->id)
            ->whereNull('resultat')
            ->latest()
            ->get();

        return view('examiner.notifications', compact('personnel', 'nouvelles'));
    }

    /**
     * Page de review d'une demande.
     */
    public function showReview($id)
    {
        $personnel = Auth::user();

        // S'assurer que l'examinateur est bien assigné à cette demande
        $avis = Avis::where('id_demande', $id)
            ->where('id_examinateur', $personnel->id)
            ->first();

        if (!$avis) {
            abort(403, 'Vous n\'êtes pas assigné à cette demande.');
        }

        $demande = Demande::with(['publication.journal', 'postulant', 'sousCommission'])
            ->findOrFail($id);

        return view('examiner.review', compact('personnel', 'demande', 'avis'));
    }

    /**
     * Soumettre un avis sur une demande.
     */
    public function submitReview(Request $request, $id)
    {
        $personnel = Auth::user();

        $avis = Avis::where('id_demande', $id)
            ->where('id_examinateur', $personnel->id)
            ->firstOrFail();

        $validated = $request->validate([
            'resultat'       => 'required|in:favorable,defavorable,reserve,rejete',
            'commentaire'    => 'required|string|min:20',
            'recommandation' => 'nullable|string',
            'confiance'      => 'nullable|integer|min:0|max:100',
        ]);

        $avis->update([
            'resultat'       => $validated['resultat'],
            'commentaire'    => $validated['commentaire'],
            'recommandation' => $validated['recommandation'] ?? null,
            'confiance'      => $validated['confiance'] ?? 50,
            'date_avis'      => now(),
        ]);

        return redirect()->route('examiner.dashboard')
            ->with('success', 'Votre avis sur la demande #' . $id . ' a été enregistré.');
    }
}
