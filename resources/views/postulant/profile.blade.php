@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-8">

    @if ($errors->any())
    <div class="bg-rose-50 border border-rose-200 rounded-xl p-4 flex items-start gap-3">
        <i class="fa-solid fa-circle-exclamation text-rose-500 mt-0.5"></i>
        <div>
            @foreach ($errors->all() as $error)
                <p class="text-sm text-rose-700 font-medium">{{ $error }}</p>
            @endforeach
        </div>
    </div>
    @endif

    @if (session('success'))
    <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 text-sm text-emerald-700 font-medium flex items-center gap-2">
        <i class="fa-solid fa-circle-check text-emerald-500"></i> {{ session('success') }}
    </div>
    @endif

    <form method="POST" action="{{ route('postulant.profile.update') }}">
        @csrf

        {{-- ── Page Header ────────────────────────────────── --}}
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-2xl font-extrabold text-slate-900">Mon Profil</h1>
                <p class="text-slate-500 text-sm mt-1">Informations personnelles et académiques</p>
            </div>
            <button type="submit"
                class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-5 py-3 rounded-xl transition-all shadow-lg shadow-indigo-200 text-sm">
                <i class="fa-solid fa-floppy-disk"></i>
                <span>Enregistrer</span>
            </button>
        </div>

    {{-- ── Avatar Card ─────────────────────────────────── --}}
    <div class="bg-white border border-slate-200 rounded-3xl shadow-sm overflow-hidden">
        <div class="h-24 bg-gradient-to-r from-indigo-600 to-violet-600"></div>
        <div class="px-8 pb-8">
            <div class="flex items-end gap-5 -mt-10 mb-6">
                {{-- Avatar --}}
                <div class="w-20 h-20 rounded-2xl bg-indigo-700 border-4 border-white shadow-xl flex items-center justify-center text-white text-2xl font-black uppercase">
                    <span>{{ substr($personnel->prenom, 0, 1) . substr($personnel->nom, 0, 1) }}</span>
                </div>
                <div class="pb-1">
                    <h2 class="text-xl font-black text-slate-900">{{ $personnel->prenom }} {{ $personnel->nom }}</h2>
                    <p class="text-sm text-slate-500">{{ $personnel->grade ?? 'Aucun grade' }}</p>
                </div>
                <div class="ml-auto pb-1">
                    <span class="bg-emerald-100 text-emerald-700 text-xs font-bold px-3 py-1.5 rounded-full flex items-center gap-1.5">
                        <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span> Profil actif
                    </span>
                </div>
            </div>

            {{-- Informations Personnelles --}}
            <div class="mb-8">
                <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-5 flex items-center gap-2">
                    <i class="fa-solid fa-user text-indigo-500"></i> Informations Personnelles
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Prénom</label>
                        <input type="text" name="prenom" value="{{ old('prenom', $personnel->prenom) }}" required
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 outline-none text-slate-800 font-semibold transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Nom</label>
                        <input type="text" name="nom" value="{{ old('nom', $personnel->nom) }}" required
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 outline-none text-slate-800 font-semibold transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Email</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-4 flex items-center text-slate-400"><i class="fa-regular fa-envelope"></i></span>
                            <input type="email" value="{{ $personnel->email }}" disabled
                                class="w-full pl-11 pr-4 py-3 bg-slate-100 border border-slate-200 rounded-xl outline-none text-slate-500 font-semibold transition-all cursor-not-allowed" title="L'email ne peut pas être modifié">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Téléphone</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-4 flex items-center text-slate-400"><i class="fa-solid fa-phone"></i></span>
                            <input type="tel" name="phone" value="{{ old('phone', $personnel->phone) }}"
                                class="w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 outline-none text-slate-800 font-semibold transition-all">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Informations Académiques --}}
            <div class="mb-8 pt-6 border-t border-slate-100">
                <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-5 flex items-center gap-2">
                    <i class="fa-solid fa-graduation-cap text-indigo-500"></i> Informations Académiques
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Laboratoire de Recherche</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-4 flex items-center text-slate-400"><i class="fa-solid fa-flask"></i></span>
                            <input type="text" name="laboratoire" value="{{ old('laboratoire', $postulant->laboratoire ?? '') }}"
                                class="w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 outline-none text-slate-800 font-semibold transition-all">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Département / Faculté</label>
                        <input type="text" name="departement" value="{{ old('departement', $personnel->departement) }}"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 outline-none text-slate-800 font-semibold transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Grade</label>
                        <select name="grade"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 outline-none text-slate-800 font-semibold transition-all">
                            @foreach(['Professeur', 'Maître de Conférences A', 'Maître de Conférences B', 'Maître Assistant A', 'Maître Assistant B', 'Doctorant'] as $g)
                                <option value="{{ $g }}" {{ old('grade', $personnel->grade) == $g ? 'selected' : '' }}>{{ $g }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Grade de Recherche</label>
                        <input type="text" name="grade_recherche" value="{{ old('grade_recherche', $postulant->grade_recherche ?? '') }}"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 outline-none text-slate-800 font-semibold transition-all">
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Changer Mot de Passe ────────────────────────── --}}
    <div class="bg-white border border-slate-200 rounded-3xl shadow-sm p-8" x-data="{changing: false}">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-rose-50 rounded-xl flex items-center justify-center">
                    <i class="fa-solid fa-lock text-rose-500"></i>
                </div>
                <div>
                    <h3 class="font-bold text-slate-900">Sécurité du compte</h3>
                    <p class="text-xs text-slate-500">Facultatif</p>
                </div>
            </div>
            <button type="button" @click="changing = !changing"
                class="text-sm font-bold text-rose-600 hover:text-rose-800 px-4 py-2 rounded-xl hover:bg-rose-50 transition-all">
                Changer le mot de passe
            </button>
        </div>
        <div x-show="changing" x-transition class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4 pt-6 border-t border-slate-100">
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Nouveau mot de passe</label>
                <input type="password" name="password" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-rose-100 focus:border-rose-400 outline-none transition-all">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Confirmer</label>
                <input type="password" name="password_confirmation" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-rose-100 focus:border-rose-400 outline-none transition-all">
            </div>
        </div>
    </div>

    </form>
</div>
@endsection
