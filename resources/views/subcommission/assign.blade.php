@extends('layouts.app')

@section('content')

@php
    $dossierData = [
        'id'       => 'REQ-' . str_pad($demande->id, 4, '0', STR_PAD_LEFT),
        'real_id'  => $demande->id,
        'title'    => $demande->publication->titre ?? 'Sans titre',
        'postulant'=> ($demande->postulant->prenom ?? '') . ' ' . ($demande->postulant->nom ?? ''),
        'type'     => $demande->publication->journal ? 'Journal' : 'Manifestation',
        'source'   => $demande->publication->journal->nom_journal ?? '—',
        'auteur'   => $demande->publication->auteur_principal ?? '—',
        'resume'   => $demande->publication->resume ?? '',
        'pdf_path' => $demande->publication->pdf_path ? asset('storage/' . $demande->publication->pdf_path) : '',
    ];

    $examinateursData = $examinateurs->map(function($ex) {
        return [
            'id'         => $ex->id,
            'name'       => ($ex->prenom ?? '') . ' ' . ($ex->nom ?? ''),
            'specialty'  => $ex->specialite ?? 'Spécialité non définie',
            'lab'        => $ex->laboratoire ?? 'Labo non défini',
            'university' => $ex->etablissement ?? 'Établissement non défini',
            'charge'     => \App\Models\Avis::where('id_examinateur', $ex->id)->whereNull('resultat')->count(),
        ];
    })->values()->toArray();
@endphp

<script>
function assignPage() {
    return {
        search:          '',
        specialtyFilter: 'all',
        selected:        [],
        confirmModal:    false,
        dossier:         @json($dossierData),
        examiners:       @json($examinateursData),

        get specialties() {
            return [...new Set(this.examiners.map(e => e.specialty))];
        },

        get filteredExaminers() {
            return this.examiners.filter(e => {
                const matchSearch = this.search === '' ||
                    e.name.toLowerCase().includes(this.search.toLowerCase()) ||
                    e.lab.toLowerCase().includes(this.search.toLowerCase()) ||
                    e.specialty.toLowerCase().includes(this.search.toLowerCase());
                const matchSpec = this.specialtyFilter === 'all' || e.specialty === this.specialtyFilter;
                return matchSearch && matchSpec;
            });
        },

        toggleExaminer(id) {
            const idx = this.selected.indexOf(id);
            if (idx > -1) { this.selected.splice(idx, 1); }
            else { this.selected.push(id); }
        },

        isSelected(id) { return this.selected.includes(id); },

        getSelectedExaminers() {
            return this.examiners.filter(e => this.selected.includes(e.id));
        },

        sendProposal() {
            this.$refs.assignForm.submit();
        }
    };
}
</script>

<div class="max-w-5xl mx-auto space-y-6" x-data="assignPage()">

    <form method="POST" :action="'{{ url('/subcommission/assign') }}/' + dossier.real_id" x-ref="assignForm">
        @csrf
        <template x-for="id in selected" :key="id">
            <input type="hidden" name="examinateurs[]" :value="id">
        </template>
    </form>

    {{-- ── Breadcrumb ─────────────────────────────────────── --}}
    <div class="flex items-center gap-2 text-sm text-slate-400">
        <a href="/subcommission/requests"
           @click.prevent="navigateTo('subcommission/requests')"
           class="hover:text-indigo-600 transition-colors">Demandes Reçues</a>
        <i class="fa-solid fa-chevron-right text-[10px]"></i>
        <span class="text-slate-700 font-bold">Proposer des Examinateurs</span>
    </div>

    {{-- ── Dossier Info Banner (PDF + Résumé) ─────────────── --}}
    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4">

        {{-- Titre & méta --}}
        <div class="flex items-start justify-between">
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-3 mb-2">
                    <span class="text-[10px] font-black text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded tracking-tighter uppercase"
                          x-text="dossier.id"></span>
                    <span class="text-xs font-bold px-2 py-1 rounded-full bg-indigo-100 text-indigo-700"
                          x-text="dossier.type"></span>
                </div>
                <h2 class="text-xl font-extrabold text-slate-900" x-text="dossier.title"></h2>
                <p class="text-sm text-slate-500 mt-1">
                    <i class="fa-solid fa-user text-slate-400 mr-1"></i>
                    <span x-text="dossier.postulant"></span>
                    <span class="mx-2 text-slate-300">·</span>
                    <span x-text="dossier.source"></span>
                    <span class="mx-2 text-slate-300">·</span>
                    <span class="italic text-slate-400">Auteur :</span>
                    <span x-text="dossier.auteur"></span>
                </p>
            </div>
            <div class="flex items-center gap-2 shrink-0 ml-4">
                <span class="text-sm font-bold px-3 py-1.5 rounded-xl"
                    :class="selected.length >= 3 ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700'">
                    <i class="fa-solid fa-user-check mr-1"></i>
                    <span x-text="selected.length"></span>/3 sélectionnés
                </span>
            </div>
        </div>

        {{-- Bouton PDF --}}
        <div>
            <template x-if="dossier.pdf_path">
                <a :href="dossier.pdf_path" target="_blank"
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-rose-50 text-rose-600 border border-rose-200 hover:bg-rose-100 rounded-xl text-sm font-bold transition-all">
                    <i class="fa-solid fa-file-pdf text-rose-500 text-base"></i>
                    Ouvrir le PDF de la publication
                </a>
            </template>
            <template x-if="!dossier.pdf_path">
                <span class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 text-slate-400 rounded-xl text-sm italic">
                    <i class="fa-solid fa-file-slash"></i> Aucun PDF joint
                </span>
            </template>
        </div>

        {{-- Résumé --}}
        <template x-if="dossier.resume">
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">
                    <i class="fa-solid fa-align-left text-indigo-400 mr-1"></i> Résumé / Abstract
                </p>
                <div class="bg-slate-50 border border-slate-200 rounded-xl px-5 py-4 max-h-36 overflow-y-auto">
                    <p class="text-sm text-slate-600 leading-relaxed" x-text="dossier.resume"></p>
                </div>
            </div>
        </template>
        <template x-if="!dossier.resume">
            <p class="text-xs text-slate-400 italic">
                <i class="fa-solid fa-circle-exclamation text-amber-400 mr-1"></i> Aucun résumé fourni par le postulant.
            </p>
        </template>
    </div>

    {{-- ── Filtres Examinateurs ────────────────────────────── --}}
    <div class="flex flex-col md:flex-row gap-3">
        <div class="relative flex-1">
            <span class="absolute inset-y-0 left-4 flex items-center text-slate-400">
                <i class="fa-solid fa-magnifying-glass"></i>
            </span>
            <input type="text" x-model="search"
                placeholder="Rechercher par nom, laboratoire ou spécialité..."
                class="w-full pl-12 pr-4 py-3 bg-white border border-slate-200 rounded-xl outline-none focus:ring-4 focus:ring-indigo-100 focus:border-indigo-400 transition-all text-sm">
        </div>
        <select x-model="specialtyFilter"
            class="px-4 py-3 bg-white border border-slate-200 rounded-xl outline-none focus:ring-4 focus:ring-indigo-100 text-sm font-bold text-slate-600">
            <option value="all">Toutes spécialités</option>
            <template x-for="spec in specialties" :key="spec">
                <option :value="spec" x-text="spec"></option>
            </template>
        </select>
    </div>

    {{-- Info --}}
    <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-4 flex items-center gap-3">
        <i class="fa-solid fa-circle-info text-indigo-500"></i>
        <p class="text-xs text-indigo-700 font-bold">
            Proposez au minimum <strong>3 examinateurs</strong>. Chaque examinateur recevra une invitation.
        </p>
    </div>

    {{-- ── Liste des examinateurs ──────────────────────────── --}}
    <div class="space-y-3 max-h-[500px] overflow-y-auto px-1">
        <template x-for="ex in filteredExaminers" :key="ex.id">
            <div @click="toggleExaminer(ex.id)"
                class="flex items-center justify-between p-5 bg-white border rounded-2xl hover:bg-slate-50 transition-all cursor-pointer"
                :class="isSelected(ex.id)
                    ? 'border-indigo-400 bg-indigo-50/30 shadow-sm shadow-indigo-100'
                    : 'border-slate-200'">
                <div class="flex items-center gap-4">
                    <img :src="'https://ui-avatars.com/api/?name=' + encodeURIComponent(ex.name) + '&background=random&size=48'"
                         class="w-12 h-12 rounded-xl" alt="">
                    <div>
                        <p class="font-bold text-slate-800" x-text="ex.name"></p>
                        <p class="text-xs text-slate-500 mt-0.5">
                            <span x-text="ex.specialty"></span>
                            <span class="mx-1">·</span>
                            <span x-text="ex.lab"></span>
                            <span class="mx-1">·</span>
                            <span x-text="ex.university"></span>
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="text-right hidden sm:block">
                        <p class="text-[10px] text-slate-400 font-bold uppercase">Charge actuelle</p>
                        <p class="text-sm font-bold"
                           :class="ex.charge >= 3 ? 'text-rose-500' : ex.charge >= 2 ? 'text-amber-500' : 'text-emerald-500'"
                           x-text="ex.charge + ' dossier(s)'"></p>
                    </div>
                    <div class="w-6 h-6 rounded-lg flex items-center justify-center transition-all"
                         :class="isSelected(ex.id) ? 'bg-indigo-600 text-white' : 'border-2 border-slate-300'">
                        <i class="fa-solid fa-check text-xs" x-show="isSelected(ex.id)"></i>
                    </div>
                </div>
            </div>
        </template>

        <template x-if="filteredExaminers.length === 0">
            <div class="text-center py-8 text-slate-400 text-sm italic">
                <i class="fa-solid fa-user-slash text-2xl mb-2 block"></i>
                Aucun examinateur correspondant.
            </div>
        </template>
    </div>

    {{-- ── Action Bar ──────────────────────────────────────── --}}
    <div class="flex justify-between items-center pt-4 border-t border-slate-200">
        <a href="/subcommission/requests"
           @click.prevent="navigateTo('subcommission/requests')"
           class="flex items-center gap-2 text-sm font-bold text-slate-500 hover:text-slate-800 px-4 py-2 rounded-xl hover:bg-slate-100 transition-all">
            <i class="fa-solid fa-arrow-left"></i> Retour
        </a>
        <button @click="selected.length >= 3 ? confirmModal = true : null"
            :class="selected.length >= 3
                ? 'bg-indigo-600 hover:bg-indigo-700 text-white shadow-lg shadow-indigo-200'
                : 'bg-slate-100 text-slate-400 cursor-not-allowed'"
            class="flex items-center gap-2 text-sm font-bold px-6 py-3 rounded-xl transition-all">
            <i class="fa-solid fa-paper-plane"></i> Envoyer la Proposition
        </button>
    </div>

    {{-- ══════════════════════════════════════════════════════
         MODAL — Confirmation d'envoi
    ══════════════════════════════════════════════════════ --}}
    <div x-show="confirmModal" x-cloak
         class="fixed inset-0 z-[60] flex items-center justify-center p-4"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95">

        <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" @click="confirmModal = false"></div>

        <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-lg">
            <div class="p-8 text-center">
                <div class="w-16 h-16 bg-indigo-100 rounded-2xl flex items-center justify-center mx-auto mb-5">
                    <i class="fa-solid fa-paper-plane text-indigo-600 text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-2">Confirmer l'envoi</h3>
                <p class="text-sm text-slate-500 mb-6">
                    Vous êtes sur le point de proposer
                    <strong x-text="selected.length"></strong> examinateur(s) pour le dossier
                    <strong x-text="dossier.id"></strong>.
                </p>

                <div class="space-y-2 mb-6">
                    <template x-for="ex in getSelectedExaminers()" :key="ex.id">
                        <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl text-left">
                            <img :src="'https://ui-avatars.com/api/?name=' + encodeURIComponent(ex.name) + '&background=random&size=32'"
                                 class="w-8 h-8 rounded-lg" alt="">
                            <div>
                                <p class="text-sm font-bold text-slate-800" x-text="ex.name"></p>
                                <p class="text-[10px] text-slate-400" x-text="ex.specialty + ' · ' + ex.lab"></p>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="bg-amber-50 border border-amber-200 rounded-xl p-3 text-sm text-amber-800 flex gap-2 mb-6 text-left">
                    <i class="fa-solid fa-triangle-exclamation mt-0.5 flex-shrink-0"></i>
                    <p>Chaque examinateur recevra une notification.</p>
                </div>

                <div class="flex gap-3 justify-center">
                    <button @click="confirmModal = false"
                        class="px-5 py-2.5 text-sm font-bold bg-white text-slate-700 border border-slate-200 hover:bg-slate-50 rounded-xl transition-all">
                        Annuler
                    </button>
                    <button @click="sendProposal()"
                        class="px-6 py-2.5 text-sm font-bold bg-indigo-600 text-white hover:bg-indigo-700 rounded-xl transition-all shadow-lg shadow-indigo-200 flex items-center gap-2">
                        <i class="fa-solid fa-check"></i> Confirmer
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
