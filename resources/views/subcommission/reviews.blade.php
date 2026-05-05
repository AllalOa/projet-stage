@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto space-y-6" x-data="{
    filter: 'all',
    dossiers: [
        @foreach($demandes as $d)
        {
            id: 'REQ-{{ str_pad($d->id, 4, "0", STR_PAD_LEFT) }}', 
            real_id: {{ $d->id }},
            title: '{!! addslashes($d->publication->titre ?? "Sans titre") !!}', 
            postulant: '{!! addslashes($d->postulant->prenom . " " . $d->postulant->nom) !!}', 
            type: '{{ $d->publication->journal ? "Journal" : "Manifestation" }}',
            pdf_path: '{{ $d->publication->pdf_path ? asset("storage/" . $d->publication->pdf_path) : "" }}',
            examiners: [
                @foreach($d->avis as $avis)
                { 
                    name: '{!! addslashes($avis->examinateur->prenom . " " . $avis->examinateur->nom) !!}', 
                    specialty: '{!! addslashes($avis->examinateur->specialite ?? "N/A") !!}', 
                    proposal: 'accepted', 
                    avis: '{{ $avis->resultat ?? "pending" }}',   
                    comment: '{!! addslashes(str_replace(["\n","\r"], [" ",""], $avis->commentaire ?? "")) !!}' 
                },
                @endforeach
            ]
        },
        @endforeach
    ],
    getAvisCount(dossier) {
        return dossier.examiners.filter(e => e.proposal === 'accepted' && (e.avis === 'favorable' || e.avis === 'defavorable')).length;
    },
    getAcceptedCount(dossier) {
        return dossier.examiners.filter(e => e.proposal === 'accepted').length;
    },
    isComplete(dossier) {
        return this.getAvisCount(dossier) === 3;
    },
    hasDeclined(dossier) {
        return dossier.examiners.some(e => e.proposal === 'declined');
    },
    get filtered() {
        if (this.filter === 'all') return this.dossiers;
        if (this.filter === 'complete') return this.dossiers.filter(d => this.isComplete(d));
        if (this.filter === 'in_progress') return this.dossiers.filter(d => !this.isComplete(d) && !this.hasDeclined(d));
        if (this.filter === 'needs_action') return this.dossiers.filter(d => this.hasDeclined(d));
        return this.dossiers;
    },
    get counts() {
        return {
            all: this.dossiers.length,
            in_progress: this.dossiers.filter(d => !this.isComplete(d) && !this.hasDeclined(d)).length,
            complete: this.dossiers.filter(d => this.isComplete(d)).length,
            needs_action: this.dossiers.filter(d => this.hasDeclined(d)).length,
        };
    },
    proposalLabel(status) {
        return { proposed: '📩 Proposé', accepted: '✅ Accepté', declined: '❌ Décliné' }[status] || status;
    },
    proposalClass(status) {
        return {
            proposed: 'bg-blue-50 text-blue-700 border-blue-200',
            accepted: 'bg-emerald-50 text-emerald-700 border-emerald-200',
            declined: 'bg-rose-50 text-rose-700 border-rose-200',
        }[status] || '';
    },
    avisLabel(avis) {
        return { pending: '⏳ En attente', favorable: '✅ Favorable', defavorable: '❌ Défavorable' }[avis] || '—';
    },
    avisClass(avis) {
        return {
            pending: 'bg-amber-50 text-amber-700 border-amber-200',
            favorable: 'bg-emerald-50 text-emerald-700 border-emerald-200',
            defavorable: 'bg-rose-50 text-rose-700 border-rose-200',
        }[avis] || 'bg-slate-50 text-slate-400 border-slate-200';
    }
}">

    {{-- ── Header ─────────────────────────────────────────── --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Suivi des Avis</h1>
            <p class="text-slate-500 text-sm mt-1">Suivez les propositions et les avis des examinateurs</p>
        </div>
    </div>

    {{-- ── Filter Tabs ────────────────────────────────────── --}}
    <div class="flex gap-2 flex-wrap">
        <template x-for="[key, label] in [['all','Tous'],['in_progress','En cours'],['complete','Avis complets'],['needs_action','Action requise']]" :key="key">
            <button @click="filter = key"
                class="px-4 py-2 rounded-xl text-sm font-bold transition-all"
                :class="filter === key ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-200' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50'">
                <span x-text="label"></span>
                <span class="ml-1 text-xs opacity-70" x-text="'(' + counts[key] + ')'"></span>
            </button>
        </template>
    </div>

    {{-- ── Dossier Cards ──────────────────────────────────── --}}
    <div class="space-y-6">
        <template x-for="dossier in filtered" :key="dossier.id">
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm hover:shadow-md transition-all overflow-hidden">
                {{-- Card Header --}}
                <div class="px-6 py-5 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <span class="text-[10px] font-black text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded tracking-tighter uppercase" x-text="dossier.id"></span>
                        <div>
                            <h3 class="font-bold text-slate-800" x-text="dossier.title"></h3>
                            <p class="text-xs text-slate-500 mt-0.5">
                                <span x-text="dossier.postulant"></span> · <span x-text="dossier.type"></span>
                                <template x-if="dossier.pdf_path">
                                    <span> · <a :href="dossier.pdf_path" target="_blank" class="text-indigo-600 hover:text-indigo-800 font-bold"><i class="fa-solid fa-file-pdf text-rose-500"></i> Voir la publication</a></span>
                                </template>
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        {{-- Progress bar --}}
                        <div class="flex items-center gap-2">
                            <div class="w-24 bg-slate-100 rounded-full h-2">
                                <div class="h-2 rounded-full transition-all duration-500"
                                    :class="isComplete(dossier) ? 'bg-emerald-500' : 'bg-indigo-500'"
                                    :style="'width:' + (getAvisCount(dossier)/3*100) + '%'"></div>
                            </div>
                            <span class="text-xs font-bold"
                                :class="isComplete(dossier) ? 'text-emerald-600' : 'text-slate-500'"
                                x-text="getAvisCount(dossier) + '/3 avis'"></span>
                        </div>
                        {{-- Actions --}}
                        <template x-if="isComplete(dossier)">
                            <a :href="'/subcommission/consolidate/' + dossier.real_id"
                               @click.prevent="navigateTo('subcommission/consolidate/' + dossier.real_id)"
                               class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-bold bg-emerald-500 text-white rounded-xl hover:bg-emerald-600 transition-all shadow-lg shadow-emerald-200">
                                <i class="fa-solid fa-file-circle-check"></i> Consolider
                            </a>
                        </template>
                        <template x-if="hasDeclined(dossier)">
                            <a :href="'/subcommission/assign/' + dossier.real_id"
                               @click.prevent="navigateTo('subcommission/assign/' + dossier.real_id)"
                               class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-bold bg-amber-500 text-white rounded-xl hover:bg-amber-600 transition-all shadow-lg shadow-amber-200">
                                <i class="fa-solid fa-user-plus"></i> Remplacer
                            </a>
                        </template>
                    </div>
                </div>

                {{-- Examiners Grid --}}
                <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <template x-for="(ex, idx) in dossier.examiners" :key="idx">
                        <div class="border rounded-xl p-4 transition-all"
                            :class="ex.proposal === 'declined' ? 'border-rose-200 bg-rose-50/30' : ex.avis === 'favorable' ? 'border-emerald-200 bg-emerald-50/30' : ex.avis === 'defavorable' ? 'border-rose-200 bg-rose-50/30' : 'border-slate-200'">

                            {{-- Examiner Info --}}
                            <div class="flex items-center gap-3 mb-3">
                                <img :src="'https://ui-avatars.com/api/?name=' + encodeURIComponent(ex.name) + '&background=random&size=40'" class="w-10 h-10 rounded-xl" alt="">
                                <div>
                                    <p class="font-bold text-sm text-slate-800" x-text="ex.name"></p>
                                    <p class="text-[10px] text-slate-400 font-bold" x-text="ex.specialty"></p>
                                </div>
                            </div>

                            {{-- Proposal Status --}}
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Proposition:</span>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full border"
                                    :class="proposalClass(ex.proposal)"
                                    x-text="proposalLabel(ex.proposal)"></span>
                            </div>

                            {{-- Avis Status (only if accepted) --}}
                            <template x-if="ex.proposal === 'accepted'">
                                <div>
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Avis:</span>
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full border"
                                            :class="avisClass(ex.avis)"
                                            x-text="avisLabel(ex.avis)"></span>
                                    </div>
                                    <template x-if="ex.comment">
                                        <p class="text-xs text-slate-600 italic bg-slate-50 p-2 rounded-lg mt-2" x-text="'\"' + ex.comment + '\"'"></p>
                                    </template>
                                </div>
                            </template>

                            {{-- Declined message --}}
                            <template x-if="ex.proposal === 'declined'">
                                <p class="text-xs text-rose-600 italic mt-1">L'examinateur a décliné. Veuillez proposer un remplaçant.</p>
                            </template>

                            {{-- Proposed / Waiting message --}}
                            <template x-if="ex.proposal === 'proposed'">
                                <p class="text-xs text-blue-600 italic mt-1">En attente de réponse à la proposition...</p>
                            </template>
                        </div>
                    </template>
                </div>
            </div>
        </template>
    </div>

</div>
@endsection
