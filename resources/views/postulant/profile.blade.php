@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-8" x-data="{
    saved: false,
    profile: {
        firstName: 'Ahmed',
        lastName:  'Benali',
        email:     'ahmed.benali@univ.dz',
        phone:     '+213 550 123 456',
        grade:     'Maître de Conférences A',
        university:'Université des Sciences et de la Technologie',
        department:'Département Informatique',
        speciality:'Intelligence Artificielle & Vision par Ordinateur',
        orcid:     '0000-0002-1234-5678',
        scholar:   'https://scholar.google.com/citations?user=example',
        bio:       'Chercheur en IA appliquée à l\'imagerie médicale, avec 12 ans d\'expérience académique.'
    },
    save() {
        this.saved = true;
        showToast('Profil enregistré avec succès !', 'success');
        setTimeout(() => this.saved = false, 3000);
    }
}">

    {{-- ── Page Header ────────────────────────────────── --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900">Mon Profil</h1>
            <p class="text-slate-500 text-sm mt-1">Informations personnelles et académiques</p>
        </div>
        <button @click="save()"
            class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-5 py-3 rounded-xl transition-all shadow-lg shadow-indigo-200 text-sm"
            :class="saved ? 'bg-emerald-600 hover:bg-emerald-600 shadow-emerald-200' : ''">
            <i :class="saved ? 'fa-solid fa-check' : 'fa-solid fa-floppy-disk'"></i>
            <span x-text="saved ? 'Enregistré !' : 'Enregistrer'"></span>
        </button>
    </div>

    {{-- ── Avatar Card ─────────────────────────────────── --}}
    <div class="bg-white border border-slate-200 rounded-3xl shadow-sm overflow-hidden">
        <div class="h-24 bg-gradient-to-r from-indigo-600 to-violet-600"></div>
        <div class="px-8 pb-8">
            <div class="flex items-end gap-5 -mt-10 mb-6">
                {{-- Avatar --}}
                <div class="w-20 h-20 rounded-2xl bg-indigo-700 border-4 border-white shadow-xl flex items-center justify-center text-white text-2xl font-black">
                    <span x-text="profile.firstName.charAt(0) + profile.lastName.charAt(0)">AB</span>
                </div>
                <div class="pb-1">
                    <h2 class="text-xl font-black text-slate-900" x-text="profile.firstName + ' ' + profile.lastName">Ahmed Benali</h2>
                    <p class="text-sm text-slate-500" x-text="profile.grade">Maître de Conférences A</p>
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
                        <input type="text" x-model="profile.firstName"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 outline-none text-slate-800 font-semibold transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Nom</label>
                        <input type="text" x-model="profile.lastName"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 outline-none text-slate-800 font-semibold transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Email</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-4 flex items-center text-slate-400"><i class="fa-regular fa-envelope"></i></span>
                            <input type="email" x-model="profile.email"
                                class="w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 outline-none text-slate-800 font-semibold transition-all">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Téléphone</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-4 flex items-center text-slate-400"><i class="fa-solid fa-phone"></i></span>
                            <input type="tel" x-model="profile.phone"
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
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Établissement</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-4 flex items-center text-slate-400"><i class="fa-solid fa-building-columns"></i></span>
                            <input type="text" x-model="profile.university"
                                class="w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 outline-none text-slate-800 font-semibold transition-all">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Département / Faculté</label>
                        <input type="text" x-model="profile.department"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 outline-none text-slate-800 font-semibold transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Grade</label>
                        <select x-model="profile.grade"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 outline-none text-slate-800 font-semibold transition-all">
                            <option>Professeur</option>
                            <option>Maître de Conférences A</option>
                            <option>Maître de Conférences B</option>
                            <option>Maître Assistant A</option>
                            <option>Maître Assistant B</option>
                            <option>Doctorant</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Spécialité</label>
                        <input type="text" x-model="profile.speciality"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 outline-none text-slate-800 font-semibold transition-all">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Bio / Présentation</label>
                        <textarea rows="3" x-model="profile.bio"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 outline-none resize-none text-slate-800 font-semibold transition-all"></textarea>
                    </div>
                </div>
            </div>

            {{-- Liens Scientifiques --}}
            <div class="pt-6 border-t border-slate-100">
                <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-5 flex items-center gap-2">
                    <i class="fa-solid fa-link text-indigo-500"></i> Identifiants Scientifiques
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">ORCID iD</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-4 flex items-center text-emerald-500 font-black text-xs">ID</span>
                            <input type="text" x-model="profile.orcid" placeholder="0000-0000-0000-0000"
                                class="w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 outline-none text-slate-800 font-semibold transition-all font-mono">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Google Scholar</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-4 flex items-center text-slate-400"><i class="fa-brands fa-google text-sm"></i></span>
                            <input type="url" x-model="profile.scholar" placeholder="https://scholar.google.com/..."
                                class="w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 outline-none text-slate-800 font-semibold transition-all">
                        </div>
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
                    <p class="text-xs text-slate-500">Dernière modification il y a 30 jours</p>
                </div>
            </div>
            <button @click="changing = !changing"
                class="text-sm font-bold text-rose-600 hover:text-rose-800 px-4 py-2 rounded-xl hover:bg-rose-50 transition-all">
                Changer le mot de passe
            </button>
        </div>
        <div x-show="changing" x-transition class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4 pt-6 border-t border-slate-100">
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Mot de passe actuel</label>
                <input type="password" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-rose-100 focus:border-rose-400 outline-none transition-all">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Nouveau mot de passe</label>
                <input type="password" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-rose-100 focus:border-rose-400 outline-none transition-all">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Confirmer</label>
                <input type="password" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-rose-100 focus:border-rose-400 outline-none transition-all">
            </div>
            <div class="md:col-span-3 flex justify-end">
                <button @click="changing=false; showToast('Mot de passe mis à jour !', 'success')"
                    class="bg-rose-600 hover:bg-rose-700 text-white font-bold px-5 py-2.5 rounded-xl text-sm transition-all shadow-lg shadow-rose-200">
                    Mettre à jour
                </button>
            </div>
        </div>
    </div>

</div>
@endsection
