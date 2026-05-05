@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="{
    filter: 'all',
    notifications: [
        @foreach($nouvelles as $avis)
        { 
            id: {{ $avis->id_demande }}, 
            type: 'proposal',       
            ref: 'REQ-{{ str_pad($avis->id_demande, 4, "0", STR_PAD_LEFT) }}', 
            title: '{!! addslashes($avis->demande->publication->titre ?? "Sans titre") !!}',    
            from: 'Sous-Commission',  
            commission: '',  
            date: '{{ $avis->created_at->format("Y-m-d") }}', 
            status: 'accepted',      
            read: true 
        },
        @endforeach
    ],
    get filtered() {
        if (this.filter === 'all') return this.notifications;
        if (this.filter === 'new') return this.notifications.filter(n => n.status === 'new');
        if (this.filter === 'proposals') return this.notifications.filter(n => n.type === 'proposal');
        if (this.filter === 'reminders') return this.notifications.filter(n => n.type === 'reminder');
        return this.notifications;
    },
    get unreadCount() {
        return this.notifications.filter(n => !n.read).length;
    },
    acceptProposal(notif) {
        notif.status = 'accepted';
        notif.read = true;
        showToast('Proposition acceptée ! Le dossier a été ajouté à vos évaluations.');
    },
    declineProposal(notif) {
        notif.status = 'declined';
        notif.read = true;
        showToast('Proposition déclinée. La sous-commission sera notifiée.', 'error');
    },
    iconFor(type) {
        return type === 'proposal' ? 'fa-user-plus' : type === 'reminder' ? 'fa-bell' : 'fa-info-circle';
    },
    iconColorFor(type) {
        return type === 'proposal' ? 'bg-indigo-100 text-indigo-600' : type === 'reminder' ? 'bg-amber-100 text-amber-600' : 'bg-slate-100 text-slate-600';
    }
}">

    {{-- ── Header ─────────────────────────────────────────── --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">
                Notifications
                <template x-if="unreadCount > 0">
                    <span class="bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full ml-2" x-text="unreadCount + ' nouvelles'"></span>
                </template>
            </h1>
            <p class="text-slate-500 text-sm mt-1">Propositions d'évaluation et rappels</p>
        </div>
    </div>

    {{-- ── Filter Tabs ────────────────────────────────────── --}}
    <div class="flex gap-2 flex-wrap">
        <template x-for="[key, label] in [['all','Toutes'],['new','Nouvelles'],['proposals','Propositions'],['reminders','Rappels']]" :key="key">
            <button @click="filter = key"
                class="px-4 py-2 rounded-xl text-sm font-bold transition-all"
                :class="filter === key ? 'bg-teal-600 text-white shadow-lg shadow-teal-200' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50'">
                <span x-text="label"></span>
            </button>
        </template>
    </div>

    {{-- ── Notifications List ─────────────────────────────── --}}
    <div class="space-y-3">
        <template x-for="notif in filtered" :key="notif.id">
            <div class="bg-white border rounded-2xl shadow-sm transition-all overflow-hidden"
                :class="!notif.read ? 'border-indigo-300 bg-indigo-50/20' : 'border-slate-200'">
                <div class="p-5 flex flex-col lg:flex-row lg:items-start gap-4">
                    {{-- Icon --}}
                    <div class="flex-shrink-0">
                        <div class="w-11 h-11 rounded-xl flex items-center justify-center" :class="iconColorFor(notif.type)">
                            <i class="fa-solid" :class="iconFor(notif.type)"></i>
                        </div>
                    </div>

                    {{-- Content --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1 flex-wrap">
                            <span class="text-[10px] font-black text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded tracking-tighter uppercase" x-text="notif.ref"></span>
                            <template x-if="!notif.read">
                                <span class="w-2 h-2 bg-indigo-500 rounded-full"></span>
                            </template>
                            <span class="text-xs text-slate-400" x-text="notif.date"></span>
                        </div>

                        {{-- Proposal notification --}}
                        <template x-if="notif.type === 'proposal'">
                            <div>
                                <p class="text-sm text-slate-700">
                                    <strong x-text="notif.from"></strong> vous propose d'examiner le dossier :
                                </p>
                                <h4 class="font-bold text-slate-900 mt-1" x-text="notif.title"></h4>
                                <p class="text-xs text-slate-400 mt-0.5" x-text="'Sous-commission : ' + notif.commission"></p>
                            </div>
                        </template>

                        {{-- Reminder notification --}}
                        <template x-if="notif.type === 'reminder'">
                            <div>
                                <h4 class="font-bold text-slate-900" x-text="notif.title"></h4>
                                <p class="text-sm text-amber-700 mt-1" x-text="notif.message"></p>
                            </div>
                        </template>

                        {{-- Status display for already responded proposals --}}
                        <template x-if="notif.type === 'proposal' && notif.status === 'declined'">
                            <div class="mt-3">
                                <span class="text-xs font-bold px-3 py-1 rounded-full border bg-rose-50 text-rose-700 border-rose-200">❌ Déclinée</span>
                            </div>
                        </template>
                    </div>

                    {{-- Actions --}}
                    <div class="flex-shrink-0 flex items-center gap-2">
                        {{-- New proposal — accept or decline --}}
                        <template x-if="notif.type === 'proposal' && notif.status === 'new'">
                            <div class="flex gap-2">
                                <button @click="acceptProposal(notif)"
                                    class="inline-flex items-center gap-1.5 px-4 py-2.5 text-sm font-bold bg-emerald-500 text-white rounded-xl hover:bg-emerald-600 transition-all shadow-lg shadow-emerald-200">
                                    <i class="fa-solid fa-check"></i> Accepter
                                </button>
                                <button @click="declineProposal(notif)"
                                    class="inline-flex items-center gap-1.5 px-4 py-2.5 text-sm font-bold bg-white text-rose-600 border border-rose-200 rounded-xl hover:bg-rose-50 transition-all">
                                    <i class="fa-solid fa-xmark"></i> Décliner
                                </button>
                            </div>
                        </template>

                        {{-- Accepted proposal — link to review --}}
                        <template x-if="notif.type === 'proposal' && notif.status === 'accepted'">
                            <a :href="'/examiner/review/' + notif.id"
                               @click.prevent="navigateTo('examiner/review/' + notif.id)"
                               class="inline-flex items-center gap-1.5 px-4 py-2.5 text-sm font-bold bg-teal-600 text-white rounded-xl hover:bg-teal-700 transition-all shadow-lg shadow-teal-200">
                                <i class="fa-solid fa-pen-nib"></i> Évaluer
                            </a>
                        </template>
                    </div>
                </div>
            </div>
        </template>

        {{-- Empty state --}}
        <template x-if="filtered.length === 0">
            <div class="bg-white border border-slate-200 rounded-2xl p-12 text-center">
                <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-bell-slash text-slate-300 text-2xl"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-600 mb-1">Aucune notification</h3>
                <p class="text-sm text-slate-400">Vous n'avez aucune notification dans cette catégorie.</p>
            </div>
        </template>
    </div>

</div>
@endsection
