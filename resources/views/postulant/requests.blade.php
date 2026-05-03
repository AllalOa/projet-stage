@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto space-y-6" x-data="{
    filter: 'all',
    selected: null,
    requests: [
        { id:1, title:'Deep Learning in Medical Imaging',       type:'Journal',       date:'2026-04-12', reviews:'2/3', status:'in_review',  source:'Nature Medicine',       issn:'1078-8956', abstract:'Étude sur l\'application du deep learning dans l\'analyse d\'IRM cérébraux.' },
        { id:2, title:'Blockchain for Decentralized Identity',  type:'Manifestation', date:'2026-03-25', reviews:'3/3', status:'approved',   source:'IEEE Conference 2026',  issn:'10.1109/x', abstract:'Proposition d\'une architecture blockchain pour la gestion d\'identité.' },
        { id:3, title:'Quantum Computing Optimization',         type:'Journal',       date:'2026-03-10', reviews:'1/3', status:'pending',    source:'Science Advances',      issn:'2375-2548', abstract:'Algorithme d\'optimisation quantique pour les problèmes NP-difficiles.' },
        { id:4, title:'NLP for Arabic Text Classification',     type:'Journal',       date:'2026-02-18', reviews:'3/3', status:'rejected',   source:'ACM Computing',         issn:'0360-0300', abstract:'Modèle de classification de texte arabe basé sur BERT multilingue.' },
        { id:5, title:'IoT Security Framework for Smart Cities',type:'Manifestation', date:'2026-01-30', reviews:'2/3', status:'in_review',  source:'ICSC 2026',             issn:'—',         abstract:'Cadre de sécurité pour les objets connectés dans les villes intelligentes.' },
    ],
    get filtered() {
        if (this.filter === 'all') return this.requests;
        return this.requests.filter(r => r.status === this.filter);
    },
    statusLabel(s) {
        return { pending:'En attente', in_review:'En cours', approved:'Approuvée', rejected:'Rejetée' }[s] || s;
    },
    statusColor(s) {
        return {
            pending:   'bg-slate-100 text-slate-600',
            in_review: 'bg-amber-100 text-amber-700',
            approved:  'bg-emerald-100 text-emerald-700',
            rejected:  'bg-rose-100 text-rose-700'
        }[s];
    },
    timeline(s) {
        const base = [
            { label:'Soumission reçue',        done: true,  date:'Confirmé' },
            { label:'Vérification éligibilité', done: s !== 'pending', date: s !== 'pending' ? 'Validé' : 'En attente' },
            { label:'Examen par les pairs',     done: s === 'approved' || s === 'rejected', date: s === 'in_review' ? 'En cours...' : (s === 'approved' || s === 'rejected' ? 'Terminé' : 'En attente') },
            { label:'Décision finale',          done: s === 'approved' || s === 'rejected', date: s === 'approved' ? 'Approuvée ✓' : (s === 'rejected' ? 'Rejetée ✗' : 'En attente') },
        ];
        return base;
    },
    printPDF(req) { window.printSubmissionPDF(req); }
}">

    {{-- ── Header ───────────────────────────────────────── --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900">Mes Demandes</h1>
            <p class="text-slate-500 text-sm mt-1">Suivi de toutes vos soumissions scientifiques</p>
        </div>
        <button onclick="Alpine.store('modals').openModal('new-request-modal')"
            class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-5 py-3 rounded-xl shadow-lg shadow-indigo-200 transition-all text-sm">
            <i class="fa-solid fa-plus"></i> Nouvelle Demande
        </button>
    </div>

    {{-- ── Filter Tabs ─────────────────────────────────── --}}
    <div class="flex gap-2 flex-wrap">
        @foreach(['all'=>'Toutes','pending'=>'En attente','in_review'=>'En cours','approved'=>'Approuvées','rejected'=>'Rejetées'] as $key=>$label)
        <button @click="filter = '{{ $key }}'"
            class="px-4 py-2 rounded-xl text-sm font-bold transition-all"
            :class="filter === '{{ $key }}'
                ? '{{ $key === 'all' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-200' :
                    ($key === 'pending' ? 'bg-slate-700 text-white' :
                    ($key === 'in_review' ? 'bg-amber-500 text-white' :
                    ($key === 'approved' ? 'bg-emerald-600 text-white' : 'bg-rose-600 text-white'))) }}'
                : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50'">
            {{ $label }}
            <span class="ml-1 opacity-70 text-xs">
                (<span x-text="{{ $key === 'all' ? 'requests.length' : 'requests.filter(r=>r.status===\'' . $key . '\').length' }}"></span>)
            </span>
        </button>
        @endforeach
    </div>

    {{-- ── Requests Table ──────────────────────────────── --}}
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-100">
                    <th class="px-6 py-4 text-left text-xs font-black text-slate-400 uppercase tracking-widest">Titre</th>
                    <th class="px-4 py-4 text-left text-xs font-black text-slate-400 uppercase tracking-widest">Type</th>
                    <th class="px-4 py-4 text-left text-xs font-black text-slate-400 uppercase tracking-widest">Date</th>
                    <th class="px-4 py-4 text-left text-xs font-black text-slate-400 uppercase tracking-widest">Progression</th>
                    <th class="px-4 py-4 text-left text-xs font-black text-slate-400 uppercase tracking-widest">Statut</th>
                    <th class="px-4 py-4 text-left text-xs font-black text-slate-400 uppercase tracking-widest">Actions</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="req in filtered" :key="req.id">
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
                        <td class="px-4 py-4 text-slate-500 text-sm italic" x-text="req.date"></td>
                        <td class="px-4 py-4">
                            <div class="w-20 bg-slate-100 rounded-full h-1.5">
                                <div class="h-1.5 rounded-full bg-indigo-500 transition-all"
                                     :style="'width:' + (parseInt(req.reviews) / 3 * 100) + '%'"></div>
                            </div>
                            <span class="text-[10px] text-slate-400 font-bold mt-1 block" x-text="req.reviews + ' avis'"></span>
                        </td>
                        <td class="px-4 py-4">
                            <span class="text-xs font-bold px-3 py-1.5 rounded-full" :class="statusColor(req.status)" x-text="statusLabel(req.status)"></span>
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-2">
                                {{-- Suivi --}}
                                <button @click="selected = selected?.id === req.id ? null : req"
                                    class="flex items-center gap-1 text-xs font-bold text-indigo-600 hover:text-indigo-800 px-3 py-1.5 rounded-lg hover:bg-indigo-50 transition-all border border-indigo-200">
                                    <i class="fa-solid fa-timeline"></i> Suivi
                                </button>
                                {{-- PDF --}}
                                <button @click="printPDF(req)"
                                    class="flex items-center gap-1 text-xs font-bold text-slate-600 hover:text-slate-800 px-3 py-1.5 rounded-lg hover:bg-slate-100 transition-all border border-slate-200">
                                    <i class="fa-solid fa-file-pdf text-rose-500"></i> PDF
                                </button>
                            </div>
                        </td>
                    </tr>

                    {{-- ── Timeline Row (Suivi) ──── --}}
                    <tr x-show="selected?.id === req.id" x-transition>
                        <td colspan="6" class="bg-indigo-50 px-8 py-6 border-b border-indigo-100">
                            <div class="flex items-start gap-4 mb-4">
                                <h4 class="text-sm font-black text-indigo-900 flex items-center gap-2">
                                    <i class="fa-solid fa-route text-indigo-500"></i>
                                    Suivi de la demande
                                </h4>
                                <span class="text-xs font-bold px-2 py-1 rounded-full ml-auto" :class="statusColor(req.status)" x-text="statusLabel(req.status)"></span>
                            </div>

                            <div class="flex gap-0 items-start">
                                <template x-for="(step, idx) in timeline(req.status)" :key="idx">
                                    <div class="flex-1 flex flex-col items-center relative">
                                        {{-- Connector --}}
                                        <template x-if="idx < timeline(req.status).length - 1">
                                            <div class="absolute top-4 left-1/2 w-full h-0.5 z-0 transition-colors"
                                                 :class="step.done ? 'bg-indigo-400' : 'bg-slate-200'"></div>
                                        </template>
                                        {{-- Dot --}}
                                        <div class="relative z-10 w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold border-2 transition-all"
                                             :class="step.done
                                                ? 'bg-indigo-600 border-indigo-600 text-white shadow-md shadow-indigo-200'
                                                : 'bg-white border-slate-300 text-slate-400'">
                                            <i :class="step.done ? 'fa-solid fa-check' : 'fa-solid fa-clock'"></i>
                                        </div>
                                        {{-- Label --}}
                                        <p class="text-xs font-bold mt-2 text-center" :class="step.done ? 'text-indigo-800' : 'text-slate-400'" x-text="step.label"></p>
                                        <p class="text-[10px] mt-0.5 text-center" :class="step.done ? 'text-indigo-500' : 'text-slate-300'" x-text="step.date"></p>
                                    </div>
                                </template>
                            </div>

                            {{-- Abstract --}}
                            <div class="mt-5 p-4 bg-white rounded-xl border border-indigo-100">
                                <p class="text-xs font-black text-slate-400 uppercase tracking-wider mb-1">Résumé soumis</p>
                                <p class="text-sm text-slate-700" x-text="req.abstract"></p>
                            </div>

                            <div class="flex justify-end mt-4">
                                <button @click="printPDF(req)"
                                    class="flex items-center gap-2 bg-white border border-slate-200 hover:border-indigo-400 text-slate-700 font-bold px-4 py-2 rounded-xl text-xs transition-all hover:text-indigo-700">
                                    <i class="fa-solid fa-file-arrow-down text-rose-500"></i> Télécharger PDF
                                </button>
                            </div>
                        </td>
                    </tr>
                </template>

                {{-- Empty state --}}
                <tr x-show="filtered.length === 0">
                    <td colspan="6" class="py-16 text-center">
                        <i class="fa-solid fa-folder-open text-4xl text-slate-200 mb-3"></i>
                        <p class="text-slate-400 font-bold">Aucune demande trouvée</p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

</div>

{{-- ── Quick New Request Modal ─────────────────────── --}}
<x-modal id="new-request-modal" title="Nouvelle Demande" size="md">
    <div class="space-y-4">
        <p class="text-slate-500 text-sm">Choisissez le type de publication pour commencer votre soumission.</p>
        <div class="grid grid-cols-2 gap-4">
            <button @click="$store.modals.close(); showToast('Formulaire Journal ouvert', 'success')"
                class="p-5 border-2 border-slate-200 rounded-2xl hover:border-indigo-500 hover:bg-indigo-50 transition-all text-left group">
                <i class="fa-solid fa-book-open text-2xl text-indigo-500 mb-3"></i>
                <h5 class="font-bold text-slate-900 text-sm">Article de Journal</h5>
                <p class="text-xs text-slate-400 mt-1">Revues, ISSN, facteur d'impact</p>
            </button>
            <button @click="$store.modals.close(); showToast('Formulaire Manifestation ouvert', 'success')"
                class="p-5 border-2 border-slate-200 rounded-2xl hover:border-violet-500 hover:bg-violet-50 transition-all text-left group">
                <i class="fa-solid fa-chalkboard-user text-2xl text-violet-500 mb-3"></i>
                <h5 class="font-bold text-slate-900 text-sm">Manifestation</h5>
                <p class="text-xs text-slate-400 mt-1">Conférences, séminaires</p>
            </button>
        </div>
    </div>
</x-modal>

@endsection

@push('scripts')
<script>
function printSubmissionPDF(req) {
    const statusLabels = {pending:'En attente', in_review:'En cours', approved:'Approuée', rejected:'Rejetée'};
    const statusBg    = {approved:'#d1fae5', rejected:'#ffe4e6', in_review:'#fef3c7', pending:'#f1f5f9'};
    const statusColor = {approved:'#065f46', rejected:'#9f1239', in_review:'#92400e', pending:'#475569'};

    const w = window.open('', '_blank');
    w.document.write(
        '<!DOCTYPE html><html><head>' +
        '<meta charset="UTF-8">' +
        '<title>Demande #' + req.id + ' - SciCouncil</title>' +
        '<style>' +
            'body{font-family:Arial,sans-serif;padding:40px;color:#1e293b}' +
            'h1{color:#4f46e5;font-size:20px;margin-bottom:4px}' +
            '.sub{color:#94a3b8;font-size:12px;margin-bottom:20px}' +
            '.meta{background:#f8fafc;padding:20px;border-radius:8px;margin:20px 0;border:1px solid #e2e8f0}' +
            '.row{display:flex;gap:40px;margin-bottom:14px;flex-wrap:wrap}' +
            '.cell{min-width:120px}' +
            '.lbl{font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.05em;margin-bottom:3px}' +
            '.val{font-size:13px;font-weight:600;color:#1e293b}' +
            '.badge{display:inline-block;padding:4px 12px;border-radius:999px;font-size:11px;font-weight:700;' +
                'background:' + statusBg[req.status] + ';color:' + statusColor[req.status] + '}' +
            '.abstract{margin-top:16px;padding:16px;background:#fff;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;line-height:1.6}' +
            '.footer{margin-top:40px;font-size:10px;color:#94a3b8;border-top:1px solid #e2e8f0;padding-top:10px;display:flex;justify-content:space-between}' +
        '</style>' +
        '</head><body>' +
        '<h1>Scientific Council &ndash; Attestation de Soumission</h1>' +
        '<p class="sub">Référence #' + req.id + ' &bull; Généré le ' + new Date().toLocaleDateString('fr-FR') + '</p>' +
        '<div class="meta">' +
            '<div class="row">' +
                '<div class="cell"><div class="lbl">Titre</div><div class="val">' + req.title + '</div></div>' +
                '<div class="cell"><div class="lbl">Statut</div><span class="badge">' + (statusLabels[req.status] || req.status) + '</span></div>' +
            '</div>' +
            '<div class="row">' +
                '<div class="cell"><div class="lbl">Type</div><div class="val">' + req.type + '</div></div>' +
                '<div class="cell"><div class="lbl">Source</div><div class="val">' + req.source + '</div></div>' +
                '<div class="cell"><div class="lbl">ISSN / DOI</div><div class="val">' + req.issn + '</div></div>' +
            '</div>' +
            '<div class="row">' +
                '<div class="cell"><div class="lbl">Date de soumission</div><div class="val">' + req.date + '</div></div>' +
                '<div class="cell"><div class="lbl">Avis reçus</div><div class="val">' + req.reviews + '</div></div>' +
            '</div>' +
            '<div class="abstract"><div class="lbl">Résumé / Abstract</div><p style="margin:6px 0 0">' + req.abstract + '</p></div>' +
        '</div>' +
        '<div class="footer">' +
            '<span>Scientific Council CMS &copy; 2026</span>' +
            '<span>Document officiel &ndash; Ne pas modifier</span>' +
        '</div>' +
        '</body></html>'
    );
    w.document.close();
    setTimeout(function(){ w.print(); }, 400);
}
</script>
@endpush
