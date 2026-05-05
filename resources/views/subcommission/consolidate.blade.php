@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="{
    recommendation: '',
    decision: '',
    consolidated: false,
    dossier: {
        id: 'REQ-{{ str_pad($demande->id, 4, "0", STR_PAD_LEFT) }}',
        real_id: {{ $demande->id }},
        title: '{!! addslashes($demande->publication->titre ?? "Sans titre") !!}',
        postulant: '{!! addslashes($demande->postulant->prenom . " " . $demande->postulant->nom) !!}',
        grade: '{!! addslashes($demande->postulant->grade ?? "Aucun") !!}',
        dept: '{!! addslashes($demande->postulant->departement ?? "Aucun") !!}',
        type: '{{ $demande->publication->journal ? "Journal" : "Manifestation" }}',
        source: '{!! addslashes($demande->publication->journal->nom_journal ?? "—") !!}',
        issn: '{!! addslashes($demande->publication->journal->issn ?? "—") !!}',
        date: '{{ $demande->created_at->format("Y-m-d") }}',
        pdf_path: '{{ $demande->publication->pdf_path ? asset("storage/" . $demande->publication->pdf_path) : "" }}'
    },
    examiners: [
        @foreach($demande->avis as $avis)
        @if($avis->resultat)
        { 
            name: '{!! addslashes($avis->examinateur->prenom . " " . $avis->examinateur->nom) !!}', 
            specialty: '{!! addslashes($avis->examinateur->specialite ?? "N/A") !!}', 
            avis: '{{ $avis->resultat }}',    
            comment: '{!! addslashes(str_replace(["\n","\r"], [" ",""], $avis->commentaire ?? "")) !!}' 
        },
        @endif
        @endforeach
    ],
    get favorableCount() {
        return this.examiners.filter(e => e.avis === 'favorable').length;
    },
    get defavorableCount() {
        return this.examiners.filter(e => e.avis === 'defavorable').length;
    },
    finalize() {
        this.$refs.consolidateForm.submit();
    }
}">

    {{-- ── Breadcrumb ─────────────────────────────────────── --}}
    <div class="flex items-center gap-2 text-sm text-slate-400">
        <a href="/subcommission/reviews" @click.prevent="navigateTo('subcommission/reviews')" class="hover:text-indigo-600 transition-colors">Suivi des Avis</a>
        <i class="fa-solid fa-chevron-right text-[10px]"></i>
        <span class="text-slate-700 font-bold">Consolider le Dossier</span>
    </div>

    {{-- ── Dossier Summary ────────────────────────────────── --}}
    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
        <div class="flex items-start justify-between">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <span class="text-[10px] font-black text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded tracking-tighter uppercase" x-text="dossier.id"></span>
                    <span class="text-xs font-bold px-2 py-1 rounded-full bg-indigo-100 text-indigo-700" x-text="dossier.type"></span>
                </div>
                <h2 class="text-xl font-extrabold text-slate-900" x-text="dossier.title"></h2>
                <p class="text-sm text-slate-500 mt-1">
                    <span x-text="dossier.postulant"></span> · <span x-text="dossier.grade"></span> · <span x-text="dossier.dept"></span>
                    <template x-if="dossier.pdf_path">
                        <span> · <a :href="dossier.pdf_path" target="_blank" class="text-indigo-600 hover:text-indigo-800 font-bold"><i class="fa-solid fa-file-pdf text-rose-500"></i> Voir la publication</a></span>
                    </template>
                </p>
            </div>
            <div class="text-right">
                <p class="text-xs text-slate-400 font-bold">Source</p>
                <p class="text-sm font-bold text-slate-700" x-text="dossier.source"></p>
                <p class="text-xs text-slate-400 mt-1" x-text="'ISSN: ' + dossier.issn"></p>
            </div>
        </div>
    </div>

    {{-- ── Success State ──────────────────────────────────── --}}
    <template x-if="consolidated">
        <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-8 text-center space-y-4">
            <div class="w-16 h-16 bg-emerald-100 rounded-2xl flex items-center justify-center mx-auto">
                <i class="fa-solid fa-file-circle-check text-emerald-600 text-2xl"></i>
            </div>
            <h3 class="text-xl font-bold text-emerald-800">Dossier Consolidé</h3>
            <p class="text-sm text-emerald-600">Le dossier est prêt à être transmis au Conseil Scientifique.</p>
            <div class="flex gap-3 justify-center pt-2">
                <a href="/subcommission/reviews" @click.prevent="navigateTo('subcommission/reviews')"
                   class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-bold bg-white text-slate-700 border border-slate-200 hover:bg-slate-50 rounded-xl transition-all">
                    <i class="fa-solid fa-arrow-left"></i> Retour aux avis
                </a>
                <a href="/subcommission/transmit" @click.prevent="navigateTo('subcommission/transmit')"
                   class="inline-flex items-center gap-2 px-6 py-2.5 text-sm font-bold bg-indigo-600 text-white hover:bg-indigo-700 rounded-xl transition-all shadow-lg shadow-indigo-200">
                    <i class="fa-solid fa-paper-plane"></i> Aller à la transmission
                </a>
            </div>
        </div>
    </template>

    <template x-if="!consolidated">
        <div class="space-y-6">
            {{-- ── Avis Synthesis ─────────────────────────────── --}}
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100">
                    <h3 class="text-lg font-bold text-slate-800">Synthèse des Avis</h3>
                    <p class="text-xs text-slate-500 mt-0.5">3 examinateurs ont rendu leur avis</p>
                </div>

                {{-- Summary Bar --}}
                <div class="px-6 py-5 bg-slate-50 border-b border-slate-100 flex items-center gap-6">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center">
                            <i class="fa-solid fa-thumbs-up text-emerald-600 text-sm"></i>
                        </div>
                        <div>
                            <p class="text-lg font-black text-emerald-600" x-text="favorableCount"></p>
                            <p class="text-[10px] font-bold text-slate-400 uppercase">Favorable</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-rose-100 rounded-lg flex items-center justify-center">
                            <i class="fa-solid fa-thumbs-down text-rose-600 text-sm"></i>
                        </div>
                        <div>
                            <p class="text-lg font-black text-rose-600" x-text="defavorableCount"></p>
                            <p class="text-[10px] font-bold text-slate-400 uppercase">Défavorable</p>
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="w-full bg-slate-200 rounded-full h-3 flex overflow-hidden">
                            <div class="bg-emerald-500 h-3 transition-all" :style="'width:' + (favorableCount/3*100) + '%'"></div>
                            <div class="bg-rose-500 h-3 transition-all" :style="'width:' + (defavorableCount/3*100) + '%'"></div>
                        </div>
                    </div>
                </div>

                {{-- Individual Reviews --}}
                <div class="p-6 space-y-4">
                    <template x-for="(ex, idx) in examiners" :key="idx">
                        <div class="border rounded-xl p-5 transition-all"
                            :class="ex.avis === 'favorable' ? 'border-emerald-200 bg-emerald-50/30' : 'border-rose-200 bg-rose-50/30'">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex items-center gap-3">
                                    <img :src="'https://ui-avatars.com/api/?name=' + encodeURIComponent(ex.name) + '&background=random&size=40'" class="w-10 h-10 rounded-xl" alt="">
                                    <div>
                                        <p class="font-bold text-sm text-slate-800" x-text="ex.name"></p>
                                        <p class="text-[10px] text-slate-400 font-bold" x-text="ex.specialty"></p>
                                    </div>
                                </div>
                                <span class="text-xs font-bold px-3 py-1 rounded-full border"
                                    :class="ex.avis === 'favorable' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-rose-50 text-rose-700 border-rose-200'"
                                    x-text="ex.avis === 'favorable' ? '✅ Favorable' : '❌ Défavorable'"></span>
                            </div>
                            <div class="bg-white/80 p-3 rounded-lg">
                                <p class="text-sm text-slate-700 italic" x-text="'\"' + ex.comment + '\"'"></p>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- ── Recommendation Form ────────────────────────── --}}
            <form method="POST" :action="'{{ url('/subcommission/consolidate') }}/' + dossier.real_id" x-ref="consolidateForm">
                @csrf
                <input type="hidden" name="decision_finale" :value="decision">
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100">
                    <h3 class="text-lg font-bold text-slate-800">Recommandation de la Sous-Commission</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Rédigez la recommandation finale basée sur les avis des examinateurs</p>
                </div>

                <div class="p-6 space-y-5">
                    {{-- Decision --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-3">Avis de la Sous-Commission <span class="text-rose-500">*</span></label>
                        <div class="grid grid-cols-3 gap-3">
                            <button type="button" @click="decision = 'favorable'"
                                class="p-4 border-2 rounded-xl text-center transition-all"
                                :class="decision === 'favorable' ? 'border-emerald-500 bg-emerald-50' : 'border-slate-200 hover:border-emerald-300'">
                                <i class="fa-solid fa-thumbs-up text-emerald-500 text-xl mb-2"></i>
                                <p class="text-sm font-bold text-slate-800">Favorable</p>
                            </button>
                            <button type="button" @click="decision = 'reserve'"
                                class="p-4 border-2 rounded-xl text-center transition-all"
                                :class="decision === 'reserve' ? 'border-amber-500 bg-amber-50' : 'border-slate-200 hover:border-amber-300'">
                                <i class="fa-solid fa-triangle-exclamation text-amber-500 text-xl mb-2"></i>
                                <p class="text-sm font-bold text-slate-800">Avec Réserve</p>
                            </button>
                            <button type="button" @click="decision = 'defavorable'"
                                class="p-4 border-2 rounded-xl text-center transition-all"
                                :class="decision === 'defavorable' ? 'border-rose-500 bg-rose-50' : 'border-slate-200 hover:border-rose-300'">
                                <i class="fa-solid fa-thumbs-down text-rose-500 text-xl mb-2"></i>
                                <p class="text-sm font-bold text-slate-800">Défavorable</p>
                            </button>
                        </div>
                    </div>

                    {{-- Recommendation Text --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Recommandation détaillée <span class="text-rose-500">*</span></label>
                        <textarea x-model="recommendation" rows="5"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 outline-none resize-none transition-all text-sm"
                            placeholder="Rédigez la recommandation de la sous-commission en vous basant sur les avis des examinateurs..."></textarea>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex justify-between items-center pt-4 border-t border-slate-200">
                        <a href="/subcommission/reviews" @click.prevent="navigateTo('subcommission/reviews')"
                           class="flex items-center gap-2 text-sm font-bold text-slate-500 hover:text-slate-800 px-4 py-2 rounded-xl hover:bg-slate-100 transition-all">
                            <i class="fa-solid fa-arrow-left"></i> Retour
                        </a>
                        <button type="button" @click="decision && recommendation.trim() ? finalize() : null"
                            :class="decision && recommendation.trim() ? 'bg-indigo-600 hover:bg-indigo-700 text-white shadow-lg shadow-indigo-200' : 'bg-slate-100 text-slate-400 cursor-not-allowed'"
                            class="flex items-center gap-2 text-sm font-bold px-6 py-3 rounded-xl transition-all">
                            <i class="fa-solid fa-file-circle-check"></i> Finaliser la Consolidation
                        </button>
                    </div>
                </div>
            </div>
            </form>
        </div>
    </template>

</div>
@endsection
