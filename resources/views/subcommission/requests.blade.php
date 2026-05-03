@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto space-y-6" x-data="{
    filter: 'all',
    controlModal: false,
    selectedRequest: null,
    checklist: { form: false, resume: false, pdf: false, deadline: false },
    rejectReason: '',
    requests: [
        { id: 'SC-2491', title: 'Deep Learning in Medical Imaging',          postulant: 'Dr. Ahmed Benali',      dept: 'Informatique',        grade: 'Maître de Conférences A', type: 'Journal',       source: 'IEEE Trans. Medical Imaging', issn: '0278-0062', date: '2026-04-28', status: 'pending' },
        { id: 'SC-2492', title: 'Blockchain for Decentralized Identity',     postulant: 'Dr. Samira Kaci',        dept: 'Informatique',        grade: 'Maître de Conférences B', type: 'Manifestation', source: 'ICSE 2026',                   issn: '',          date: '2026-04-27', status: 'pending' },
        { id: 'SC-2493', title: 'Quantum Computing Optimization',            postulant: 'Dr. Hamid Moussa',       dept: 'Mathématiques',       grade: 'Professeur',              type: 'Journal',       source: 'Nature Physics',              issn: '1745-2473', date: '2026-04-25', status: 'pending' },
        { id: 'SC-2494', title: 'Ethical Challenges in Modern AI',           postulant: 'Pr. Mehdi Boudiaf',      dept: 'Informatique',        grade: 'Professeur',              type: 'Journal',       source: 'AI & Society',                issn: '0951-5666', date: '2026-04-22', status: 'recevable' },
        { id: 'SC-2495', title: 'Edge Computing IoT Optimization',           postulant: 'Dr. Fatima Zerhouni',    dept: 'Télécommunications',  grade: 'Maître de Conférences A', type: 'Manifestation', source: 'MobiCom 2026',                issn: '',          date: '2026-04-20', status: 'recevable' },
        { id: 'SC-2496', title: 'Neural Architecture Search Survey',         postulant: 'Dr. Youssef Amrani',     dept: 'Informatique',        grade: 'Maître de Conférences B', type: 'Journal',       source: 'JMLR',                        issn: '1533-7928', date: '2026-04-18', status: 'rejected' },
    ],
    get filtered() {
        if (this.filter === 'all') return this.requests;
        return this.requests.filter(r => r.status === this.filter);
    },
    get counts() {
        return {
            all: this.requests.length,
            pending: this.requests.filter(r => r.status === 'pending').length,
            recevable: this.requests.filter(r => r.status === 'recevable').length,
            rejected: this.requests.filter(r => r.status === 'rejected').length,
        };
    },
    openControl(req) {
        this.selectedRequest = req;
        this.checklist = { form: false, resume: false, pdf: false, deadline: false };
        this.rejectReason = '';
        this.controlModal = true;
    },
    get allChecked() {
        return this.checklist.form && this.checklist.resume && this.checklist.pdf && this.checklist.deadline;
    },
    markRecevable() {
        if (this.selectedRequest) {
            this.selectedRequest.status = 'recevable';
            this.controlModal = false;
            showToast('Dossier marqué comme recevable !');
        }
    },
    markRejected() {
        if (this.selectedRequest && this.rejectReason.trim()) {
            this.selectedRequest.status = 'rejected';
            this.controlModal = false;
            showToast('Dossier rejeté — le postulant sera notifié.', 'error');
        }
    }
}">

    {{-- ── Header ─────────────────────────────────────────── --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Demandes Reçues</h1>
            <p class="text-slate-500 text-sm mt-1">Réception et contrôle de recevabilité des dossiers</p>
        </div>
    </div>

    {{-- ── Filter Tabs ────────────────────────────────────── --}}
    <div class="flex gap-2 flex-wrap">
        <template x-for="[key, label] in [['all','Toutes'],['pending','En attente'],['recevable','Recevable'],['rejected','Non recevable']]" :key="key">
            <button @click="filter = key"
                class="px-4 py-2 rounded-xl text-sm font-bold transition-all"
                :class="filter === key ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-200' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50'">
                <span x-text="label"></span>
                <span class="ml-1 text-xs opacity-70" x-text="'(' + counts[key] + ')'"></span>
            </button>
        </template>
    </div>

    {{-- ── Requests Table ─────────────────────────────────── --}}
    <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white">
        <table class="w-full text-left border-collapse">
            <thead class="bg-slate-50/50 border-b border-slate-200">
                <tr>
                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest">Réf.</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest">Titre</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest">Postulant</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest">Type</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest">Date</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest">Statut</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <template x-for="req in filtered" :key="req.id">
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4">
                            <span class="text-[10px] font-black text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded tracking-tighter uppercase" x-text="req.id"></span>
                        </td>
                        <td class="px-6 py-4 font-bold text-slate-800 max-w-[260px] truncate" x-text="req.title"></td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <img :src="'https://ui-avatars.com/api/?name=' + encodeURIComponent(req.postulant) + '&size=24&bg=6366f1&color=fff'" class="w-6 h-6 rounded-full" alt="">
                                <span class="text-sm text-slate-600" x-text="req.postulant"></span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-xs font-bold px-2 py-1 rounded-full"
                                :class="req.type === 'Journal' ? 'bg-indigo-100 text-indigo-700' : 'bg-violet-100 text-violet-700'"
                                x-text="req.type"></span>
                        </td>
                        <td class="px-6 py-4 text-slate-500 text-sm italic" x-text="req.date"></td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border uppercase tracking-wider"
                                :class="{
                                    'bg-slate-100 text-slate-600 border-slate-200': req.status === 'pending',
                                    'bg-emerald-50 text-emerald-600 border-emerald-200': req.status === 'recevable',
                                    'bg-rose-50 text-rose-600 border-rose-200': req.status === 'rejected',
                                }">
                                <span class="w-1.5 h-1.5 rounded-full mr-1.5"
                                    :class="{
                                        'bg-slate-400': req.status === 'pending',
                                        'bg-emerald-500': req.status === 'recevable',
                                        'bg-rose-500': req.status === 'rejected',
                                    }"></span>
                                <span x-text="req.status === 'pending' ? 'En attente' : req.status === 'recevable' ? 'Recevable' : 'Non recevable'"></span>
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <template x-if="req.status === 'pending'">
                                <button @click="openControl(req)"
                                    class="text-indigo-600 hover:text-indigo-800 text-xs font-bold flex items-center gap-1">
                                    <i class="fa-solid fa-clipboard-check"></i> Contrôler
                                </button>
                            </template>
                            <template x-if="req.status === 'recevable'">
                                <a :href="'/subcommission/assign/' + req.id.replace('SC-','')"
                                   @click.prevent="navigateTo('subcommission/assign/' + req.id.replace('SC-',''))"
                                   class="text-emerald-600 hover:text-emerald-800 text-xs font-bold flex items-center gap-1">
                                    <i class="fa-solid fa-user-plus"></i> Proposer Examinateurs
                                </a>
                            </template>
                            <template x-if="req.status === 'rejected'">
                                <span class="text-xs text-slate-400 italic">Clôturé</span>
                            </template>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>

    {{-- ══════════════════════════════════════════════════════
         MODAL — Contrôle de Recevabilité
    ══════════════════════════════════════════════════════ --}}
    <div x-show="controlModal" x-cloak
         class="fixed inset-0 z-[60] flex items-center justify-center p-4"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95">

        <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" @click="controlModal = false"></div>

        <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-3xl flex flex-col max-h-[90vh]">
            {{-- Header --}}
            <div class="flex items-center justify-between px-8 py-6 border-b border-slate-100">
                <div>
                    <h3 class="text-xl font-bold text-slate-900">Contrôle de Recevabilité</h3>
                    <p class="text-sm text-slate-500 mt-0.5" x-text="selectedRequest?.id"></p>
                </div>
                <button @click="controlModal = false" class="text-slate-400 hover:text-slate-600 p-2 rounded-full hover:bg-slate-100 transition-colors">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>

            {{-- Body --}}
            <div class="p-8 overflow-y-auto space-y-6">
                {{-- Postulant Info --}}
                <div class="bg-slate-50 rounded-2xl p-5">
                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Informations du Postulant</h4>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-slate-400 text-xs font-bold">Nom</span>
                            <p class="font-bold text-slate-800" x-text="selectedRequest?.postulant"></p>
                        </div>
                        <div>
                            <span class="text-slate-400 text-xs font-bold">Grade</span>
                            <p class="font-bold text-slate-800" x-text="selectedRequest?.grade"></p>
                        </div>
                        <div>
                            <span class="text-slate-400 text-xs font-bold">Département</span>
                            <p class="font-bold text-slate-800" x-text="selectedRequest?.dept"></p>
                        </div>
                        <div>
                            <span class="text-slate-400 text-xs font-bold">Date de soumission</span>
                            <p class="font-bold text-slate-800" x-text="selectedRequest?.date"></p>
                        </div>
                    </div>
                </div>

                {{-- Publication Info --}}
                <div class="bg-slate-50 rounded-2xl p-5">
                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Détails de la Publication</h4>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div class="col-span-2">
                            <span class="text-slate-400 text-xs font-bold">Titre</span>
                            <p class="font-bold text-slate-800" x-text="selectedRequest?.title"></p>
                        </div>
                        <div>
                            <span class="text-slate-400 text-xs font-bold">Type</span>
                            <p class="font-bold text-slate-800" x-text="selectedRequest?.type"></p>
                        </div>
                        <div>
                            <span class="text-slate-400 text-xs font-bold">Source</span>
                            <p class="font-bold text-slate-800" x-text="selectedRequest?.source"></p>
                        </div>
                        <div>
                            <span class="text-slate-400 text-xs font-bold">ISSN</span>
                            <p class="font-bold text-slate-800" x-text="selectedRequest?.issn || '—'"></p>
                        </div>
                    </div>
                </div>

                {{-- Checklist --}}
                <div>
                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">
                        <i class="fa-solid fa-list-check text-indigo-500 mr-1"></i> Checklist de Recevabilité
                    </h4>
                    <div class="space-y-3">
                        <label class="flex items-center gap-3 p-4 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors cursor-pointer"
                               :class="checklist.form ? 'border-emerald-300 bg-emerald-50/30' : ''">
                            <input type="checkbox" x-model="checklist.form" class="w-5 h-5 text-emerald-600 rounded-lg border-slate-300 focus:ring-emerald-500">
                            <div>
                                <p class="font-bold text-sm text-slate-800">Formulaire correctement rempli</p>
                                <p class="text-xs text-slate-400">Tous les champs obligatoires sont complétés</p>
                            </div>
                        </label>
                        <label class="flex items-center gap-3 p-4 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors cursor-pointer"
                               :class="checklist.resume ? 'border-emerald-300 bg-emerald-50/30' : ''">
                            <input type="checkbox" x-model="checklist.resume" class="w-5 h-5 text-emerald-600 rounded-lg border-slate-300 focus:ring-emerald-500">
                            <div>
                                <p class="font-bold text-sm text-slate-800">Résumé / Abstract joint</p>
                                <p class="text-xs text-slate-400">Le résumé est présent et lisible</p>
                            </div>
                        </label>
                        <label class="flex items-center gap-3 p-4 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors cursor-pointer"
                               :class="checklist.pdf ? 'border-emerald-300 bg-emerald-50/30' : ''">
                            <input type="checkbox" x-model="checklist.pdf" class="w-5 h-5 text-emerald-600 rounded-lg border-slate-300 focus:ring-emerald-500">
                            <div>
                                <p class="font-bold text-sm text-slate-800">Document PDF de la publication</p>
                                <p class="text-xs text-slate-400">Le fichier PDF est joint et complet</p>
                            </div>
                        </label>
                        <label class="flex items-center gap-3 p-4 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors cursor-pointer"
                               :class="checklist.deadline ? 'border-emerald-300 bg-emerald-50/30' : ''">
                            <input type="checkbox" x-model="checklist.deadline" class="w-5 h-5 text-emerald-600 rounded-lg border-slate-300 focus:ring-emerald-500">
                            <div>
                                <p class="font-bold text-sm text-slate-800">Délai de soumission respecté</p>
                                <p class="text-xs text-slate-400">La demande a été déposée dans les délais réglementaires</p>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Reject Reason --}}
                <div x-show="!allChecked" x-transition class="space-y-2">
                    <label class="block text-sm font-bold text-slate-700">Motif de rejet <span class="text-rose-500">*</span></label>
                    <textarea x-model="rejectReason" rows="3"
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-rose-100 focus:border-rose-400 outline-none resize-none transition-all"
                        placeholder="Précisez le motif de non-recevabilité..."></textarea>
                </div>
            </div>

            {{-- Footer --}}
            <div class="px-8 py-6 bg-slate-50 border-t border-slate-100 rounded-b-3xl flex justify-between gap-3">
                <button @click="controlModal = false"
                    class="inline-flex items-center justify-center font-semibold px-5 py-2.5 text-sm bg-white text-slate-700 border border-slate-200 hover:bg-slate-50 rounded-xl transition-all">
                    Annuler
                </button>
                <div class="flex gap-3">
                    <button @click="markRejected()"
                        x-show="!allChecked && rejectReason.trim()"
                        x-transition
                        class="inline-flex items-center justify-center font-semibold px-5 py-2.5 text-sm bg-rose-500 text-white hover:bg-rose-600 rounded-xl transition-all shadow-lg shadow-rose-200 gap-2">
                        <i class="fa-solid fa-xmark"></i> Rejeter le Dossier
                    </button>
                    <button @click="markRecevable()"
                        x-show="allChecked"
                        x-transition
                        class="inline-flex items-center justify-center font-semibold px-5 py-2.5 text-sm bg-emerald-500 text-white hover:bg-emerald-600 rounded-xl transition-all shadow-lg shadow-emerald-200 gap-2">
                        <i class="fa-solid fa-check"></i> Marquer Recevable
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
