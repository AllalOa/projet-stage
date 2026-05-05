@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto space-y-8">

    {{-- Welcome Banner --}}
    <div class="relative overflow-hidden bg-gradient-to-r from-amber-700 via-amber-800 to-yellow-900 rounded-3xl p-8 text-white shadow-xl">
        <div class="absolute -top-8 -right-8 w-48 h-48 bg-white/5 rounded-full"></div>
        <div class="absolute top-4 right-16 w-24 h-24 bg-white/5 rounded-full"></div>
        <div class="relative flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <p class="text-amber-300 text-sm font-bold uppercase tracking-widest mb-1">
                    <i class="fa-solid fa-landmark mr-1"></i> Conseil Scientifique
                </p>
                <h1 class="text-3xl font-extrabold tracking-tight">{{ $personnel->grade ?? 'Pr.' }} {{ $personnel->prenom }} {{ $personnel->nom }}</h1>
                <p class="text-amber-300/70 mt-2 text-sm">Président du Conseil Scientifique — {{ $personnel->departement ?? 'Université' }}</p>
            </div>
            <div class="flex gap-3 flex-wrap">
                <a href="/president/dossiers" onclick="event.preventDefault(); navigateTo('president/dossiers')"
                    class="flex items-center gap-2 bg-white text-amber-800 font-bold px-5 py-3 rounded-xl hover:bg-amber-50 transition-all shadow-lg text-sm">
                    <i class="fa-solid fa-folder-open"></i> Dossiers Reçus
                </a>
                <a href="/president/commissions" onclick="event.preventDefault(); navigateTo('president/commissions')"
                    class="flex items-center gap-2 bg-white/20 text-white font-bold px-5 py-3 rounded-xl hover:bg-white/30 transition-all text-sm border border-white/30">
                    <i class="fa-solid fa-sitemap"></i> Sous-Commissions
                </a>
            </div>
        </div>
    </div>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-5">
        @php
        $statCards = [
            ['label' => 'Dossiers Transmis',    'value' => str_pad($stats['transmis'], 2, '0', STR_PAD_LEFT),   'icon' => 'fa-folder-open',    'bg' => 'bg-amber-50',   'text' => 'text-amber-600',   'border' => 'border-amber-200'],
            ['label' => 'En Délibération',      'value' => str_pad($stats['en_cours'], 2, '0', STR_PAD_LEFT),   'icon' => 'fa-scale-balanced', 'bg' => 'bg-indigo-50',  'text' => 'text-indigo-600',  'border' => 'border-indigo-200'],
            ['label' => 'Avis Favorables',      'value' => str_pad($stats['favorables'], 2, '0', STR_PAD_LEFT), 'icon' => 'fa-check-double',   'bg' => 'bg-emerald-50', 'text' => 'text-emerald-600', 'border' => 'border-emerald-200'],
            ['label' => 'Total Dossiers',       'value' => str_pad($stats['total'], 2, '0', STR_PAD_LEFT),      'icon' => 'fa-layer-group',    'bg' => 'bg-violet-50',  'text' => 'text-violet-600',  'border' => 'border-violet-200'],
        ];
        @endphp
        @foreach($statCards as $s)
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

    {{-- Recent Dossiers --}}
    <x-card title="Dossiers Récents" subtitle="Dossiers transmis par les sous-commissions">
        <x-slot name="headerAction">
            <a href="/president/dossiers" onclick="event.preventDefault(); navigateTo('president/dossiers')" class="text-sm font-bold text-amber-600 hover:text-amber-800 flex items-center gap-1">
                Voir tout <i class="fa-solid fa-arrow-right text-xs"></i>
            </a>
        </x-slot>

        @if($dernieresDemandes->isEmpty())
            <div class="text-center py-12 text-slate-400">
                <i class="fa-solid fa-folder-open text-4xl mb-3 block"></i>
                <p class="font-bold">Aucun dossier transmis pour l'instant.</p>
            </div>
        @else
        <x-table :headers="['Réf.', 'Titre', 'Postulant', 'Commission', 'Avis SC', 'Statut', 'Actions']">
            @foreach($dernieresDemandes as $d)
            <tr class="hover:bg-slate-50 transition-colors">
                <td class="px-6 py-4"><span class="text-[10px] font-black text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded tracking-tighter uppercase">REQ-{{ str_pad($d->id, 4, '0', STR_PAD_LEFT) }}</span></td>
                <td class="px-6 py-4 font-bold text-slate-800 max-w-[220px] truncate">{{ $d->publication?->titre ?? 'Sans titre' }}</td>
                <td class="px-6 py-4 text-sm text-slate-600">{{ $d->postulant->prenom }} {{ $d->postulant->nom }}</td>
                <td class="px-6 py-4"><span class="text-xs font-bold bg-violet-100 text-violet-700 px-2 py-1 rounded-lg">{{ $d->sousCommission?->nom ?? '—' }}</span></td>
                <td class="px-6 py-4">
                    @php
                        $dc = [
                            'favorable'=>'bg-emerald-50 text-emerald-700 border-emerald-200',
                            'reserve'=>'bg-amber-50 text-amber-700 border-amber-200',
                            'defavorable'=>'bg-rose-50 text-rose-700 border-rose-200',
                            '' => 'bg-slate-50 text-slate-500 border-slate-200'
                        ];
                        $decision = $d->decision_finale ?? '';
                    @endphp
                    @if($decision)
                        <span class="text-xs font-bold px-2.5 py-0.5 rounded-full border {{ $dc[$decision] }}">{{ ucfirst($decision) }}</span>
                    @else
                        <span class="text-xs font-bold px-2.5 py-0.5 rounded-full border bg-slate-50 text-slate-500 border-slate-200">En attente</span>
                    @endif
                </td>
                <td class="px-6 py-4"><x-badge :status="$d->statut" /></td>
                <td class="px-6 py-4">
                    <a href="{{ route('president.dossier.detail', $d->id) }}" class="text-amber-600 hover:text-amber-800 text-xs font-bold flex items-center gap-1">
                        <i class="fa-solid fa-gavel"></i> Délibérer
                    </a>
                </td>
            </tr>
            @endforeach
        </x-table>
        @endif
    </x-card>

</div>
@endsection
