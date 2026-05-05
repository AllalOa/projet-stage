@extends('layouts.app')

@section('content')

@php
    $requestsData = $demandes->map(function($d) {
        $statut = $d->statut;
        if (in_array($statut, ['en_attente', 'reception'])) {
            $status = 'pending';
        } elseif ($statut === 'avis') {
            $status = 'avis';
        } elseif ($statut === 'consolidation') {
            $status = 'consolidation';
        } elseif ($statut === 'transmis') {
            $status = 'transmis';
        } else {
            $status = 'autre';
        }

        return [
            'id'             => 'REQ-' . str_pad($d->id, 4, '0', STR_PAD_LEFT),
            'real_id'        => $d->id,
            'title'          => $d->publication->titre ?? 'Sans titre',
            'postulant'      => ($d->postulant->prenom ?? '') . ' ' . ($d->postulant->nom ?? ''),
            'dept'           => $d->postulant->departement ?? 'Aucun',
            'grade'          => $d->postulant->grade ?? 'Aucun',
            'type'           => ($d->publication->journal ? 'Journal' : 'Manifestation'),
            'source'         => $d->publication->journal->nom_journal ?? '—',
            'issn'           => $d->publication->journal->issn ?? '—',
            'facteur_impact' => $d->publication->journal->facteur_impact ?? '—',
            'auteur'         => $d->publication->auteur_principal ?? '—',
            'date_pub'       => $d->publication->date_publication ?? '—',
            'date'           => $d->created_at->format('Y-m-d'),
            'resume'         => $d->publication->resume ?? '',
            'pdf_url'        => $d->publication->pdf_path ? asset('storage/' . $d->publication->pdf_path) : '',
            'avis_count'     => $d->avis->count(),
            'status'         => $status,
        ];
    })->values()->toArray();
@endphp

<script>
function subcommissionRequests() {
    return {
        filter:   'all',
        expanded: null,
        requests: @json($requestsData),

        get filtered() {
            if (this.filter === 'all') return this.requests;
            return this.requests.filter(r => r.status === this.filter);
        },

        get counts() {
            return {
                all:           this.requests.length,
                pending:       this.requests.filter(r => r.status === 'pending').length,
                avis:          this.requests.filter(r => r.status === 'avis').length,
                consolidation: this.requests.filter(r => r.status === 'consolidation').length,
                transmis:      this.requests.filter(r => r.status === 'transmis').length,
            };
        },

        toggleExpand(id) {
            this.expanded = this.expanded === id ? null : id;
        }
    };
}
</script>

<div class="max-w-7xl mx-auto space-y-6" x-data="subcommissionRequests()">

    {{-- ── Header ──────────────────────────────────────────────── --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Demandes Reçues</h1>
            <p class="text-slate-500 text-sm mt-1">Consultez les dossiers et proposez des examinateurs directement</p>
        </div>
        @if(session('success'))
        <div class="flex items-center gap-2 bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-bold px-4 py-2 rounded-xl">
            <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
        </div>
        @endif
        @if(session('error'))
        <div class="flex items-center gap-2 bg-rose-50 border border-rose-200 text-rose-700 text-sm font-bold px-4 py-2 rounded-xl">
            <i class="fa-solid fa-circle-xmark"></i> {{ session('error') }}
        </div>
        @endif
    </div>

    {{-- ── Filter Tabs ─────────────────────────────────────────── --}}
    <div class="flex gap-2 flex-wrap">
        <template x-for="tab in [
            {key:'all',           label:'Toutes'},
            {key:'pending',       label:'En attente'},
            {key:'avis',          label:'En examen'},
            {key:'consolidation', label:'Consolidé'},
            {key:'transmis',      label:'Transmis'}
        ]" :key="tab.key">
            <button @click="filter = tab.key"
                class="px-4 py-2 rounded-xl text-sm font-bold transition-all"
                :class="filter === tab.key
                    ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-200'
                    : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50'">
                <span x-text="tab.label"></span>
                <span class="ml-1 text-xs opacity-70" x-text="'(' + counts[tab.key] + ')'"></span>
            </button>
        </template>
    </div>

    {{-- ── Requests List ────────────────────────────────────────── --}}
    <div class="space-y-3">

        <template x-if="filtered.length === 0">
            <div class="bg-white border border-slate-200 rounded-2xl p-12 text-center">
                <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-inbox text-slate-400 text-2xl"></i>
                </div>
                <p class="font-bold text-slate-500">Aucune demande dans cette catégorie.</p>
            </div>
        </template>

        <template x-for="req in filtered" :key="req.id">
            <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-shadow">

                {{-- ── Ligne principale (cliquable) ──────────────── --}}
                <div class="flex items-center justify-between px-6 py-4 cursor-pointer select-none"
                     @click="toggleExpand(req.id)">

                    <div class="flex items-center gap-4 flex-1 min-w-0">
                        <span class="text-[10px] font-black text-indigo-600 bg-indigo-50 px-2 py-1 rounded tracking-tighter uppercase shrink-0"
                              x-text="req.id"></span>
                        <div class="min-w-0">
                            <p class="font-bold text-slate-800 truncate text-sm" x-text="req.title"></p>
                            <p class="text-xs text-slate-400 mt-0.5">
                                <span x-text="req.postulant"></span>
                                <span class="mx-1">·</span>
                                <span class="italic" x-text="req.dept"></span>
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 shrink-0 ml-4">
                        <span class="text-xs font-bold px-2.5 py-1 rounded-full hidden sm:inline"
                              :class="req.type === 'Journal' ? 'bg-indigo-100 text-indigo-700' : 'bg-violet-100 text-violet-700'"
                              x-text="req.type"></span>

                        <span class="text-xs text-slate-400 italic hidden md:inline" x-text="req.date"></span>

                        {{-- Badge statut --}}
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border uppercase tracking-wider"
                              :class="{
                                  'bg-amber-50 text-amber-600 border-amber-200':       req.status === 'pending',
                                  'bg-blue-50 text-blue-600 border-blue-200':          req.status === 'avis',
                                  'bg-violet-50 text-violet-600 border-violet-200':    req.status === 'consolidation',
                                  'bg-emerald-50 text-emerald-600 border-emerald-200': req.status === 'transmis',
                              }">
                            <span class="w-1.5 h-1.5 rounded-full mr-1.5"
                                  :class="{
                                      'bg-amber-400':   req.status === 'pending',
                                      'bg-blue-500':    req.status === 'avis',
                                      'bg-violet-500':  req.status === 'consolidation',
                                      'bg-emerald-500': req.status === 'transmis',
                                  }"></span>
                            <span x-text="{
                                pending:       'En attente',
                                avis:          'En examen',
                                consolidation: 'Consolidé',
                                transmis:      'Transmis'
                            }[req.status] || req.status"></span>
                        </span>

                        <i class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform duration-300"
                           :class="expanded === req.id ? 'rotate-180' : ''"></i>
                    </div>
                </div>

                {{-- ── Panneau expandable ───────────────────────── --}}
                <div x-show="expanded === req.id"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 -translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-1"
                     class="border-t border-slate-100 bg-slate-50/50">

                    <div class="px-6 py-5 space-y-5">

                        {{-- Grille info publication --}}
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Auteur principal</p>
                                <p class="text-sm font-bold text-slate-700" x-text="req.auteur"></p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Journal / Source</p>
                                <p class="text-sm font-bold text-slate-700" x-text="req.source"></p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">ISSN</p>
                                <p class="text-sm font-bold text-slate-700" x-text="req.issn"></p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Facteur d'impact</p>
                                <p class="text-sm font-bold text-slate-700" x-text="req.facteur_impact"></p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Grade</p>
                                <p class="text-sm font-bold text-slate-700" x-text="req.grade"></p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Département</p>
                                <p class="text-sm font-bold text-slate-700" x-text="req.dept"></p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Date publication</p>
                                <p class="text-sm font-bold text-slate-700" x-text="req.date_pub"></p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Avis reçus</p>
                                <p class="text-sm font-bold text-slate-700" x-text="req.avis_count + ' avis'"></p>
                            </div>
                        </div>

                        {{-- Résumé --}}
                        <template x-if="req.resume">
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">
                                    <i class="fa-solid fa-align-left text-indigo-400 mr-1"></i> Résumé / Abstract
                                </p>
                                <div class="bg-white border border-slate-200 rounded-xl px-5 py-4 max-h-40 overflow-y-auto">
                                    <p class="text-sm text-slate-600 leading-relaxed" x-text="req.resume"></p>
                                </div>
                            </div>
                        </template>
                        <template x-if="!req.resume">
                            <p class="text-xs text-slate-400 italic">
                                <i class="fa-solid fa-circle-exclamation text-amber-400 mr-1"></i> Aucun résumé fourni.
                            </p>
                        </template>

                        {{-- Actions --}}
                        <div class="flex items-center justify-between pt-3 border-t border-slate-200">

                            {{-- Bouton PDF --}}
                            <div>
                                <template x-if="req.pdf_url">
                                    <a :href="req.pdf_url" target="_blank"
                                       class="inline-flex items-center gap-2 px-4 py-2 bg-rose-50 text-rose-600 border border-rose-200 hover:bg-rose-100 rounded-xl text-sm font-bold transition-all">
                                        <i class="fa-solid fa-file-pdf text-rose-500"></i>
                                        Voir le PDF
                                    </a>
                                </template>
                                <template x-if="!req.pdf_url">
                                    <span class="text-xs text-slate-400 italic flex items-center gap-1">
                                        <i class="fa-solid fa-file-slash"></i> Aucun PDF joint
                                    </span>
                                </template>
                            </div>

                            {{-- Bouton action principale --}}
                            <div>
                                <template x-if="req.status === 'pending'">
                                    <a :href="'/subcommission/assign/' + req.real_id"
                                       @click.prevent="navigateTo('subcommission/assign/' + req.real_id)"
                                       class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl transition-all shadow-md shadow-indigo-200">
                                        <i class="fa-solid fa-user-plus"></i> Assigner des Examinateurs
                                    </a>
                                </template>
                                <template x-if="req.status === 'avis'">
                                    <a href="/subcommission/reviews"
                                       @click.prevent="navigateTo('subcommission/reviews')"
                                       class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-50 text-blue-700 border border-blue-200 hover:bg-blue-100 text-sm font-bold rounded-xl transition-all">
                                        <i class="fa-solid fa-comments"></i> Suivre les Avis
                                    </a>
                                </template>
                                <template x-if="req.status === 'consolidation'">
                                    <a href="/subcommission/transmit"
                                       @click.prevent="navigateTo('subcommission/transmit')"
                                       class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-xl transition-all shadow-md shadow-emerald-200">
                                        <i class="fa-solid fa-paper-plane"></i> Transmettre
                                    </a>
                                </template>
                                <template x-if="req.status === 'transmis'">
                                    <span class="text-xs text-slate-400 italic flex items-center gap-1">
                                        <i class="fa-solid fa-check-double text-emerald-500"></i> Transmis au Conseil
                                    </span>
                                </template>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </template>
    </div>

</div>
@endsection
