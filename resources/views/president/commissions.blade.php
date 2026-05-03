@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto space-y-6" x-data="{
    showCreateModal: false,
    showDetailModal: false,
    selectedCommission: null,
    newCommission: { name: '', specialty: '', chef: '', members: [] },
    availableMembers: [
        { id: 1,  name: 'Pr. Karim Bouziane',       specialty: 'Intelligence Artificielle', grade: 'Professeur' },
        { id: 2,  name: 'Pr. Hamid El Moussaoui',    specialty: 'Intelligence Artificielle', grade: 'Professeur' },
        { id: 3,  name: 'Dr. Kamel Ait Ouali',       specialty: 'Machine Learning',          grade: 'MCA' },
        { id: 4,  name: 'Pr. Nadia Bensalem',        specialty: 'Traitement d\'Images',      grade: 'Professeur' },
        { id: 5,  name: 'Pr. Fatima Benbouzid',      specialty: 'Intelligence Artificielle', grade: 'Professeur' },
        { id: 6,  name: 'Dr. Rachid Boudour',        specialty: 'Deep Learning',             grade: 'MCA' },
        { id: 7,  name: 'Pr. Amel Zenati',           specialty: 'Traitement d\'Images',      grade: 'Professeur' },
        { id: 8,  name: 'Dr. Mohamed Cheriet',        specialty: 'Vision par Ordinateur',     grade: 'MCA' },
        { id: 9,  name: 'Pr. Leila Hamdad',          specialty: 'Data Science',              grade: 'Professeur' },
        { id: 10, name: 'Dr. Youssef Amrani',        specialty: 'Réseaux',                   grade: 'MCA' },
        { id: 11, name: 'Pr. Lotfi Boualem',         specialty: 'Télécommunications',        grade: 'Professeur' },
        { id: 12, name: 'Dr. Amira Bekkouche',       specialty: 'Sécurité Informatique',     grade: 'MCA' },
    ],
    commissions: [
        { id: 1, name: 'Informatique & IA', specialty: 'Intelligence Artificielle, Machine Learning, Deep Learning', chef: { id: 1, name: 'Pr. Karim Bouziane' }, members: [
            { id: 2, name: 'Pr. Hamid El Moussaoui' },{ id: 3, name: 'Dr. Kamel Ait Ouali' },{ id: 5, name: 'Pr. Fatima Benbouzid' },{ id: 9, name: 'Pr. Leila Hamdad' }
        ], dossiers: 4, active: true },
        { id: 2, name: 'Traitement du Signal', specialty: 'Traitement d\'Images, Vision par Ordinateur', chef: { id: 4, name: 'Pr. Nadia Bensalem' }, members: [
            { id: 7, name: 'Pr. Amel Zenati' },{ id: 8, name: 'Dr. Mohamed Cheriet' }
        ], dossiers: 2, active: true },
        { id: 3, name: 'Télécommunications', specialty: 'Réseaux, Sécurité, Télécoms', chef: { id: 11, name: 'Pr. Lotfi Boualem' }, members: [
            { id: 10, name: 'Dr. Youssef Amrani' },{ id: 12, name: 'Dr. Amira Bekkouche' }
        ], dossiers: 1, active: true },
    ],
    toggleMember(memberId) {
        const idx = this.newCommission.members.indexOf(memberId);
        if (idx > -1) { this.newCommission.members.splice(idx, 1); if (this.newCommission.chef == memberId) this.newCommission.chef = ''; }
        else { this.newCommission.members.push(memberId); }
    },
    isMemberSelected(id) { return this.newCommission.members.includes(id); },
    getMemberName(id) { const m = this.availableMembers.find(m => m.id === id); return m ? m.name : ''; },
    createCommission() {
        if (!this.newCommission.name || !this.newCommission.chef || this.newCommission.members.length < 2) return;
        const chef = this.availableMembers.find(m => m.id == this.newCommission.chef);
        const members = this.newCommission.members.filter(id => id != this.newCommission.chef).map(id => { const m = this.availableMembers.find(x => x.id === id); return { id: m.id, name: m.name }; });
        this.commissions.push({
            id: this.commissions.length + 1, name: this.newCommission.name, specialty: this.newCommission.specialty,
            chef: { id: chef.id, name: chef.name }, members: members, dossiers: 0, active: true
        });
        this.showCreateModal = false;
        this.newCommission = { name: '', specialty: '', chef: '', members: [] };
        showToast('Sous-commission créée avec succès !');
    },
    viewDetail(c) { this.selectedCommission = c; this.showDetailModal = true; }
}">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Sous-Commissions</h1>
            <p class="text-slate-500 text-sm mt-1">Gérer les sous-commissions et leurs membres</p>
        </div>
        <button @click="showCreateModal = true" class="inline-flex items-center gap-2 px-5 py-3 text-sm font-bold bg-amber-600 text-white rounded-xl hover:bg-amber-700 transition-all shadow-lg shadow-amber-200">
            <i class="fa-solid fa-plus"></i> Nouvelle Sous-Commission
        </button>
    </div>

    {{-- Commission Cards --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
        <template x-for="c in commissions" :key="c.id">
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm hover:shadow-md transition-all overflow-hidden">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-extrabold text-slate-900" x-text="c.name"></h3>
                        <span class="text-[10px] font-bold px-2 py-1 rounded-full bg-emerald-100 text-emerald-700">Active</span>
                    </div>
                    <p class="text-xs text-slate-400 mb-5" x-text="c.specialty"></p>

                    {{-- Chef --}}
                    <div class="mb-4">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Président</p>
                        <div class="flex items-center gap-3 bg-amber-50 border border-amber-200 rounded-xl p-3">
                            <img :src="'https://ui-avatars.com/api/?name='+encodeURIComponent(c.chef.name)+'&bg=d97706&color=fff&size=36'" class="w-9 h-9 rounded-lg" alt="">
                            <div>
                                <p class="text-sm font-bold text-slate-800" x-text="c.chef.name"></p>
                                <p class="text-[10px] text-amber-600 font-bold">👑 Chef de commission</p>
                            </div>
                        </div>
                    </div>

                    {{-- Members --}}
                    <div class="mb-4">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Membres (<span x-text="c.members.length"></span>)</p>
                        <div class="flex flex-wrap gap-2">
                            <template x-for="m in c.members" :key="m.id">
                                <div class="flex items-center gap-2 bg-slate-50 border border-slate-200 rounded-lg px-3 py-1.5">
                                    <img :src="'https://ui-avatars.com/api/?name='+encodeURIComponent(m.name)+'&background=random&size=24'" class="w-6 h-6 rounded-md" alt="">
                                    <span class="text-xs font-bold text-slate-600" x-text="m.name"></span>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- Stats --}}
                    <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                        <span class="text-xs text-slate-400"><i class="fa-solid fa-folder mr-1"></i> <span x-text="c.dossiers"></span> dossier(s) assignés</span>
                        <button @click="viewDetail(c)" class="text-xs font-bold text-amber-600 hover:text-amber-800 flex items-center gap-1">
                            <i class="fa-solid fa-eye"></i> Détails
                        </button>
                    </div>
                </div>
            </div>
        </template>
    </div>

    {{-- ══ MODAL: Créer Sous-Commission ══ --}}
    <div x-show="showCreateModal" x-cloak class="fixed inset-0 z-[60] flex items-center justify-center p-4"
         x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" @click="showCreateModal = false"></div>
        <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-2xl max-h-[85vh] overflow-y-auto">
            <div class="p-8 space-y-5">
                <div class="text-center">
                    <div class="w-14 h-14 bg-amber-100 rounded-2xl flex items-center justify-center mx-auto mb-4"><i class="fa-solid fa-sitemap text-amber-600 text-xl"></i></div>
                    <h3 class="text-xl font-bold text-slate-900">Nouvelle Sous-Commission</h3>
                    <p class="text-sm text-slate-500 mt-1">Créez une sous-commission et assignez les membres</p>
                </div>

                {{-- Name & Specialty --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Nom <span class="text-rose-500">*</span></label>
                        <input type="text" x-model="newCommission.name" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-amber-100 focus:border-amber-500 outline-none text-sm" placeholder="Ex: Bioinformatique">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Spécialité</label>
                        <input type="text" x-model="newCommission.specialty" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-amber-100 focus:border-amber-500 outline-none text-sm" placeholder="Ex: Bio, Génomique">
                    </div>
                </div>

                {{-- Select Members --}}
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Sélectionner les Membres <span class="text-rose-500">*</span> <span class="text-xs font-normal text-slate-400">(min. 2)</span></label>
                    <div class="space-y-2 max-h-[220px] overflow-y-auto border border-slate-200 rounded-xl p-3">
                        <template x-for="m in availableMembers" :key="m.id">
                            <div @click="toggleMember(m.id)" class="flex items-center justify-between p-3 rounded-xl cursor-pointer transition-all"
                                :class="isMemberSelected(m.id) ? 'bg-amber-50 border border-amber-300' : 'hover:bg-slate-50 border border-transparent'">
                                <div class="flex items-center gap-3">
                                    <img :src="'https://ui-avatars.com/api/?name='+encodeURIComponent(m.name)+'&background=random&size=32'" class="w-8 h-8 rounded-lg" alt="">
                                    <div>
                                        <p class="text-sm font-bold text-slate-800" x-text="m.name"></p>
                                        <p class="text-[10px] text-slate-400" x-text="m.specialty + ' · ' + m.grade"></p>
                                    </div>
                                </div>
                                <div class="w-5 h-5 rounded flex items-center justify-center" :class="isMemberSelected(m.id)?'bg-amber-600 text-white':'border-2 border-slate-300'">
                                    <i class="fa-solid fa-check text-[10px]" x-show="isMemberSelected(m.id)"></i>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Select Chef --}}
                <div x-show="newCommission.members.length >= 1">
                    <label class="block text-sm font-bold text-slate-700 mb-2">Désigner le Chef <span class="text-rose-500">*</span></label>
                    <select x-model="newCommission.chef" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-amber-100 text-sm font-bold text-slate-600">
                        <option value="">— Choisir parmi les membres sélectionnés —</option>
                        <template x-for="id in newCommission.members" :key="id">
                            <option :value="id" x-text="getMemberName(id)"></option>
                        </template>
                    </select>
                </div>

                {{-- Actions --}}
                <div class="flex gap-3 justify-end pt-4 border-t border-slate-100">
                    <button @click="showCreateModal = false" class="px-5 py-2.5 text-sm font-bold bg-white text-slate-700 border border-slate-200 hover:bg-slate-50 rounded-xl transition-all">Annuler</button>
                    <button @click="createCommission()"
                        :class="newCommission.name && newCommission.chef && newCommission.members.length >= 2 ? 'bg-amber-600 hover:bg-amber-700 text-white shadow-lg shadow-amber-200' : 'bg-slate-100 text-slate-400 cursor-not-allowed'"
                        class="px-6 py-2.5 text-sm font-bold rounded-xl transition-all flex items-center gap-2">
                        <i class="fa-solid fa-plus"></i> Créer
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ══ MODAL: Détail Commission ══ --}}
    <div x-show="showDetailModal" x-cloak class="fixed inset-0 z-[60] flex items-center justify-center p-4"
         x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" @click="showDetailModal = false"></div>
        <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-lg">
            <template x-if="selectedCommission">
                <div class="p-8 space-y-5">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-bold text-slate-900" x-text="selectedCommission.name"></h3>
                        <button @click="showDetailModal = false" class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center hover:bg-slate-200 transition-all"><i class="fa-solid fa-xmark text-slate-400"></i></button>
                    </div>
                    <p class="text-xs text-slate-400" x-text="selectedCommission.specialty"></p>

                    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 flex items-center gap-3">
                        <img :src="'https://ui-avatars.com/api/?name='+encodeURIComponent(selectedCommission.chef.name)+'&bg=d97706&color=fff&size=40'" class="w-10 h-10 rounded-lg" alt="">
                        <div>
                            <p class="font-bold text-slate-800" x-text="selectedCommission.chef.name"></p>
                            <p class="text-xs text-amber-600 font-bold">👑 Président de la sous-commission</p>
                        </div>
                    </div>

                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Membres</p>
                        <div class="space-y-2">
                            <template x-for="m in selectedCommission.members" :key="m.id">
                                <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl">
                                    <img :src="'https://ui-avatars.com/api/?name='+encodeURIComponent(m.name)+'&background=random&size=32'" class="w-8 h-8 rounded-lg" alt="">
                                    <span class="text-sm font-bold text-slate-700" x-text="m.name"></span>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 pt-4 border-t border-slate-100 text-sm text-slate-500">
                        <span><i class="fa-solid fa-folder mr-1"></i> <span x-text="selectedCommission.dossiers"></span> dossier(s)</span>
                        <span class="text-emerald-600 font-bold"><i class="fa-solid fa-circle text-[6px] mr-1"></i> Active</span>
                    </div>
                </div>
            </template>
        </div>
    </div>

</div>
@endsection
