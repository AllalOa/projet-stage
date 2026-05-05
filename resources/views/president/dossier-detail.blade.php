@extends('layouts.app')

@section('content')

@php
    $dossierData = [
        'id'          => 'REQ-' . str_pad($demande->id, 4, '0', STR_PAD_LEFT),
        'real_id'     => $demande->id,
        'title'       => $demande->publication->titre ?? 'Sans titre',
        'postulant'   => ($demande->postulant->prenom ?? '') . ' ' . ($demande->postulant->nom ?? ''),
        'grade'       => $demande->postulant->grade ?? 'Aucun',
        'dept'        => $demande->postulant->departement ?? 'Aucun',
        'type'        => $demande->publication->journal ? 'Journal' : 'Manifestation',
        'source'      => $demande->publication->journal->nom_journal ?? '—',
        'issn'        => $demande->publication->journal->issn ?? '—',
        'date'        => $demande->created_at->format('Y-m-d'),
        'commission'  => $demande->sousCommission->nom ?? 'N/A',
        'president_sc'=> ($demande->sousCommission->president->prenom ?? '') . ' ' . ($demande->sousCommission->president->nom ?? ''),
        'avis_sc'     => $demande->decision_finale ?? '—',
        'pdf_path'    => $demande->publication->pdf_path ? asset('storage/' . $demande->publication->pdf_path) : '',
    ];

    $examinersData = $demande->avis->filter(fn($a) => $a->resultat)->map(function($a) {
        return [
            'name'    => ($a->examinateur->prenom ?? '') . ' ' . ($a->examinateur->nom ?? ''),
            'avis'    => $a->resultat,
            'comment' => $a->commentaire ?? '',
        ];
    })->values()->toArray();
@endphp

<script>
function presidentDossier() {
    return {
        step:     1,
        decision: '',
        comment:  '',
        decided:  false,
        dossier:  @json($dossierData),
        examiners:@json($examinersData),

        submitDecision() {
            this.$refs.decisionForm.submit();
        }
    };
}
</script>

<div class="max-w-4xl mx-auto space-y-6" x-data="presidentDossier()">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-slate-400">
        <a href="/president/dossiers" @click.prevent="navigateTo('president/dossiers')"
           class="hover:text-amber-600 transition-colors">Dossiers Reçus</a>
        <i class="fa-solid fa-chevron-right text-[10px]"></i>
        <span class="text-slate-700 font-bold">Délibération</span>
    </div>

    {{-- Dossier Header --}}
    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
        <div class="flex items-center gap-3 mb-2">
            <span class="text-[10px] font-black text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded tracking-tighter uppercase"
                  x-text="dossier.id"></span>
            <span class="text-xs font-bold px-2 py-1 rounded-full bg-indigo-100 text-indigo-700"
                  x-text="dossier.type"></span>
            <span class="text-xs font-bold px-2 py-1 rounded-full border"
                  :class="dossier.avis_sc === 'favorable'
                      ? 'bg-emerald-50 text-emerald-700 border-emerald-200'
                      : (dossier.avis_sc === 'defavorable'
                          ? 'bg-rose-50 text-rose-700 border-rose-200'
                          : 'bg-amber-50 text-amber-700 border-amber-200')"
                  x-text="'Avis SC : ' + (dossier.avis_sc || 'En attente')"></span>
        </div>
        <h2 class="text-xl font-extrabold text-slate-900" x-text="dossier.title"></h2>
        <p class="text-sm text-slate-500 mt-1">
            <span x-text="dossier.postulant"></span>
            <span class="mx-1">·</span>
            <span x-text="dossier.source"></span>
            <span class="mx-1">·</span>
            Commission : <span x-text="dossier.commission"></span>
        </p>
    </div>

    {{-- Step Progress --}}
    <div class="flex items-center bg-white border border-slate-200 rounded-2xl p-4">
        @foreach([1 => 'Consulter Pièces', 2 => 'Synthèse Avis', 3 => 'Décision Finale'] as $n => $label)
        <div class="flex-1 flex flex-col items-center gap-1.5">
            <div class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-sm transition-all"
                 :class="step >= {{ $n }} ? 'bg-amber-600 text-white shadow-lg shadow-amber-200' : 'bg-white border-2 border-slate-200 text-slate-400'">
                <span x-show="step > {{ $n }}"><i class="fa-solid fa-check text-xs"></i></span>
                <span x-show="step <= {{ $n }}">{{ $n }}</span>
            </div>
            <span class="text-[10px] font-bold uppercase tracking-wider"
                  :class="step >= {{ $n }} ? 'text-amber-600' : 'text-slate-400'">{{ $label }}</span>
        </div>
        @if($n < 3)<div class="flex-shrink-0 w-12 h-0.5 bg-slate-200 mt-[-18px]"></div>@endif
        @endforeach
    </div>

    {{-- Success --}}
    <template x-if="decided">
        <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-8 text-center space-y-4">
            <div class="w-16 h-16 bg-emerald-100 rounded-2xl flex items-center justify-center mx-auto">
                <i class="fa-solid fa-gavel text-emerald-600 text-2xl"></i>
            </div>
            <h3 class="text-xl font-bold text-emerald-800">Décision Enregistrée</h3>
            <p class="text-sm text-emerald-600">La décision a été enregistrée. Le postulant sera notifié.</p>
            <a href="/president/dossiers" @click.prevent="navigateTo('president/dossiers')"
               class="inline-flex items-center gap-2 px-6 py-3 text-sm font-bold bg-amber-600 text-white rounded-xl hover:bg-amber-700 transition-all shadow-lg shadow-amber-200">
                <i class="fa-solid fa-arrow-left"></i> Retour aux dossiers
            </a>
        </div>
    </template>

    <template x-if="!decided">
        {{-- ✅ URL corrigée : /president/dossier/{id}/decide --}}
        <form method="POST"
              :action="'/president/dossier/' + dossier.real_id + '/decide'"
              x-ref="decisionForm">
            @csrf
            <input type="hidden" name="decision_finale" :value="decision">
            <input type="hidden" name="commentaire_decision" :value="comment">

            {{-- STEP 1: Consulter Pièces --}}
            <div x-show="step === 1" x-transition>
                <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100">
                        <h3 class="text-lg font-bold text-slate-800">
                            <i class="fa-solid fa-folder-open text-amber-600 mr-2"></i>Pièces du Dossier
                        </h3>
                    </div>
                    <div class="p-6 space-y-5">
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
                            <div>
                                <p class="text-xs font-bold text-slate-400 uppercase mb-1">Postulant</p>
                                <p class="font-bold text-slate-800" x-text="dossier.postulant"></p>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-slate-400 uppercase mb-1">Grade</p>
                                <p class="font-bold text-slate-800" x-text="dossier.grade"></p>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-slate-400 uppercase mb-1">Département</p>
                                <p class="font-bold text-slate-800" x-text="dossier.dept"></p>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-slate-400 uppercase mb-1">Source</p>
                                <p class="font-bold text-slate-800" x-text="dossier.source"></p>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-slate-400 uppercase mb-1">ISSN</p>
                                <p class="font-bold text-slate-800" x-text="dossier.issn"></p>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-slate-400 uppercase mb-1">Date de soumission</p>
                                <p class="font-bold text-slate-800" x-text="dossier.date"></p>
                            </div>
                        </div>

                        <div class="border-2 border-dashed border-slate-200 rounded-2xl p-6 text-center bg-slate-50">
                            <i class="fa-solid fa-file-pdf text-4xl text-red-400 mb-3 block"></i>
                            <p class="font-bold text-slate-600 mb-3">Publication PDF</p>
                            <template x-if="dossier.pdf_path">
                                <a :href="dossier.pdf_path" target="_blank"
                                   class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-bold bg-rose-50 text-rose-600 border border-rose-200 hover:bg-rose-100 rounded-xl transition-all">
                                    <i class="fa-solid fa-file-pdf"></i> Ouvrir le PDF
                                </a>
                            </template>
                            <template x-if="!dossier.pdf_path">
                                <p class="text-xs text-slate-400 italic">Aucun fichier fourni</p>
                            </template>
                        </div>

                        <div class="flex justify-end pt-4 border-t border-slate-100">
                            <button type="button" @click="step = 2"
                                class="flex items-center gap-2 text-sm font-bold px-6 py-3 rounded-xl bg-amber-600 hover:bg-amber-700 text-white shadow-lg shadow-amber-200 transition-all">
                                Suivant <i class="fa-solid fa-arrow-right"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- STEP 2: Synthèse des Avis --}}
            <div x-show="step === 2" x-transition>
                <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100">
                        <h3 class="text-lg font-bold text-slate-800">
                            <i class="fa-solid fa-comments text-amber-600 mr-2"></i>Synthèse des Avis
                        </h3>
                    </div>
                    <div class="p-6 space-y-5">

                        {{-- SC Recommendation --}}
                        <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="text-xs font-bold text-amber-700 uppercase tracking-widest">
                                    Recommandation de la Sous-Commission
                                </h4>
                                <span class="text-xs font-bold px-3 py-1 rounded-full border"
                                      :class="dossier.avis_sc === 'favorable'
                                          ? 'bg-emerald-50 text-emerald-700 border-emerald-200'
                                          : (dossier.avis_sc === 'defavorable'
                                              ? 'bg-rose-50 text-rose-700 border-rose-200'
                                              : 'bg-amber-50 text-amber-700 border-amber-200')"
                                      x-text="dossier.avis_sc === 'favorable' ? '✅ Favorable'
                                              : (dossier.avis_sc === 'defavorable' ? '❌ Défavorable' : '— En attente')">
                                </span>
                            </div>
                            <p class="text-xs text-amber-600 mt-2">
                                — <span x-text="dossier.president_sc"></span>,
                                Président de la sous-commission <span x-text="dossier.commission"></span>
                            </p>
                        </div>

                        {{-- Avis examinateurs --}}
                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest">
                            Avis des Examinateurs (<span x-text="examiners.length"></span>)
                        </h4>

                        <template x-if="examiners.length === 0">
                            <div class="text-center py-6 text-slate-400 italic text-sm">
                                <i class="fa-solid fa-circle-info text-slate-300 text-xl mb-2 block"></i>
                                Aucun avis d'examinateur disponible.
                            </div>
                        </template>

                        <div class="space-y-3">
                            <template x-for="(ex, i) in examiners" :key="i">
                                <div class="border rounded-xl p-4"
                                     :class="ex.avis === 'favorable'
                                         ? 'border-emerald-200 bg-emerald-50/30'
                                         : (ex.avis === 'reserve'
                                             ? 'border-amber-200 bg-amber-50/30'
                                             : 'border-rose-200 bg-rose-50/30')">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center gap-3">
                                            <img :src="'https://ui-avatars.com/api/?name=' + encodeURIComponent(ex.name) + '&background=random&size=36'"
                                                 class="w-9 h-9 rounded-xl" alt="">
                                            <p class="font-bold text-sm text-slate-800" x-text="ex.name"></p>
                                        </div>
                                        <span class="text-xs font-bold px-3 py-1 rounded-full border"
                                              :class="ex.avis === 'favorable'
                                                  ? 'bg-emerald-50 text-emerald-700 border-emerald-200'
                                                  : (ex.avis === 'reserve'
                                                      ? 'bg-amber-50 text-amber-700 border-amber-200'
                                                      : 'bg-rose-50 text-rose-700 border-rose-200')"
                                              x-text="ex.avis === 'favorable' ? '✅ Favorable'
                                                      : (ex.avis === 'reserve' ? '✋ Réservé' : '❌ Défavorable')">
                                        </span>
                                    </div>
                                    <p class="text-sm text-slate-600 italic" x-text="ex.comment ? ('«' + ex.comment + '»') : 'Aucun commentaire.'"></p>
                                </div>
                            </template>
                        </div>

                        <div class="flex justify-between pt-4 border-t border-slate-100">
                            <button type="button" @click="step = 1"
                                class="flex items-center gap-2 text-sm font-bold text-slate-500 hover:text-slate-800 px-4 py-2 rounded-xl hover:bg-slate-100 transition-all">
                                <i class="fa-solid fa-arrow-left"></i> Retour
                            </button>
                            <button type="button" @click="step = 3"
                                class="flex items-center gap-2 text-sm font-bold px-6 py-3 rounded-xl bg-amber-600 hover:bg-amber-700 text-white shadow-lg shadow-amber-200 transition-all">
                                Décision Finale <i class="fa-solid fa-arrow-right"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- STEP 3: Décision Finale --}}
            <div x-show="step === 3" x-transition>
                <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100">
                        <h3 class="text-lg font-bold text-slate-800">
                            <i class="fa-solid fa-gavel text-amber-600 mr-2"></i>Décision Finale du Conseil
                        </h3>
                    </div>
                    <div class="p-6 space-y-5">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-3">
                                Décision <span class="text-rose-500">*</span>
                            </label>
                            <div class="grid grid-cols-3 gap-3">
                                <button type="button" @click="decision = 'favorable'"
                                    class="p-4 border-2 rounded-xl text-center transition-all"
                                    :class="decision === 'favorable' ? 'border-emerald-500 bg-emerald-50' : 'border-slate-200 hover:border-emerald-300'">
                                    <i class="fa-solid fa-circle-check text-emerald-500 text-xl mb-2 block"></i>
                                    <p class="text-sm font-bold text-slate-800">Approuver</p>
                                </button>
                                <button type="button" @click="decision = 'reserve'"
                                    class="p-4 border-2 rounded-xl text-center transition-all"
                                    :class="decision === 'reserve' ? 'border-amber-500 bg-amber-50' : 'border-slate-200 hover:border-amber-300'">
                                    <i class="fa-solid fa-pen text-amber-500 text-xl mb-2 block"></i>
                                    <p class="text-sm font-bold text-slate-800">Avec Réserve</p>
                                </button>
                                <button type="button" @click="decision = 'defavorable'"
                                    class="p-4 border-2 rounded-xl text-center transition-all"
                                    :class="decision === 'defavorable' ? 'border-rose-500 bg-rose-50' : 'border-slate-200 hover:border-rose-300'">
                                    <i class="fa-solid fa-circle-xmark text-rose-500 text-xl mb-2 block"></i>
                                    <p class="text-sm font-bold text-slate-800">Rejeter</p>
                                </button>
                            </div>
                            <p x-show="!decision" class="text-xs text-rose-500 mt-2 font-bold">
                                <i class="fa-solid fa-circle-exclamation mr-1"></i> Veuillez sélectionner une décision.
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">
                                Motif / Observations <span class="text-rose-500">*</span>
                            </label>
                            <textarea x-model="comment" rows="4"
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-amber-100 focus:border-amber-500 outline-none resize-none transition-all text-sm"
                                placeholder="Motivez la décision du conseil..."></textarea>
                        </div>

                        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 text-sm text-amber-800 flex gap-3">
                            <i class="fa-solid fa-triangle-exclamation mt-0.5 flex-shrink-0"></i>
                            <p>Cette décision est <strong>officielle et définitive</strong>. Le postulant sera notifié par voie officielle.</p>
                        </div>

                        <div class="flex justify-between pt-4 border-t border-slate-100">
                            <button type="button" @click="step = 2"
                                class="flex items-center gap-2 text-sm font-bold text-slate-500 hover:text-slate-800 px-4 py-2 rounded-xl hover:bg-slate-100 transition-all">
                                <i class="fa-solid fa-arrow-left"></i> Retour
                            </button>
                            <button type="button"
                                @click="(decision && comment.trim()) ? submitDecision() : null"
                                :class="(decision && comment.trim())
                                    ? 'bg-amber-600 hover:bg-amber-700 text-white shadow-lg shadow-amber-200'
                                    : 'bg-slate-100 text-slate-400 cursor-not-allowed'"
                                class="flex items-center gap-2 text-sm font-bold px-6 py-3 rounded-xl transition-all">
                                <i class="fa-solid fa-gavel"></i> Enregistrer la Décision
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </form>
    </template>
</div>
@endsection
