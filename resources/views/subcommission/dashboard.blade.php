@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto space-y-8">

    {{-- ── Welcome Banner ─────────────────────────────────── --}}
    <div class="relative overflow-hidden bg-gradient-to-r from-slate-800 via-slate-900 to-indigo-900 rounded-3xl p-8 text-white shadow-xl">
        <div class="absolute -top-8 -right-8 w-48 h-48 bg-white/5 rounded-full"></div>
        <div class="absolute top-4 right-16 w-24 h-24 bg-white/5 rounded-full"></div>
        <div class="relative flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <p class="text-indigo-300 text-sm font-bold uppercase tracking-widest mb-1">
                    <i class="fa-solid fa-building-columns mr-1"></i> Sous-Commission
                </p>
                <h1 class="text-3xl font-extrabold tracking-tight">Pr. Karim Bouziane</h1>
                <p class="text-slate-400 mt-2 text-sm">Président de Sous-Commission — Informatique & IA</p>
            </div>
            <div class="flex gap-3 flex-wrap">
                <a href="/subcommission/requests" onclick="event.preventDefault(); navigateTo('subcommission/requests')"
                    class="flex items-center gap-2 bg-white text-slate-800 font-bold px-5 py-3 rounded-xl hover:bg-indigo-50 transition-all shadow-lg text-sm">
                    <i class="fa-solid fa-inbox"></i> Demandes Reçues
                </a>
                <a href="/subcommission/reviews" onclick="event.preventDefault(); navigateTo('subcommission/reviews')"
                    class="flex items-center gap-2 bg-white/20 text-white font-bold px-5 py-3 rounded-xl hover:bg-white/30 transition-all text-sm border border-white/30">
                    <i class="fa-solid fa-comments"></i> Suivi des Avis
                </a>
            </div>
        </div>
    </div>

    {{-- ── Stat Cards ──────────────────────────────────────── --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-5">
        @php
        $stats = [
            ['label' => 'Nouvelles Demandes',   'value' => '06', 'icon' => 'fa-inbox',           'bg' => 'bg-indigo-50',  'text' => 'text-indigo-600',  'border' => 'border-indigo-200'],
            ['label' => 'En Examen',            'value' => '04', 'icon' => 'fa-magnifying-glass', 'bg' => 'bg-amber-50',   'text' => 'text-amber-600',   'border' => 'border-amber-200'],
            ['label' => 'Avis Complets',        'value' => '03', 'icon' => 'fa-clipboard-check',  'bg' => 'bg-emerald-50', 'text' => 'text-emerald-600', 'border' => 'border-emerald-200'],
            ['label' => 'Prêts à Transmettre',  'value' => '02', 'icon' => 'fa-paper-plane',      'bg' => 'bg-violet-50',  'text' => 'text-violet-600',  'border' => 'border-violet-200'],
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

    {{-- ── Workflow Pipeline ──────────────────────────────── --}}
    <x-card title="Pipeline du Workflow" subtitle="Progression des dossiers à travers les étapes">
        <div class="flex items-center justify-between gap-2 py-4">
            @php
            $steps = [
                ['label' => 'Réception',      'icon' => 'fa-inbox',           'count' => 6, 'color' => 'indigo'],
                ['label' => 'Recevabilité',    'icon' => 'fa-clipboard-check', 'count' => 4, 'color' => 'blue'],
                ['label' => 'Proposition',     'icon' => 'fa-user-plus',       'count' => 4, 'color' => 'cyan'],
                ['label' => 'Avis',            'icon' => 'fa-comments',        'count' => 3, 'color' => 'amber'],
                ['label' => 'Consolidation',   'icon' => 'fa-file-circle-check','count' => 2, 'color' => 'emerald'],
                ['label' => 'Transmission',    'icon' => 'fa-paper-plane',     'count' => 1, 'color' => 'violet'],
            ];
            @endphp

            @foreach($steps as $i => $step)
                <div class="flex-1 flex flex-col items-center gap-2 relative">
                    <div class="w-12 h-12 rounded-2xl bg-{{ $step['color'] }}-100 text-{{ $step['color'] }}-600 flex items-center justify-center shadow-sm">
                        <i class="fa-solid {{ $step['icon'] }} text-lg"></i>
                    </div>
                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider text-center">{{ $step['label'] }}</span>
                    <span class="bg-{{ $step['color'] }}-100 text-{{ $step['color'] }}-700 text-[10px] font-black px-2 py-0.5 rounded-full">{{ $step['count'] }}</span>
                </div>
                @if($i < count($steps) - 1)
                    <div class="flex-shrink-0 w-8 h-0.5 bg-slate-200 mt-[-28px]"></div>
                @endif
            @endforeach
        </div>
    </x-card>

    {{-- ── Recent Dossiers ────────────────────────────────── --}}
    <x-card title="Dossiers Récents" subtitle="Les dernières soumissions reçues">
        <x-slot name="headerAction">
            <a href="/subcommission/requests" onclick="event.preventDefault(); navigateTo('subcommission/requests')"
               class="text-sm font-bold text-indigo-600 hover:text-indigo-800 flex items-center gap-1">
                Voir tout <i class="fa-solid fa-arrow-right text-xs"></i>
            </a>
        </x-slot>

        @php
        $dossiers = [
            ['id' => 'SC-2491', 'title' => 'Deep Learning in Medical Imaging',          'postulant' => 'Dr. A. Benali',      'date' => '2026-04-28', 'status' => 'pending',    'step' => 'Réception'],
            ['id' => 'SC-2490', 'title' => 'Blockchain for Decentralized Identity',     'postulant' => 'Dr. S. Kaci',         'date' => '2026-04-25', 'status' => 'in_review',  'step' => 'Examen (2/3)'],
            ['id' => 'SC-2489', 'title' => 'Ethical Challenges in Modern AI',            'postulant' => 'Pr. M. Boudiaf',      'date' => '2026-04-20', 'status' => 'in_review',  'step' => 'Examen (3/3)'],
            ['id' => 'SC-2488', 'title' => 'Quantum Computing Optimization',             'postulant' => 'Dr. H. Moussa',       'date' => '2026-04-15', 'status' => 'approved',   'step' => 'Transmis'],
        ];
        @endphp

        <x-table :headers="['Réf.', 'Titre', 'Postulant', 'Date', 'Étape', 'Statut', 'Actions']">
            @foreach($dossiers as $d)
            <tr class="hover:bg-slate-50 transition-colors">
                <td class="px-6 py-4">
                    <span class="text-[10px] font-black text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded tracking-tighter uppercase">{{ $d['id'] }}</span>
                </td>
                <td class="px-6 py-4 font-bold text-slate-800 max-w-[240px] truncate">{{ $d['title'] }}</td>
                <td class="px-6 py-4">
                    <div class="flex items-center gap-2">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($d['postulant']) }}&size=24&bg=6366f1&color=fff" class="w-6 h-6 rounded-full" alt="">
                        <span class="text-sm text-slate-600">{{ $d['postulant'] }}</span>
                    </div>
                </td>
                <td class="px-6 py-4 text-slate-500 text-sm italic">{{ $d['date'] }}</td>
                <td class="px-6 py-4">
                    <span class="text-xs font-bold text-slate-600 bg-slate-100 px-2 py-1 rounded-lg">{{ $d['step'] }}</span>
                </td>
                <td class="px-6 py-4"><x-badge :status="$d['status']" /></td>
                <td class="px-6 py-4">
                    @if($d['status'] === 'pending')
                        <a href="/subcommission/requests" onclick="event.preventDefault(); navigateTo('subcommission/requests')"
                           class="text-indigo-600 hover:text-indigo-800 text-xs font-bold flex items-center gap-1">
                            <i class="fa-solid fa-clipboard-check"></i> Contrôler
                        </a>
                    @elseif($d['step'] === 'Examen (3/3)')
                        <a href="/subcommission/consolidate/2489" onclick="event.preventDefault(); navigateTo('subcommission/consolidate/2489')"
                           class="text-emerald-600 hover:text-emerald-800 text-xs font-bold flex items-center gap-1">
                            <i class="fa-solid fa-file-circle-check"></i> Consolider
                        </a>
                    @else
                        <span class="text-xs text-slate-400 italic">—</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </x-table>
    </x-card>

</div>
@endsection
