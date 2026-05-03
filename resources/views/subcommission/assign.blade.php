@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto space-y-6" x-data="{
    search: '',
    specialtyFilter: 'all',
    selected: [],
    confirmModal: false,
    proposalSent: false,
    dossier: {
        id: 'SC-2491',
        title: 'Deep Learning in Medical Imaging',
        postulant: 'Dr. Ahmed Benali',
        type: 'Journal',
        source: 'IEEE Trans. Medical Imaging',
    },
    examiners: [
        { id: 1, name: 'Pr. Hamid El Moussaoui',  specialty: 'Intelligence Artificielle',   lab: 'LRIA',  university: 'USTHB',               charge: 2 },
        { id: 2, name: 'Pr. Nadia Bensalem',       specialty: 'Traitement d\'Images',        lab: 'LTIR',  university: 'Univ. Blida',          charge: 1 },
        { id: 3, name: 'Dr. Kamel Ait Ouali',      specialty: 'Machine Learning',            lab: 'LMCS',  university: 'ESI Alger',            charge: 3 },
        { id: 4, name: 'Pr. Fatima Benbouzid',     specialty: 'Intelligence Artificielle',   lab: 'LRIA',  university: 'USTHB',               charge: 0 },
        { id: 5, name: 'Dr. Rachid Boudour',       specialty: 'Deep Learning',               lab: 'LABGED',university: 'Univ. Annaba',         charge: 1 },
        { id: 6, name: 'Pr. Amel Zenati',          specialty: 'Traitement d\'Images',        lab: 'LMCS',  university: 'ESI Alger',            charge: 2 },
        { id: 7, name: 'Dr. Mohamed Cheriet',       specialty: 'Vision par Ordinateur',       lab: 'LIVIA', university: 'ÉTS Montréal',        charge: 0 },
        { id: 8, name: 'Pr. Leila Hamdad',         specialty: 'Data Science',                lab: 'LCSI',  university: 'ESI Alger',            charge: 1 },
    ],
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
    getSelectedExaminers() { return this.examiners.filter(e => this.selected.includes(e.id)); },
    sendProposal() {
        this.proposalSent = true;
        this.confirmModal = false;
        showToast('Propositions envoyées aux ' + this.selected.length + ' examinateurs !');
    }
}">

    {{-- ── Breadcrumb ─────────────────────────────────────── --}}
    <div class="flex items-center gap-2 text-sm text-slate-400">
        <a href="/subcommission/requests" @click.prevent="navigateTo('subcommission/requests')" class="hover:text-indigo-600 transition-colors">Demandes Reçues</a>
        <i class="fa-solid fa-chevron-right text-[10px]"></i>
        <span class="text-slate-700 font-bold">Proposer des Examinateurs</span>
    </div>

    {{-- ── Dossier Info Banner ─────────────────────────────── --}}
    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
        <div class="flex items-start justify-between">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <span class="text-[10px] font-black text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded tracking-tighter uppercase" x-text="dossier.id"></span>
                    <span class="text-xs font-bold px-2 py-1 rounded-full bg-indigo-100 text-indigo-700" x-text="dossier.type"></span>
                </div>
                <h2 class="text-xl font-extrabold text-slate-900" x-text="dossier.title"></h2>
                <p class="text-sm text-slate-500 mt-1">
                    <span x-text="dossier.postulant"></span> · <span x-text="dossier.source"></span>
                </p>
            </div>
            <div class="flex items-center gap-2" x-show="!proposalSent">
                <span class="text-sm font-bold px-3 py-1.5 rounded-xl"
                    :class="selected.length >= 3 ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700'">
                    <i class="fa-solid fa-user-check mr-1"></i>
                    <span x-text="selected.length"></span>/3 sélectionnés
                </span>
            </div>
        </div>
    </div>

    {{-- ── Success State ──────────────────────────────────── --}}
    <template x-if="proposalSent">
        <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-8 text-center">
            <div class="w-16 h-16 bg-emerald-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <i class="fa-solid fa-paper-plane text-emerald-600 text-2xl"></i>
            </div>
            <h3 class="text-xl font-bold text-emerald-800 mb-2">Propositions Envoyées !</h3>
            <p class="text-sm text-emerald-600 mb-6">Les examinateurs sélectionnés ont été notifiés. Ils peuvent accepter ou décliner la proposition.</p>
            <div class="flex flex-wrap gap-3 justify-center mb-6">
                <template x-for="ex in getSelectedExaminers()" :key="ex.id">
                    <div class="flex items-center gap-2 bg-white border border-emerald-200 rounded-xl px-4 py-2">
                        <img :src="'https://ui-avatars.com/api/?name=' + encodeURIComponent(ex.name) + '&bg=10b981&color=fff&size=28'" class="w-7 h-7 rounded-lg" alt="">
                        <div class="text-left">
                            <p class="text-sm font-bold text-slate-800" x-text="ex.name"></p>
                            <p class="text-[10px] text-emerald-600 font-bold">📩 Proposition envoyée</p>
                        </div>
                    </div>
                </template>
            </div>
            <a href="/subcommission/reviews" @click.prevent="navigateTo('subcommission/reviews')"
               class="inline-flex items-center gap-2 bg-indigo-600 text-white font-bold px-6 py-3 rounded-xl hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-200 text-sm">
                <i class="fa-solid fa-comments"></i> Suivre les Avis
            </a>
        </div>
    </template>

    {{-- ── Selection Panel ────────────────────────────────── --}}
    <template x-if="!proposalSent">
        <div class="space-y-4">
            {{-- Search & Filters --}}
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

            {{-- Info Box --}}
            <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-4 flex items-center gap-3">
                <i class="fa-solid fa-circle-info text-indigo-500"></i>
                <p class="text-xs text-indigo-700 font-bold">Vous devez proposer au minimum <strong>3 examinateurs</strong>. Chaque examinateur recevra une invitation qu'il peut accepter ou décliner.</p>
            </div>

            {{-- Examiner List --}}
            <div class="space-y-3 max-h-[500px] overflow-y-auto px-1">
                <template x-for="ex in filteredExaminers" :key="ex.id">
                    <div @click="toggleExaminer(ex.id)"
                        class="flex items-center justify-between p-5 bg-white border rounded-2xl hover:bg-slate-50 transition-all cursor-pointer group"
                        :class="isSelected(ex.id) ? 'border-indigo-400 bg-indigo-50/30 shadow-sm shadow-indigo-100' : 'border-slate-200'">
                        <div class="flex items-center gap-4">
                            <img :src="'https://ui-avatars.com/api/?name=' + encodeURIComponent(ex.name) + '&background=random&size=48'" class="w-12 h-12 rounded-xl" alt="">
                            <div>
                                <p class="font-bold text-slate-800" x-text="ex.name"></p>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    <span x-text="ex.specialty"></span> · <span x-text="ex.lab"></span> · <span x-text="ex.university"></span>
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="text-right hidden sm:block">
                                <p class="text-[10px] text-slate-400 font-bold uppercase">Charge actuelle</p>
                                <p class="text-sm font-bold" :class="ex.charge >= 3 ? 'text-rose-500' : ex.charge >= 2 ? 'text-amber-500' : 'text-emerald-500'" x-text="ex.charge + ' dossier(s)'"></p>
                            </div>
                            <div class="w-6 h-6 rounded-lg flex items-center justify-center transition-all"
                                :class="isSelected(ex.id) ? 'bg-indigo-600 text-white' : 'border-2 border-slate-300'">
                                <i class="fa-solid fa-check text-xs" x-show="isSelected(ex.id)"></i>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            {{-- Action Bar --}}
            <div class="flex justify-between items-center pt-4 border-t border-slate-200">
                <a href="/subcommission/requests" @click.prevent="navigateTo('subcommission/requests')"
                   class="flex items-center gap-2 text-sm font-bold text-slate-500 hover:text-slate-800 px-4 py-2 rounded-xl hover:bg-slate-100 transition-all">
                    <i class="fa-solid fa-arrow-left"></i> Retour
                </a>
                <button @click="selected.length >= 3 ? confirmModal = true : null"
                    :class="selected.length >= 3 ? 'bg-indigo-600 hover:bg-indigo-700 text-white shadow-lg shadow-indigo-200' : 'bg-slate-100 text-slate-400 cursor-not-allowed'"
                    class="flex items-center gap-2 text-sm font-bold px-6 py-3 rounded-xl transition-all">
                    <i class="fa-solid fa-paper-plane"></i> Envoyer la Proposition
                </button>
            </div>
        </div>
    </template>

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
                <h3 class="text-xl font-bold text-slate-900 mb-2">Confirmer l'envoi des propositions</h3>
                <p class="text-sm text-slate-500 mb-6">
                    Vous êtes sur le point de proposer <strong x-text="selected.length"></strong> examinateur(s) pour le dossier <strong x-text="dossier.id"></strong>.
                </p>

                <div class="space-y-2 mb-6">
                    <template x-for="ex in getSelectedExaminers()" :key="ex.id">
                        <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl text-left">
                            <img :src="'https://ui-avatars.com/api/?name=' + encodeURIComponent(ex.name) + '&background=random&size=32'" class="w-8 h-8 rounded-lg" alt="">
                            <div>
                                <p class="text-sm font-bold text-slate-800" x-text="ex.name"></p>
                                <p class="text-[10px] text-slate-400" x-text="ex.specialty + ' · ' + ex.lab"></p>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="bg-amber-50 border border-amber-200 rounded-xl p-3 text-sm text-amber-800 flex gap-2 mb-6 text-left">
                    <i class="fa-solid fa-triangle-exclamation mt-0.5 flex-shrink-0"></i>
                    <p>Chaque examinateur recevra une notification et pourra <strong>accepter</strong> ou <strong>décliner</strong> cette proposition.</p>
                </div>

                <div class="flex gap-3 justify-center">
                    <button @click="confirmModal = false"
                        class="px-5 py-2.5 text-sm font-bold bg-white text-slate-700 border border-slate-200 hover:bg-slate-50 rounded-xl transition-all">
                        Annuler
                    </button>
                    <button @click="sendProposal()"
                        class="px-6 py-2.5 text-sm font-bold bg-indigo-600 text-white hover:bg-indigo-700 rounded-xl transition-all shadow-lg shadow-indigo-200 flex items-center gap-2">
                        <i class="fa-solid fa-check"></i> Confirmer l'envoi
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
