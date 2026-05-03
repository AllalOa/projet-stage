@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto space-y-8" x-data="{
    filter: 'all',
    assignments: [
        { id: 1, ref: 'SC-2491', title: 'Deep Learning in Medical Imaging',          postulant: 'Dr. Ahmed Benali',   type: 'Journal',       source: 'IEEE Trans. Medical Imaging',  date: '2026-04-30', deadline: '2026-05-15', proposalStatus: 'accepted', reviewStatus: 'pending' },
        { id: 2, ref: 'SC-2490', title: 'Blockchain for Decentralized Identity',     postulant: 'Dr. Samira Kaci',    type: 'Manifestation', source: 'ICSE 2026',                    date: '2026-04-28', deadline: '2026-05-12', proposalStatus: 'accepted', reviewStatus: 'submitted' },
        { id: 3, ref: 'SC-2487', title: 'Edge Computing IoT Networks',               postulant: 'Dr. Fatima Zerhouni',type: 'Manifestation', source: 'MobiCom 2026',                 date: '2026-05-01', deadline: '2026-05-18', proposalStatus: 'accepted', reviewStatus: 'pending' },
        { id: 4, ref: 'SC-2485', title: 'Neural Network Pruning Strategies',         postulant: 'Dr. Youssef Amrani', type: 'Manifestation', source: 'NeurIPS 2026',                 date: '2026-03-15', deadline: '2026-03-30', proposalStatus: 'accepted', reviewStatus: 'submitted' },
    ],
    get filtered() {
        if (this.filter === 'all') return this.assignments;
        return this.assignments.filter(a => a.reviewStatus === this.filter);
    },
    get counts() {
        return {
            all: this.assignments.length,
            pending: this.assignments.filter(a => a.reviewStatus === 'pending').length,
            submitted: this.assignments.filter(a => a.reviewStatus === 'submitted').length,
        };
    },
    daysLeft(deadline) {
        const diff = Math.ceil((new Date(deadline) - new Date()) / (1000*60*60*24));
        return diff;
    }
}">

    {{-- ── Welcome Banner ─────────────────────────────────── --}}
    <div class="relative overflow-hidden bg-gradient-to-r from-teal-700 via-teal-800 to-emerald-900 rounded-3xl p-8 text-white shadow-xl">
        <div class="absolute -top-8 -right-8 w-48 h-48 bg-white/5 rounded-full"></div>
        <div class="absolute top-4 right-16 w-24 h-24 bg-white/5 rounded-full"></div>
        <div class="relative flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <p class="text-teal-300 text-sm font-bold uppercase tracking-widest mb-1">
                    <i class="fa-solid fa-microscope mr-1"></i> Espace Examinateur
                </p>
                <h1 class="text-3xl font-extrabold tracking-tight">Pr. Hamid El Moussaoui</h1>
                <p class="text-teal-300/70 mt-2 text-sm">Intelligence Artificielle · LRIA · USTHB</p>
            </div>
            <div class="flex gap-3 flex-wrap">
                <a href="/examiner/notifications" onclick="event.preventDefault(); navigateTo('examiner/notifications')"
                    class="flex items-center gap-2 bg-white text-teal-800 font-bold px-5 py-3 rounded-xl hover:bg-teal-50 transition-all shadow-lg text-sm">
                    <i class="fa-solid fa-bell"></i> Notifications
                    <span class="bg-red-500 text-white text-[10px] font-black px-1.5 py-0.5 rounded-full ml-1">2</span>
                </a>
            </div>
        </div>
    </div>

    {{-- ── Stat Cards ──────────────────────────────────────── --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-5">
        @php
        $stats = [
            ['label' => 'Total Assignés',     'value' => '04', 'icon' => 'fa-file-signature',  'bg' => 'bg-teal-50',    'text' => 'text-teal-600',    'border' => 'border-teal-200'],
            ['label' => 'En Attente',          'value' => '02', 'icon' => 'fa-hourglass-half',  'bg' => 'bg-amber-50',   'text' => 'text-amber-600',   'border' => 'border-amber-200'],
            ['label' => 'Avis Rendus',         'value' => '02', 'icon' => 'fa-check-double',    'bg' => 'bg-emerald-50', 'text' => 'text-emerald-600', 'border' => 'border-emerald-200'],
            ['label' => 'Délai Urgent',        'value' => '01', 'icon' => 'fa-clock',           'bg' => 'bg-rose-50',    'text' => 'text-rose-600',    'border' => 'border-rose-200'],
        ];
        @endphp

        @foreach($stats as $s)
        <div class="bg-white border {{ $s['border'] }} rounded-2xl p-5 shadow-sm hover:shadow-md transition-all group">
            <div class="flex items-center justify-between mb-4">
                <div class="{{ $s['bg'] }} {{ $s['text'] }} w-10 h-10 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i class="fa-solid {{ $s['icon'] }}"></i>
                </div>
            </div>
            <p class="text-3xl font-black text-slate-900">{{ $s['value'] }}</p>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">{{ $s['label'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- ── Filter Tabs ────────────────────────────────────── --}}
    <div class="flex gap-2 flex-wrap">
        <template x-for="[key, label] in [['all','Tous'],['pending','En attente'],['submitted','Avis rendus']]" :key="key">
            <button @click="filter = key"
                class="px-4 py-2 rounded-xl text-sm font-bold transition-all"
                :class="filter === key ? 'bg-teal-600 text-white shadow-lg shadow-teal-200' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50'">
                <span x-text="label"></span>
                <span class="ml-1 text-xs opacity-70" x-text="'(' + counts[key] + ')'"></span>
            </button>
        </template>
    </div>

    {{-- ── Assignments List ───────────────────────────────── --}}
    <div class="space-y-4">
        <template x-for="a in filtered" :key="a.id">
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm hover:shadow-md transition-all overflow-hidden">
                <div class="p-6 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    {{-- Left: Info --}}
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2 flex-wrap">
                            <span class="text-[10px] font-black text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded tracking-tighter uppercase" x-text="a.ref"></span>
                            <span class="text-xs font-bold px-2 py-1 rounded-full"
                                :class="a.type === 'Journal' ? 'bg-indigo-100 text-indigo-700' : 'bg-violet-100 text-violet-700'"
                                x-text="a.type"></span>
                            {{-- Status badge --}}
                            <span class="text-xs font-bold px-2 py-1 rounded-full border"
                                :class="a.reviewStatus === 'pending' ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-emerald-50 text-emerald-700 border-emerald-200'"
                                x-text="a.reviewStatus === 'pending' ? '⏳ En attente' : '✅ Avis rendu'"></span>
                        </div>
                        <h3 class="text-lg font-extrabold text-slate-900 mb-1" x-text="a.title"></h3>
                        <p class="text-sm text-slate-500">
                            <span x-text="a.postulant"></span> · <span x-text="a.source"></span>
                        </p>
                        <div class="flex gap-4 mt-3 text-xs text-slate-400">
                            <span><i class="fa-solid fa-calendar mr-1"></i> Reçu: <span x-text="a.date"></span></span>
                            <span :class="daysLeft(a.deadline) <= 3 ? 'text-rose-500 font-bold' : ''">
                                <i class="fa-solid fa-clock mr-1"></i> Deadline: <span x-text="a.deadline"></span>
                                <template x-if="daysLeft(a.deadline) > 0">
                                    <span x-text="'(' + daysLeft(a.deadline) + 'j restants)'"></span>
                                </template>
                                <template x-if="daysLeft(a.deadline) <= 0">
                                    <span class="text-rose-600 font-bold">(Expiré)</span>
                                </template>
                            </span>
                        </div>
                    </div>

                    {{-- Right: Action --}}
                    <div class="flex-shrink-0">
                        <template x-if="a.reviewStatus === 'pending'">
                            <a :href="'/examiner/review/' + a.id"
                               @click.prevent="navigateTo('examiner/review/' + a.id)"
                               class="inline-flex items-center gap-2 px-6 py-3 text-sm font-bold bg-teal-600 text-white rounded-xl hover:bg-teal-700 transition-all shadow-lg shadow-teal-200">
                                <i class="fa-solid fa-pen-nib"></i> Évaluer
                            </a>
                        </template>
                        <template x-if="a.reviewStatus === 'submitted'">
                            <a :href="'/examiner/review/' + a.id"
                               @click.prevent="navigateTo('examiner/review/' + a.id)"
                               class="inline-flex items-center gap-2 px-6 py-3 text-sm font-bold bg-white text-slate-700 border border-slate-200 rounded-xl hover:bg-slate-50 transition-all">
                                <i class="fa-solid fa-eye"></i> Voir mon avis
                            </a>
                        </template>
                    </div>
                </div>
            </div>
        </template>
    </div>

</div>
@endsection
