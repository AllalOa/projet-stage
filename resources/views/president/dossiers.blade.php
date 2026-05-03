@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto space-y-6" x-data="{
    filter: 'all',
    dossiers: [
        { id: 'SC-2489', title: 'Ethical Challenges in Modern AI',       postulant: 'Pr. Mehdi Boudiaf',    commission: 'Informatique & IA',  avis: 'favorable',    date: '2026-05-01', status: 'pending' },
        { id: 'SC-2488', title: 'Quantum Computing Optimization',        postulant: 'Dr. Hamid Moussa',     commission: 'Informatique & IA',  avis: 'reserve',      date: '2026-04-29', status: 'pending' },
        { id: 'SC-2485', title: 'Neural Network Pruning Strategies',     postulant: 'Dr. Youssef Amrani',   commission: 'Informatique & IA',  avis: 'favorable',    date: '2026-03-28', status: 'approved' },
        { id: 'SC-2482', title: 'Federated Learning Privacy',            postulant: 'Dr. Amira Bekkouche',  commission: 'Informatique & IA',  avis: 'defavorable',  date: '2026-03-18', status: 'rejected' },
        { id: 'SC-2480', title: 'Cloud-Edge Collaborative Computing',    postulant: 'Pr. Lotfi Boualem',    commission: 'Télécommunications', avis: 'favorable',    date: '2026-03-05', status: 'approved' },
    ],
    get filtered() {
        if (this.filter === 'all') return this.dossiers;
        return this.dossiers.filter(d => d.status === this.filter);
    },
    get counts() {
        return {
            all: this.dossiers.length,
            pending: this.dossiers.filter(d => d.status === 'pending').length,
            approved: this.dossiers.filter(d => d.status === 'approved').length,
            rejected: this.dossiers.filter(d => d.status === 'rejected').length,
        };
    },
    avisLabel(a) { return {favorable:'Favorable',reserve:'Avec Réserve',defavorable:'Défavorable'}[a]; },
    avisClass(a) { return {favorable:'bg-emerald-50 text-emerald-700 border-emerald-200',reserve:'bg-amber-50 text-amber-700 border-amber-200',defavorable:'bg-rose-50 text-rose-700 border-rose-200'}[a]; },
}">

    <div>
        <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Dossiers Reçus</h1>
        <p class="text-slate-500 text-sm mt-1">Dossiers transmis par les sous-commissions pour délibération</p>
    </div>

    <div class="flex gap-2 flex-wrap">
        <template x-for="[key, label] in [['all','Tous'],['pending','En attente'],['approved','Approuvés'],['rejected','Rejetés']]" :key="key">
            <button @click="filter = key" class="px-4 py-2 rounded-xl text-sm font-bold transition-all"
                :class="filter === key ? 'bg-amber-600 text-white shadow-lg shadow-amber-200' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50'">
                <span x-text="label"></span>
                <span class="ml-1 text-xs opacity-70" x-text="'(' + counts[key] + ')'"></span>
            </button>
        </template>
    </div>

    <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white">
        <table class="w-full text-left border-collapse">
            <thead class="bg-slate-50/50 border-b border-slate-200">
                <tr>
                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest">Réf.</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest">Titre</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest">Postulant</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest">Commission</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest">Avis SC</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest">Reçu le</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest">Statut</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <template x-for="d in filtered" :key="d.id">
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4"><span class="text-[10px] font-black text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded tracking-tighter uppercase" x-text="d.id"></span></td>
                        <td class="px-6 py-4 font-bold text-slate-800 max-w-[240px] truncate" x-text="d.title"></td>
                        <td class="px-6 py-4 text-sm text-slate-600" x-text="d.postulant"></td>
                        <td class="px-6 py-4"><span class="text-xs font-bold bg-violet-100 text-violet-700 px-2 py-1 rounded-lg" x-text="d.commission"></span></td>
                        <td class="px-6 py-4"><span class="text-xs font-bold px-2.5 py-0.5 rounded-full border" :class="avisClass(d.avis)" x-text="avisLabel(d.avis)"></span></td>
                        <td class="px-6 py-4 text-slate-500 text-sm italic" x-text="d.date"></td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border uppercase tracking-wider"
                                :class="{'bg-slate-100 text-slate-600 border-slate-200': d.status==='pending','bg-emerald-50 text-emerald-600 border-emerald-200': d.status==='approved','bg-rose-50 text-rose-600 border-rose-200': d.status==='rejected'}">
                                <span class="w-1.5 h-1.5 rounded-full mr-1.5" :class="{'bg-slate-400': d.status==='pending','bg-emerald-500': d.status==='approved','bg-rose-500': d.status==='rejected'}"></span>
                                <span x-text="d.status==='pending'?'En attente':d.status==='approved'?'Approuvé':'Rejeté'"></span>
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <template x-if="d.status === 'pending'">
                                <a :href="'/president/dossier/' + d.id.replace('SC-','')" @click.prevent="navigateTo('president/dossier/' + d.id.replace('SC-',''))" class="text-amber-600 hover:text-amber-800 text-xs font-bold flex items-center gap-1">
                                    <i class="fa-solid fa-gavel"></i> Délibérer
                                </a>
                            </template>
                            <template x-if="d.status !== 'pending'">
                                <a :href="'/president/dossier/' + d.id.replace('SC-','')" @click.prevent="navigateTo('president/dossier/' + d.id.replace('SC-',''))" class="text-slate-500 hover:text-slate-800 text-xs font-bold flex items-center gap-1">
                                    <i class="fa-solid fa-eye"></i> Voir
                                </a>
                            </template>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>
</div>
@endsection
