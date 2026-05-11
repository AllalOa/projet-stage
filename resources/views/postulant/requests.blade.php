@extends('layouts.app')

@section('content')

@php
    $requestsData = $demandes->map(function($d) {
        $statut   = $d->statut;
        $decision = $d->decision_finale;

        if (in_array($statut, ['en_attente', 'reception'])) {
            $status = 'pending';
        } elseif ($statut === 'fini' && $decision === 'favorable') {
            $status = 'approved';
        } elseif ($statut === 'fini' && $decision === 'defavorable') {
            $status = 'rejected';
        } else {
            $status = 'in_review';
        }

        return [
            'id'       => $d->id,
            'title'    => $d->publication->titre ?? 'Sans titre',
            'type'     => $d->publication->journal ? 'Journal' : ($d->publication->manifestation ? 'Manifestation' : '—'),
            'date'     => $d->created_at->format('Y-m-d'),
            'reviews'  => $d->avis->count() . '/3',
            'status'   => $status,
            'source'   => $d->publication->journal->nom_journal
                       ?? $d->publication->manifestation->nom_manifestation
                       ?? '—',
            'issn'     => $d->publication->journal->issn ?? '—',
            'abstract' => $d->publication->resume ?? '',
            'pdf_path' => $d->publication->pdf_path ? asset('storage/' . $d->publication->pdf_path) : '',
        ];
    })->values()->toArray();
@endphp

<script>
function postulantRequests() {
    return {
        filter:   'all',
        selected: null,
        requests: @json($requestsData),

        get filtered() {
            if (this.filter === 'all') return this.requests;
            return this.requests.filter(r => r.status === this.filter);
        },

        countByStatus(key) {
            if (key === 'all') return this.requests.length;
            return this.requests.filter(r => r.status === key).length;
        },

        statusLabel(s) {
            return { pending: 'En attente', in_review: 'En cours', approved: 'Approuvée', rejected: 'Rejetée' }[s] || s;
        },

        statusColor(s) {
            return {
                pending:   'bg-slate-100 text-slate-600',
                in_review: 'bg-amber-100 text-amber-700',
                approved:  'bg-emerald-100 text-emerald-700',
                rejected:  'bg-rose-100 text-rose-700',
            }[s] || 'bg-slate-100 text-slate-600';
        },

        isOpen(req) {
            return this.selected !== null && this.selected.id === req.id;
        },

        toggleSuivi(req) {
            this.selected = this.isOpen(req) ? null : req;
        },

        timeline(s) {
            return [
                { label: 'Soumission reçue',     done: true,                              date: 'Confirmé' },
                { label: 'Vérification dossier',  done: s !== 'pending',                  date: s !== 'pending' ? 'Validé' : 'En attente' },
                { label: 'Examen par les pairs',  done: s === 'approved' || s === 'rejected', date: s === 'in_review' ? 'En cours...' : (s === 'approved' || s === 'rejected' ? 'Terminé' : 'En attente') },
                { label: 'Décision finale',       done: s === 'approved' || s === 'rejected', date: s === 'approved' ? 'Approuvée ✓' : (s === 'rejected' ? 'Rejetée ✗' : 'En attente') },
            ];
        },

        printPDF(req) { window.printSubmissionPDF(req); }
    };
}
</script>

<div class="max-w-7xl mx-auto space-y-6" x-data="postulantRequests()">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900">Mes Demandes</h1>
            <p class="text-slate-500 text-sm mt-1">Suivi de toutes vos soumissions scientifiques</p>
        </div>
        <button onclick="document.getElementById('new-request-modal').classList.remove('hidden')"
            class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-5 py-3 rounded-xl shadow-lg shadow-indigo-200 transition-all text-sm">
            <i class="fa-solid fa-plus"></i> Nouvelle Demande
        </button>
    </div>

    @if(session('success'))
    <div class="flex items-center gap-2 bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-bold px-4 py-3 rounded-xl">
        <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
    </div>
    @endif

    @if($errors->any())
    <div class="flex items-start gap-2 bg-rose-50 border border-rose-200 text-rose-700 text-sm font-bold px-4 py-3 rounded-xl">
        <i class="fa-solid fa-triangle-exclamation mt-0.5"></i>
        <div>
            <p class="font-black">Erreur de soumission — veuillez corriger :</p>
            <ul class="mt-1 font-normal list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    {{-- Ré-ouvrir le modal si erreur --}}
    <script>document.addEventListener('DOMContentLoaded',function(){document.getElementById('new-request-modal').classList.remove('hidden');});</script>
    @endif

    {{-- Filter Tabs --}}
    <div class="flex gap-2 flex-wrap">
        <template x-for="tab in [
            {key:'all',       label:'Toutes'},
            {key:'pending',   label:'En attente'},
            {key:'in_review', label:'En cours'},
            {key:'approved',  label:'Approuvées'},
            {key:'rejected',  label:'Rejetées'}
        ]" :key="tab.key">
            <button @click="filter = tab.key"
                class="px-4 py-2 rounded-xl text-sm font-bold transition-all"
                :class="filter === tab.key
                    ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-200'
                    : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50'">
                <span x-text="tab.label"></span>
                <span class="ml-1 opacity-70 text-xs" x-text="'(' + countByStatus(tab.key) + ')'"></span>
            </button>
        </template>
    </div>

    {{-- Table
         NOTE: x-for itère sur des <tbody> — valide en HTML5.
         Cela permet d'avoir 2 <tr> (ligne + suivi) par itération,
         ce qu'Alpine ne supporte pas avec un seul root element. --}}
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-100">
                    <th class="px-6 py-4 text-left text-xs font-black text-slate-400 uppercase tracking-widest">Titre</th>
                    <th class="px-4 py-4 text-left text-xs font-black text-slate-400 uppercase tracking-widest">Type</th>
                    <th class="px-4 py-4 text-left text-xs font-black text-slate-400 uppercase tracking-widest">Date</th>
                    <th class="px-4 py-4 text-left text-xs font-black text-slate-400 uppercase tracking-widest">Avis</th>
                    <th class="px-4 py-4 text-left text-xs font-black text-slate-400 uppercase tracking-widest">Statut</th>
                    <th class="px-4 py-4 text-left text-xs font-black text-slate-400 uppercase tracking-widest">Actions</th>
                </tr>
            </thead>

            <template x-for="req in filtered" :key="req.id">
                <tbody>
                    {{-- Ligne principale --}}
                    <tr class="border-b border-slate-50 hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4">
                            <p class="font-bold text-slate-800 max-w-[280px] truncate" x-text="req.title"></p>
                            <p class="text-xs text-slate-400 mt-0.5" x-text="req.source"></p>
                        </td>
                        <td class="px-4 py-4">
                            <span class="text-xs font-bold px-2 py-1 rounded-full"
                                :class="req.type === 'Journal' ? 'bg-indigo-100 text-indigo-700' : 'bg-violet-100 text-violet-700'"
                                x-text="req.type"></span>
                        </td>
                        <td class="px-4 py-4 text-slate-500 text-sm" x-text="req.date"></td>
                        <td class="px-4 py-4">
                            <div class="w-20 bg-slate-100 rounded-full h-1.5 mb-1">
                                <div class="h-1.5 rounded-full bg-indigo-500 transition-all"
                                     :style="'width:' + (parseInt(req.reviews) / 3 * 100) + '%'"></div>
                            </div>
                            <span class="text-[10px] text-slate-400 font-bold" x-text="req.reviews + ' avis'"></span>
                        </td>
                        <td class="px-4 py-4">
                            <span class="text-xs font-bold px-3 py-1.5 rounded-full"
                                  :class="statusColor(req.status)"
                                  x-text="statusLabel(req.status)"></span>
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-2">
                                <button @click="toggleSuivi(req)"
                                    class="flex items-center gap-1 text-xs font-bold px-3 py-1.5 rounded-lg transition-all border"
                                    :class="isOpen(req)
                                        ? 'bg-indigo-600 text-white border-indigo-600 shadow-md shadow-indigo-200'
                                        : 'text-indigo-600 border-indigo-200 hover:bg-indigo-50'">
                                    <i class="fa-solid" :class="isOpen(req) ? 'fa-chevron-up' : 'fa-timeline'"></i>
                                    <span x-text="isOpen(req) ? 'Fermer' : 'Suivi'"></span>
                                </button>
                                <button @click="printPDF(req)"
                                    class="flex items-center gap-1 text-xs font-bold text-slate-600 hover:text-indigo-700 px-3 py-1.5 rounded-lg hover:bg-indigo-50 transition-all border border-slate-200 hover:border-indigo-200">
                                    <i class="fa-solid fa-certificate text-indigo-400"></i> Reçu
                                </button>
                            </div>
                        </td>
                    </tr>

                    {{-- Ligne timeline (suivi) --}}
                    <tr x-show="isOpen(req)"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0">
                        <td colspan="6" class="bg-indigo-50/70 px-8 py-6 border-b border-indigo-100">

                            {{-- Header suivi --}}
                            <div class="flex items-center justify-between mb-5">
                                <h4 class="text-sm font-black text-indigo-900 flex items-center gap-2">
                                    <i class="fa-solid fa-route text-indigo-500"></i>
                                    Suivi — <span class="font-medium text-indigo-600 truncate max-w-[300px]" x-text="req.title"></span>
                                </h4>
                                <span class="text-xs font-bold px-2.5 py-1 rounded-full shrink-0"
                                      :class="statusColor(req.status)"
                                      x-text="statusLabel(req.status)"></span>
                            </div>

                            {{-- Étapes --}}
                            <div class="flex items-start">
                                <template x-for="(step, idx) in timeline(req.status)" :key="idx">
                                    <div class="flex-1 flex flex-col items-center relative">
                                        <template x-if="idx < 3">
                                            <div class="absolute top-4 left-1/2 w-full h-0.5 z-0"
                                                 :class="step.done ? 'bg-indigo-400' : 'bg-slate-200'"></div>
                                        </template>
                                        <div class="relative z-10 w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold border-2 transition-all"
                                             :class="step.done
                                                ? 'bg-indigo-600 border-indigo-600 text-white shadow-md shadow-indigo-200'
                                                : 'bg-white border-slate-300 text-slate-400'">
                                            <i :class="step.done ? 'fa-solid fa-check' : 'fa-solid fa-clock'"></i>
                                        </div>
                                        <p class="text-xs font-bold mt-2 text-center leading-tight px-1"
                                           :class="step.done ? 'text-indigo-800' : 'text-slate-400'"
                                           x-text="step.label"></p>
                                        <p class="text-[10px] mt-0.5 text-center"
                                           :class="step.done ? 'text-indigo-500' : 'text-slate-300'"
                                           x-text="step.date"></p>
                                    </div>
                                </template>
                            </div>

                            {{-- Résumé --}}
                            <div class="mt-5 p-4 bg-white rounded-xl border border-indigo-100">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Résumé soumis</p>
                                <template x-if="req.abstract">
                                    <p class="text-sm text-slate-700 leading-relaxed" x-text="req.abstract"></p>
                                </template>
                                <template x-if="!req.abstract">
                                    <p class="text-sm text-slate-400 italic">Aucun résumé fourni.</p>
                                </template>
                            </div>

                            {{-- Actions --}}
                            <div class="flex justify-end mt-4 gap-2">
                                <template x-if="req.pdf_path">
                                    <a :href="req.pdf_path" target="_blank"
                                        class="flex items-center gap-2 bg-white border border-rose-200 hover:bg-rose-50 text-rose-600 font-bold px-4 py-2 rounded-xl text-xs transition-all">
                                        <i class="fa-solid fa-file-pdf"></i> Voir la Publication
                                    </a>
                                </template>
                                <button @click="printPDF(req)"
                                    class="flex items-center gap-2 bg-white border border-indigo-200 hover:bg-indigo-50 text-indigo-600 font-bold px-4 py-2 rounded-xl text-xs transition-all">
                                    <i class="fa-solid fa-file-arrow-down"></i> Télécharger Attestation
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </template>

            {{-- Empty state --}}
            <tbody x-show="filtered.length === 0">
                <tr>
                    <td colspan="6" class="py-16 text-center">
                        <i class="fa-solid fa-folder-open text-4xl text-slate-200 mb-3 block"></i>
                        <p class="text-slate-400 font-bold">Aucune demande trouvée</p>
                        <p class="text-slate-300 text-sm mt-1">Soumettez votre première demande de validation</p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

</div>

{{-- ── Modal Nouvelle Demande ─────────────────────────── --}}
<div id="new-request-modal" class="hidden fixed inset-0 z-[70] flex items-center justify-center p-4">
    <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm"
         onclick="document.getElementById('new-request-modal').classList.add('hidden')"></div>
    <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-8 py-6 border-b border-slate-100 sticky top-0 bg-white z-10 rounded-t-3xl">
            <h3 class="text-xl font-bold text-slate-900">Nouvelle Demande de Validation</h3>
            <button onclick="document.getElementById('new-request-modal').classList.add('hidden')"
                class="text-slate-400 hover:text-slate-600 p-2 rounded-full hover:bg-slate-100 transition-colors">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>

        <form method="POST" action="{{ route('postulant.requests.submit') }}" enctype="multipart/form-data"
              class="p-8 space-y-5" x-data="{ typePublication: 'journal' }">
            @csrf

            {{-- Erreurs inline --}}
            @if($errors->any())
            <div class="flex items-start gap-2 bg-rose-50 border border-rose-200 text-rose-700 text-sm px-4 py-3 rounded-xl mb-2">
                <i class="fa-solid fa-circle-exclamation mt-0.5"></i>
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- ① Type de publication --}}
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">
                    Type de publication <span class="text-rose-500">*</span>
                </label>
                <div class="flex gap-3">
                    <label class="flex-1 cursor-pointer">
                        <input type="radio" name="type_publication" value="journal"
                               x-model="typePublication" class="sr-only">
                        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border-2 transition-all"
                             :class="typePublication === 'journal'
                                ? 'border-indigo-500 bg-indigo-50 text-indigo-700'
                                : 'border-slate-200 bg-slate-50 text-slate-500 hover:border-slate-300'">
                            <i class="fa-solid fa-book-open text-lg"></i>
                            <div>
                                <p class="font-bold text-sm">Article de Journal</p>
                                <p class="text-xs opacity-70">Revue scientifique, ISSN</p>
                            </div>
                        </div>
                    </label>
                    <label class="flex-1 cursor-pointer">
                        <input type="radio" name="type_publication" value="manifestation"
                               x-model="typePublication" class="sr-only">
                        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border-2 transition-all"
                             :class="typePublication === 'manifestation'
                                ? 'border-violet-500 bg-violet-50 text-violet-700'
                                : 'border-slate-200 bg-slate-50 text-slate-500 hover:border-slate-300'">
                            <i class="fa-solid fa-users-line text-lg"></i>
                            <div>
                                <p class="font-bold text-sm">Manifestation</p>
                                <p class="text-xs opacity-70">Congrès, séminaire, colloque</p>
                            </div>
                        </div>
                    </label>
                </div>
            </div>

            {{-- ② Champs communs --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Titre de la publication <span class="text-rose-500">*</span></label>
                    <input type="text" name="titre" required
                           class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 outline-none text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Auteur Principal <span class="text-rose-500">*</span></label>
                    <input type="text" name="auteur_principal" required
                           class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 outline-none text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Date de Publication <span class="text-rose-500">*</span></label>
                    <input type="date" name="date_publication" required
                           class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 outline-none text-sm">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Sous-Commission <span class="text-rose-500">*</span></label>
                    <select name="id_sous_comm" required
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 outline-none text-sm">
                        <option value="">Sélectionner une sous-commission</option>
                        @foreach($sousCommissions as $sc)
                            <option value="{{ $sc->id }}">{{ $sc->nom }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- ③ Champs Journal (conditionnels) --}}
            <div x-show="typePublication === 'journal'" x-transition class="space-y-4 p-4 bg-indigo-50/60 rounded-2xl border border-indigo-100">
                <p class="text-xs font-black text-indigo-600 uppercase tracking-wider flex items-center gap-2">
                    <i class="fa-solid fa-book-open"></i> Informations du Journal
                </p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Nom du Journal <span class="text-rose-500">*</span></label>
                        <input type="text" name="nom_journal"
                               :required="typePublication === 'journal'"
                               class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 outline-none text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">ISSN</label>
                        <input type="text" name="issn"
                               class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 outline-none text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Facteur d'impact</label>
                        <input type="number" step="0.01" name="facteur_impact"
                               class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 outline-none text-sm">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Lien URL</label>
                        <input type="url" name="lien_url"
                               class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 outline-none text-sm">
                    </div>
                </div>
            </div>

            {{-- ④ Champs Manifestation (conditionnels) --}}
            <div x-show="typePublication === 'manifestation'" x-transition class="space-y-4 p-4 bg-violet-50/60 rounded-2xl border border-violet-100">
                <p class="text-xs font-black text-violet-600 uppercase tracking-wider flex items-center gap-2">
                    <i class="fa-solid fa-users-line"></i> Informations de la Manifestation
                </p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Nom de la Manifestation <span class="text-rose-500">*</span></label>
                        <input type="text" name="nom_manifestation"
                               :required="typePublication === 'manifestation'"
                               class="w-full px-4 py-3 bg-white border border-violet-200 rounded-xl focus:ring-4 focus:ring-violet-100 focus:border-violet-500 outline-none text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Type</label>
                        <select name="type_manifestation"
                                class="w-full px-4 py-3 bg-white border border-violet-200 rounded-xl focus:ring-4 focus:ring-violet-100 focus:border-violet-500 outline-none text-sm">
                            <option value="">Sélectionner…</option>
                            <option value="congres">Congrès</option>
                            <option value="seminaire">Séminaire</option>
                            <option value="colloque">Colloque</option>
                            <option value="conference">Conférence</option>
                            <option value="journee_etude">Journée d'étude</option>
                            <option value="autre">Autre</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Date de l'événement</label>
                        <input type="date" name="date_event"
                               class="w-full px-4 py-3 bg-white border border-violet-200 rounded-xl focus:ring-4 focus:ring-violet-100 focus:border-violet-500 outline-none text-sm">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Lieu</label>
                        <input type="text" name="lieu"
                               class="w-full px-4 py-3 bg-white border border-violet-200 rounded-xl focus:ring-4 focus:ring-violet-100 focus:border-violet-500 outline-none text-sm"
                               placeholder="Ville, Pays">
                    </div>
                </div>
            </div>

            {{-- ⑤ Résumé + PDF --}}
            <div class="grid grid-cols-1 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Résumé (Abstract)</label>
                    <textarea name="resume" rows="3"
                              class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 outline-none text-sm"></textarea>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Fichier PDF <span class="text-rose-500">*</span></label>
                    <input type="file" name="pdf" accept="application/pdf" required
                           class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl outline-none text-sm">
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('new-request-modal').classList.add('hidden')"
                    class="px-5 py-2.5 text-sm font-bold bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-all">Annuler</button>
                <button type="submit"
                    class="px-6 py-2.5 text-sm font-bold bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-200">Soumettre</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
function printSubmissionPDF(req) {
    const labels = {pending:'En attente', in_review:'En cours', approved:'Approuvée', rejected:'Rejetée'};
    const bg     = {approved:'#d1fae5', rejected:'#ffe4e6', in_review:'#fef3c7', pending:'#f1f5f9'};
    const color  = {approved:'#065f46', rejected:'#9f1239', in_review:'#92400e', pending:'#475569'};
    const w = window.open('', '_blank');
    w.document.write(
        '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Demande #' + req.id + '</title>' +
        '<style>body{font-family:Arial,sans-serif;padding:40px;color:#1e293b}h1{color:#4f46e5;font-size:20px}' +
        '.sub{color:#94a3b8;font-size:12px;margin-bottom:20px}.meta{background:#f8fafc;padding:20px;border-radius:8px;margin:20px 0;border:1px solid #e2e8f0}' +
        '.row{display:flex;gap:40px;margin-bottom:14px;flex-wrap:wrap}.cell{min-width:120px}' +
        '.lbl{font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;margin-bottom:3px}.val{font-size:13px;font-weight:600}' +
        '.badge{display:inline-block;padding:4px 12px;border-radius:999px;font-size:11px;font-weight:700;background:' + bg[req.status] + ';color:' + color[req.status] + '}' +
        '.abstract{margin-top:16px;padding:16px;background:#fff;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;line-height:1.6}' +
        '.footer{margin-top:40px;font-size:10px;color:#94a3b8;border-top:1px solid #e2e8f0;padding-top:10px;display:flex;justify-content:space-between}' +
        '</style></head><body>' +
        '<h1>Scientific Council &ndash; Attestation de Soumission</h1>' +
        '<p class="sub">Référence #' + req.id + ' &bull; Généré le ' + new Date().toLocaleDateString('fr-FR') + '</p>' +
        '<div class="meta">' +
            '<div class="row"><div class="cell"><div class="lbl">Titre</div><div class="val">' + req.title + '</div></div>' +
            '<div class="cell"><div class="lbl">Statut</div><span class="badge">' + (labels[req.status] || req.status) + '</span></div></div>' +
            '<div class="row"><div class="cell"><div class="lbl">Type</div><div class="val">' + req.type + '</div></div>' +
            '<div class="cell"><div class="lbl">Source</div><div class="val">' + req.source + '</div></div>' +
            '<div class="cell"><div class="lbl">ISSN</div><div class="val">' + req.issn + '</div></div></div>' +
            '<div class="row"><div class="cell"><div class="lbl">Date</div><div class="val">' + req.date + '</div></div>' +
            '<div class="cell"><div class="lbl">Avis reçus</div><div class="val">' + req.reviews + '</div></div></div>' +
            '<div class="abstract"><div class="lbl">Résumé</div><p style="margin:6px 0 0">' + (req.abstract || 'Non renseigné') + '</p></div>' +
        '</div>' +
        '<div class="footer"><span>Scientific Council CMS &copy; 2026</span><span>Document officiel</span></div>' +
        '</body></html>'
    );
    w.document.close();
    setTimeout(function(){ w.print(); }, 400);
}
</script>
@endpush
