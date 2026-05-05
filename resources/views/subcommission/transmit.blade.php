@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto space-y-6" x-data="{
    tab: 'ready',
    confirmModal: false,
    selectedDossier: null,
    readyDossiers: [
        @foreach($dossiers as $d)
        { 
            id: 'REQ-{{ str_pad($d->id, 4, "0", STR_PAD_LEFT) }}', 
            real_id: {{ $d->id }},
            title: '{!! addslashes($d->publication->titre ?? "Sans titre") !!}',
            postulant: '{!! addslashes($d->postulant->prenom . " " . $d->postulant->nom) !!}',
            type: '{{ $d->publication->journal ? "Journal" : "Manifestation" }}',
            decision: '{{ $d->decision_finale }}',    
            recommendation: '—', 
            date: '{{ $d->created_at->format("Y-m-d") }}', 
            consolidatedDate: '{{ $d->date_decision ? \Carbon\Carbon::parse($d->date_decision)->format("Y-m-d") : "N/A" }}' 
        },
        @endforeach
    ],
    transmittedDossiers: [
        @foreach($transmis as $d)
        { 
            id: 'REQ-{{ str_pad($d->id, 4, "0", STR_PAD_LEFT) }}', 
            real_id: {{ $d->id }},
            title: '{!! addslashes($d->publication->titre ?? "Sans titre") !!}',
            postulant: '{!! addslashes($d->postulant->prenom . " " . $d->postulant->nom) !!}',
            type: '{{ $d->publication->journal ? "Journal" : "Manifestation" }}',
            decision: '{{ $d->decision_finale }}',    
            recommendation: '—', 
            date: '{{ $d->created_at->format("Y-m-d") }}', 
            consolidatedDate: '{{ $d->date_decision ? \Carbon\Carbon::parse($d->date_decision)->format("Y-m-d") : "N/A" }}',
            transmittedDate: '{{ $d->updated_at->format("Y-m-d") }}' 
        },
        @endforeach
    ],
    decisionLabel(d) {
        return { favorable: 'Favorable', reserve: 'Avec Réserve', defavorable: 'Défavorable' }[d] || d;
    },
    decisionClass(d) {
        return {
            favorable: 'bg-emerald-50 text-emerald-700 border-emerald-200',
            reserve: 'bg-amber-50 text-amber-700 border-amber-200',
            defavorable: 'bg-rose-50 text-rose-700 border-rose-200',
        }[d] || '';
    },
    decisionIcon(d) {
        return { favorable: 'fa-thumbs-up', reserve: 'fa-triangle-exclamation', defavorable: 'fa-thumbs-down' }[d] || '';
    },
    openConfirm(dossier) {
        this.selectedDossier = dossier;
        this.confirmModal = true;
    },
    transmit() {
        if (this.selectedDossier) {
            this.$refs.transmitForm.action = '{{ url("/subcommission/transmit") }}/' + this.selectedDossier.real_id;
            this.$refs.transmitForm.submit();
        }
    }
}">

    <form method="POST" x-ref="transmitForm">
        @csrf
    </form>

    {{-- ── Header ─────────────────────────────────────────── --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Transmission au Conseil</h1>
            <p class="text-slate-500 text-sm mt-1">Transmettre les dossiers consolidés au président du Conseil Scientifique</p>
        </div>
    </div>

    {{-- ── Tab Switcher ───────────────────────────────────── --}}
    <div class="flex gap-2">
        <button @click="tab = 'ready'"
            class="px-5 py-2.5 rounded-xl text-sm font-bold transition-all"
            :class="tab === 'ready' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-200' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50'">
            <i class="fa-solid fa-clock mr-1"></i> Prêts à transmettre
            <span class="ml-1 text-xs opacity-70" x-text="'(' + readyDossiers.length + ')'"></span>
        </button>
        <button @click="tab = 'transmitted'"
            class="px-5 py-2.5 rounded-xl text-sm font-bold transition-all"
            :class="tab === 'transmitted' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-200' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50'">
            <i class="fa-solid fa-check-double mr-1"></i> Déjà transmis
            <span class="ml-1 text-xs opacity-70" x-text="'(' + transmittedDossiers.length + ')'"></span>
        </button>
    </div>

    {{-- ── Ready to Transmit ──────────────────────────────── --}}
    <div x-show="tab === 'ready'" x-transition class="space-y-4">
        <template x-if="readyDossiers.length === 0">
            <div class="bg-white border border-slate-200 rounded-2xl p-12 text-center">
                <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-inbox text-slate-300 text-2xl"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-600 mb-1">Aucun dossier en attente</h3>
                <p class="text-sm text-slate-400">Tous les dossiers consolidés ont été transmis.</p>
            </div>
        </template>

        <template x-for="dossier in readyDossiers" :key="dossier.id">
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm hover:shadow-md transition-all overflow-hidden">
                <div class="p-6">
                    <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-4">
                        {{-- Left: Info --}}
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <span class="text-[10px] font-black text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded tracking-tighter uppercase" x-text="dossier.id"></span>
                                <span class="text-xs font-bold px-2 py-1 rounded-full"
                                    :class="dossier.type === 'Journal' ? 'bg-indigo-100 text-indigo-700' : 'bg-violet-100 text-violet-700'"
                                    x-text="dossier.type"></span>
                                <span class="text-xs font-bold px-2 py-1 rounded-full border"
                                    :class="decisionClass(dossier.decision)">
                                    <i class="fa-solid" :class="decisionIcon(dossier.decision)"></i>
                                    <span x-text="decisionLabel(dossier.decision)"></span>
                                </span>
                            </div>
                            <h3 class="text-lg font-extrabold text-slate-900 mb-1" x-text="dossier.title"></h3>
                            <p class="text-sm text-slate-500 mb-3" x-text="dossier.postulant"></p>

                            <div class="bg-slate-50 rounded-xl p-4 mb-3">
                                <p class="text-xs font-bold text-slate-400 uppercase mb-1">Recommandation de la Sous-Commission</p>
                                <p class="text-sm text-slate-700 italic" x-text="dossier.recommendation"></p>
                            </div>

                            <div class="flex gap-4 text-xs text-slate-400">
                                <span><i class="fa-solid fa-calendar mr-1"></i> Soumis: <span x-text="dossier.date"></span></span>
                                <span><i class="fa-solid fa-file-circle-check mr-1"></i> Consolidé: <span x-text="dossier.consolidatedDate"></span></span>
                            </div>
                        </div>

                        {{-- Right: Action --}}
                        <div class="flex-shrink-0">
                            <button @click="openConfirm(dossier)"
                                class="inline-flex items-center gap-2 px-6 py-3 text-sm font-bold bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-200">
                                <i class="fa-solid fa-paper-plane"></i> Transmettre au Conseil
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>

    {{-- ── Already Transmitted ─────────────────────────────── --}}
    <div x-show="tab === 'transmitted'" x-transition>
        <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50/50 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest">Réf.</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest">Titre</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest">Postulant</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest">Avis</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest">Transmis le</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <template x-for="d in transmittedDossiers" :key="d.id">
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4">
                                <span class="text-[10px] font-black text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded tracking-tighter uppercase" x-text="d.id"></span>
                            </td>
                            <td class="px-6 py-4 font-bold text-slate-800 max-w-[260px] truncate" x-text="d.title"></td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <img :src="'https://ui-avatars.com/api/?name=' + encodeURIComponent(d.postulant) + '&size=24&bg=6366f1&color=fff'" class="w-6 h-6 rounded-full" alt="">
                                    <span class="text-sm text-slate-600" x-text="d.postulant"></span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-xs font-bold px-2.5 py-0.5 rounded-full border"
                                    :class="decisionClass(d.decision)"
                                    x-text="decisionLabel(d.decision)"></span>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-500 italic" x-text="d.transmittedDate"></td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════
         MODAL — Confirmation de Transmission
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
                <h3 class="text-xl font-bold text-slate-900 mb-2">Confirmer la Transmission</h3>
                <p class="text-sm text-slate-500 mb-4">
                    Vous êtes sur le point de transmettre ce dossier au <strong>Président du Conseil Scientifique</strong>.
                </p>

                <template x-if="selectedDossier">
                    <div class="bg-slate-50 rounded-xl p-4 mb-4 text-left">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-[10px] font-black text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded tracking-tighter uppercase" x-text="selectedDossier.id"></span>
                            <span class="text-xs font-bold px-2 py-0.5 rounded-full border"
                                :class="decisionClass(selectedDossier.decision)"
                                x-text="decisionLabel(selectedDossier.decision)"></span>
                        </div>
                        <p class="font-bold text-slate-800 text-sm" x-text="selectedDossier.title"></p>
                        <p class="text-xs text-slate-500 mt-1" x-text="selectedDossier.postulant"></p>
                    </div>
                </template>

                <div class="bg-amber-50 border border-amber-200 rounded-xl p-3 text-sm text-amber-800 flex gap-2 mb-6 text-left">
                    <i class="fa-solid fa-triangle-exclamation mt-0.5 flex-shrink-0"></i>
                    <p>Cette action est <strong>irréversible</strong>. Le dossier sera visible par le président du Conseil Scientifique pour délibération.</p>
                </div>

                <div class="flex gap-3 justify-center">
                    <button @click="confirmModal = false"
                        class="px-5 py-2.5 text-sm font-bold bg-white text-slate-700 border border-slate-200 hover:bg-slate-50 rounded-xl transition-all">
                        Annuler
                    </button>
                    <button @click="transmit()"
                        class="px-6 py-2.5 text-sm font-bold bg-indigo-600 text-white hover:bg-indigo-700 rounded-xl transition-all shadow-lg shadow-indigo-200 flex items-center gap-2">
                        <i class="fa-solid fa-paper-plane"></i> Transmettre
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
