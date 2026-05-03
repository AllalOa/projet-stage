@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto space-y-8">

    {{-- ── Welcome Banner ─────────────────────────────────── --}}
    <div class="relative overflow-hidden bg-gradient-to-r from-indigo-600 via-indigo-700 to-violet-700 rounded-3xl p-8 text-white shadow-xl shadow-indigo-200">
        <div class="absolute -top-8 -right-8 w-48 h-48 bg-white/5 rounded-full"></div>
        <div class="absolute top-4 right-16 w-24 h-24 bg-white/5 rounded-full"></div>
        <div class="relative flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <p class="text-indigo-200 text-sm font-bold uppercase tracking-widest mb-1">
                    <i class="fa-solid fa-sun mr-1"></i> Bonjour
                </p>
                <h1 class="text-3xl font-extrabold tracking-tight">Dr. Ahmed Benali</h1>
                <p class="text-indigo-200 mt-2 text-sm">Université des Sciences — Département Informatique</p>
            </div>
            <div class="flex gap-3 flex-wrap">
                <button onclick="Alpine.store('modals').openModal('create-request')"
                    class="flex items-center gap-2 bg-white text-indigo-700 font-bold px-5 py-3 rounded-xl hover:bg-indigo-50 transition-all shadow-lg text-sm">
                    <i class="fa-solid fa-plus"></i> Nouvelle Demande
                </button>
                <a href="/postulant/profile" onclick="event.preventDefault(); navigateTo('postulant/profile')"
                    class="flex items-center gap-2 bg-white/20 text-white font-bold px-5 py-3 rounded-xl hover:bg-white/30 transition-all text-sm border border-white/30">
                    <i class="fa-solid fa-circle-user"></i> Mon Profil
                </a>
            </div>
        </div>
    </div>

    {{-- ── Stat Cards ──────────────────────────────────────── --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-5">
        @php
        $stats = [
            ['label' => 'Total Soumissions', 'value' => '12', 'icon' => 'fa-layer-group',   'color' => 'indigo',   'bg' => 'bg-indigo-50',  'text' => 'text-indigo-600',  'border' => 'border-indigo-200'],
            ['label' => 'En Cours',          'value' => '03', 'icon' => 'fa-hourglass-half', 'color' => 'amber',    'bg' => 'bg-amber-50',   'text' => 'text-amber-600',   'border' => 'border-amber-200'],
            ['label' => 'Approuvées',        'value' => '08', 'icon' => 'fa-circle-check',   'color' => 'emerald',  'bg' => 'bg-emerald-50', 'text' => 'text-emerald-600', 'border' => 'border-emerald-200'],
            ['label' => 'Rejetées',          'value' => '01', 'icon' => 'fa-circle-xmark',   'color' => 'rose',     'bg' => 'bg-rose-50',    'text' => 'text-rose-600',    'border' => 'border-rose-200'],
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

    {{-- ── Recent Requests ─────────────────────────────────── --}}
    <x-card title="Dernières Demandes" subtitle="Vos soumissions récentes">
        <x-slot name="headerAction">
            <a href="/postulant/requests" onclick="event.preventDefault(); navigateTo('postulant/requests')"
               class="text-sm font-bold text-indigo-600 hover:text-indigo-800 flex items-center gap-1">
                Voir tout <i class="fa-solid fa-arrow-right text-xs"></i>
            </a>
        </x-slot>

        @php
        $requests = [
            ['title' => 'Deep Learning in Medical Imaging',       'type' => 'Journal',        'date' => '2026-04-12', 'reviews' => '2/3', 'status' => 'in_review'],
            ['title' => 'Blockchain for Decentralized Identity',  'type' => 'Manifestation',  'date' => '2026-03-25', 'reviews' => '3/3', 'status' => 'approved'],
            ['title' => 'Quantum Computing Optimization',         'type' => 'Journal',        'date' => '2026-03-10', 'reviews' => '1/3', 'status' => 'pending'],
        ];
        @endphp

        <x-table :headers="['Titre', 'Type', 'Date', 'Avis', 'Statut', 'Actions']">
            @foreach($requests as $req)
            <tr class="hover:bg-slate-50 transition-colors">
                <td class="px-6 py-4 font-bold text-slate-800 max-w-[260px] truncate">{{ $req['title'] }}</td>
                <td class="px-6 py-4">
                    <span class="text-xs font-bold px-2 py-1 rounded-full
                        {{ $req['type'] === 'Journal' ? 'bg-indigo-100 text-indigo-700' : 'bg-violet-100 text-violet-700' }}">
                        {{ $req['type'] }}
                    </span>
                </td>
                <td class="px-6 py-4 text-slate-500 text-sm italic">{{ $req['date'] }}</td>
                <td class="px-6 py-4">
                    @php $pct = (intval(substr($req['reviews'],0,1))/3)*100; @endphp
                    <div class="w-full bg-slate-100 rounded-full h-1.5 max-w-[80px]">
                        <div class="bg-indigo-500 h-1.5 rounded-full" style="width: {{ $pct }}%"></div>
                    </div>
                    <span class="text-[10px] text-slate-400 font-bold mt-1 block">{{ $req['reviews'] }} avis</span>
                </td>
                <td class="px-6 py-4"><x-badge :status="$req['status']" /></td>
                <td class="px-6 py-4">
                    <a href="/postulant/requests" onclick="event.preventDefault(); navigateTo('postulant/requests')"
                       class="text-indigo-600 hover:text-indigo-800 text-xs font-bold flex items-center gap-1">
                        <i class="fa-solid fa-eye"></i> Suivi
                    </a>
                </td>
            </tr>
            @endforeach
        </x-table>
    </x-card>

</div>

{{-- ══════════════════════════════════════════════════════
     MODAL — Nouvelle Demande (multi-step)
══════════════════════════════════════════════════════ --}}
<x-modal id="create-request" title="Nouvelle Soumission" size="lg">
    <div x-data="{
        step: 1,
        type: '',
        form: { title:'', source:'', issn:'', doi:'', abstract:'', file: null, filename:'' },
        next(n) { this.step = n },
        handleFile(e) {
            const f = e.target.files[0];
            if (f) { this.form.filename = f.name; this.form.file = f; }
        }
    }">
        {{-- Progress Steps --}}
        <div class="flex items-center mb-10 relative">
            <div class="absolute top-5 left-0 right-0 h-0.5 bg-slate-100 -z-10"></div>
            @foreach([1=>'Type', 2=>'Détails', 3=>'Résumé', 4=>'Confirmer'] as $n => $label)
            <div class="flex-1 flex flex-col items-center gap-2">
                <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm transition-all duration-300"
                     :class="step >= {{ $n }} ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-200' : 'bg-white border-2 border-slate-200 text-slate-400'">
                    <span x-show="step > {{ $n }}"><i class="fa-solid fa-check text-xs"></i></span>
                    <span x-show="step <= {{ $n }}">{{ $n }}</span>
                </div>
                <span class="text-[10px] font-bold uppercase tracking-wider transition-colors"
                      :class="step >= {{ $n }} ? 'text-indigo-600' : 'text-slate-400'">{{ $label }}</span>
            </div>
            @endforeach
        </div>

        {{-- STEP 1 — Type --}}
        <div x-show="step === 1" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
            <h4 class="text-lg font-bold text-slate-800 mb-6">Choisir le type de publication</h4>
            <div class="grid grid-cols-2 gap-4">
                <button @click="type='journal'; next(2)"
                    class="p-6 border-2 border-slate-200 rounded-2xl hover:border-indigo-500 hover:bg-indigo-50 transition-all text-left group">
                    <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-indigo-200 transition-colors">
                        <i class="fa-solid fa-book-open text-indigo-600 text-xl"></i>
                    </div>
                    <h5 class="font-bold text-slate-900">Article de Journal</h5>
                    <p class="text-xs text-slate-500 mt-1">Journaux scientifiques avec ISSN, facteur d'impact...</p>
                </button>
                <button @click="type='manifestation'; next(2)"
                    class="p-6 border-2 border-slate-200 rounded-2xl hover:border-violet-500 hover:bg-violet-50 transition-all text-left group">
                    <div class="w-12 h-12 bg-violet-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-violet-200 transition-colors">
                        <i class="fa-solid fa-chalkboard-user text-violet-600 text-xl"></i>
                    </div>
                    <h5 class="font-bold text-slate-900">Manifestation Scientifique</h5>
                    <p class="text-xs text-slate-500 mt-1">Conférences, séminaires, ateliers...</p>
                </button>
            </div>
        </div>

        {{-- STEP 2 — Détails --}}
        <div x-show="step === 2" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0" class="space-y-4">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Titre Scientifique <span class="text-rose-500">*</span></label>
                <input type="text" x-model="form.title" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 outline-none transition-all" placeholder="Titre complet de votre travail">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Source (Journal / Conférence)</label>
                    <input type="text" x-model="form.source" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 outline-none transition-all" placeholder="Ex: Nature Medicine">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">ISSN / DOI</label>
                    <input type="text" x-model="form.issn" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 outline-none transition-all" placeholder="Ex: 10.xxxx/xxxxx">
                </div>
            </div>
            <div class="flex justify-between mt-6">
                <button @click="next(1)" class="flex items-center gap-2 text-sm font-bold text-slate-500 hover:text-slate-800 px-4 py-2 rounded-xl hover:bg-slate-100 transition-all">
                    <i class="fa-solid fa-arrow-left"></i> Retour
                </button>
                <button @click="form.title ? next(3) : null"
                    :class="form.title ? 'bg-indigo-600 hover:bg-indigo-700 text-white shadow-lg shadow-indigo-200' : 'bg-slate-100 text-slate-400 cursor-not-allowed'"
                    class="flex items-center gap-2 text-sm font-bold px-6 py-3 rounded-xl transition-all">
                    Suivant <i class="fa-solid fa-arrow-right"></i>
                </button>
            </div>
        </div>

        {{-- STEP 3 — Résumé & Fichier --}}
        <div x-show="step === 3" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0" class="space-y-5">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Résumé / Abstract <span class="text-rose-500">*</span></label>
                <textarea x-model="form.abstract" rows="4" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 outline-none resize-none transition-all" placeholder="Résumé en français ou anglais (max 500 mots)..."></textarea>
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Joindre le Dossier (PDF) <span class="text-rose-500">*</span></label>
                <label class="block cursor-pointer group">
                    <input type="file" accept=".pdf" class="hidden" @change="handleFile($event)">
                    <div class="border-2 border-dashed rounded-2xl p-8 text-center transition-all"
                         :class="form.filename ? 'border-emerald-400 bg-emerald-50' : 'border-slate-300 bg-slate-50 hover:border-indigo-400 hover:bg-indigo-50'">
                        <template x-if="!form.filename">
                            <div>
                                <i class="fa-solid fa-cloud-arrow-up text-4xl text-slate-300 mb-3 group-hover:text-indigo-400 transition-colors"></i>
                                <p class="font-bold text-slate-500 group-hover:text-indigo-600 transition-colors">Glisser & Déposer votre PDF</p>
                                <p class="text-xs text-slate-400 mt-1">ou cliquez pour parcourir — Max 10 MB</p>
                            </div>
                        </template>
                        <template x-if="form.filename">
                            <div class="flex items-center justify-center gap-3">
                                <i class="fa-solid fa-file-pdf text-emerald-500 text-3xl"></i>
                                <div class="text-left">
                                    <p class="font-bold text-emerald-700 text-sm" x-text="form.filename"></p>
                                    <p class="text-xs text-emerald-500">Fichier prêt à être soumis</p>
                                </div>
                            </div>
                        </template>
                    </div>
                </label>
            </div>
            <div class="flex justify-between mt-2">
                <button @click="next(2)" class="flex items-center gap-2 text-sm font-bold text-slate-500 hover:text-slate-800 px-4 py-2 rounded-xl hover:bg-slate-100 transition-all">
                    <i class="fa-solid fa-arrow-left"></i> Retour
                </button>
                <button @click="form.abstract ? next(4) : null"
                    :class="form.abstract ? 'bg-indigo-600 hover:bg-indigo-700 text-white shadow-lg shadow-indigo-200' : 'bg-slate-100 text-slate-400 cursor-not-allowed'"
                    class="flex items-center gap-2 text-sm font-bold px-6 py-3 rounded-xl transition-all">
                    Vérifier <i class="fa-solid fa-arrow-right"></i>
                </button>
            </div>
        </div>

        {{-- STEP 4 — Confirmer --}}
        <div x-show="step === 4" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
            <div class="bg-slate-50 rounded-2xl p-6 space-y-3 mb-6">
                <h4 class="font-black text-slate-800 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-clipboard-check text-indigo-500"></i> Récapitulatif
                </h4>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase mb-1">Type</p>
                        <p class="font-bold text-slate-800 capitalize" x-text="type === 'journal' ? 'Article de Journal' : 'Manifestation Scientifique'"></p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase mb-1">Source</p>
                        <p class="font-bold text-slate-800" x-text="form.source || '—'"></p>
                    </div>
                    <div class="col-span-2">
                        <p class="text-xs font-bold text-slate-400 uppercase mb-1">Titre</p>
                        <p class="font-bold text-slate-800" x-text="form.title"></p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase mb-1">ISSN / DOI</p>
                        <p class="font-bold text-slate-800" x-text="form.issn || '—'"></p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase mb-1">Fichier PDF</p>
                        <p class="font-bold text-emerald-600 flex items-center gap-1" x-text="form.filename || 'Aucun fichier'">
                            <i class="fa-solid fa-file-pdf" x-show="form.filename"></i>
                        </p>
                    </div>
                </div>
            </div>
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 text-sm text-amber-800 flex gap-3 mb-6">
                <i class="fa-solid fa-triangle-exclamation mt-0.5 flex-shrink-0"></i>
                <p>Une fois soumise, votre demande sera examinée par la sous-commission. Vous recevrez une notification à chaque changement de statut.</p>
            </div>
            <div class="flex justify-between">
                <button @click="next(3)" class="flex items-center gap-2 text-sm font-bold text-slate-500 hover:text-slate-800 px-4 py-2 rounded-xl hover:bg-slate-100 transition-all">
                    <i class="fa-solid fa-arrow-left"></i> Retour
                </button>
                <button @click="$store.modals.close(); showToast('Demande soumise avec succès !', 'success')"
                    class="flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-6 py-3 rounded-xl transition-all shadow-lg shadow-emerald-200">
                    <i class="fa-solid fa-paper-plane"></i> Soumettre
                </button>
            </div>
        </div>

    </div>
</x-modal>
@endsection
