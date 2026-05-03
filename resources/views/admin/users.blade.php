@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto space-y-6" x-data="usersAdmin()">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Gestion des Utilisateurs</h1>
            <p class="text-slate-500 text-sm mt-1">Créer, modifier et supprimer les comptes de tous les rôles</p>
        </div>
        <button @click="openCreate()" class="inline-flex items-center gap-2 px-5 py-3 text-sm font-bold bg-slate-800 text-white rounded-xl hover:bg-slate-900 transition-all shadow-lg">
            <i class="fa-solid fa-user-plus"></i> Nouveau Compte
        </button>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <template x-for="[role, label, icon, bg, text] in [
            ['all','Total','fa-users','bg-slate-100','text-slate-600'],
            ['postulant','Postulants','fa-user-graduate','bg-blue-50','text-blue-600'],
            ['examiner','Examinateurs','fa-microscope','bg-teal-50','text-teal-600'],
            ['president-sub','Prés. Sous-Comm.','fa-landmark','bg-indigo-50','text-indigo-600'],
            ['president-council','Prés. Conseil','fa-gavel','bg-amber-50','text-amber-600'],
        ]" :key="role">
            <button @click="filterRole = role" class="bg-white border rounded-xl p-4 text-left hover:shadow-md transition-all"
                :class="filterRole === role ? 'border-slate-800 shadow-md' : 'border-slate-200'">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center mb-2" :class="bg">
                    <i class="fa-solid text-sm" :class="icon + ' ' + text"></i>
                </div>
                <p class="text-xl font-black text-slate-900" x-text="role==='all' ? users.length : users.filter(u=>u.role===role).length"></p>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest" x-text="label"></p>
            </button>
        </template>
    </div>

    {{-- Search --}}
    <div class="flex flex-col md:flex-row gap-3">
        <div class="relative flex-1">
            <span class="absolute inset-y-0 left-4 flex items-center text-slate-400"><i class="fa-solid fa-magnifying-glass"></i></span>
            <input type="text" x-model="search" placeholder="Rechercher par nom, email, téléphone..." class="w-full pl-12 pr-4 py-3 bg-white border border-slate-200 rounded-xl outline-none focus:ring-4 focus:ring-slate-100 focus:border-slate-400 transition-all text-sm">
        </div>
    </div>

    {{-- Users Table --}}
    <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white">
        <table class="w-full text-left border-collapse">
            <thead class="bg-slate-50/50 border-b border-slate-200">
                <tr>
                    <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest">Utilisateur</th>
                    <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest">Contact</th>
                    <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest">Rôle</th>
                    <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest">Statut</th>
                    <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest">Créé le</th>
                    <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <template x-for="user in filteredUsers" :key="user.id">
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <img :src="'https://ui-avatars.com/api/?name='+encodeURIComponent(user.name)+'&background=random&size=36'" class="w-9 h-9 rounded-xl" alt="">
                                <div>
                                    <p class="font-bold text-slate-800 text-sm" x-text="user.name"></p>
                                    <p class="text-[10px] text-slate-400" x-text="user.dept || '—'"></p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <p class="text-sm text-slate-600" x-text="user.email"></p>
                            <p class="text-xs text-slate-400" x-text="user.phone || '—'"></p>
                        </td>
                        <td class="px-5 py-4">
                            <span class="text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider"
                                :class="roleClass(user.role)" x-text="roleLabel(user.role)"></span>
                        </td>
                        <td class="px-5 py-4">
                            <span class="inline-flex items-center text-xs font-bold gap-1.5"
                                :class="user.active ? 'text-emerald-600' : 'text-slate-400'">
                                <span class="w-2 h-2 rounded-full" :class="user.active ? 'bg-emerald-500' : 'bg-slate-300'"></span>
                                <span x-text="user.active ? 'Actif' : 'Inactif'"></span>
                            </span>
                        </td>
                        <td class="px-5 py-4 text-sm text-slate-500 italic" x-text="user.created"></td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-2 justify-end">
                                <button @click="openEdit(user)" class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center hover:bg-indigo-100 transition-all" title="Modifier">
                                    <i class="fa-solid fa-pen text-xs"></i>
                                </button>
                                <button @click="confirmDelete(user)" class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center hover:bg-rose-100 transition-all" title="Supprimer">
                                    <i class="fa-solid fa-trash text-xs"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>

    {{-- ══ MODAL: Create / Edit ══ --}}
    <div x-show="showFormModal" x-cloak class="fixed inset-0 z-[60] flex items-center justify-center p-4"
         x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" @click="showFormModal = false"></div>
        <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-lg max-h-[85vh] overflow-y-auto">
            <div class="p-8 space-y-5">
                <div class="text-center">
                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center mx-auto mb-4"
                        :class="editMode ? 'bg-indigo-100' : 'bg-slate-800'">
                        <i class="fa-solid text-xl" :class="editMode ? 'fa-pen text-indigo-600' : 'fa-user-plus text-white'"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900" x-text="editMode ? 'Modifier l\'Utilisateur' : 'Nouveau Compte'"></h3>
                </div>

                {{-- Name --}}
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Nom complet <span class="text-rose-500">*</span></label>
                    <input type="text" x-model="form.name" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-slate-100 focus:border-slate-500 outline-none text-sm" placeholder="Pr. Mohamed Larbi">
                </div>

                {{-- Email & Phone --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Email <span class="text-rose-500">*</span></label>
                        <input type="email" x-model="form.email" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-slate-100 outline-none text-sm" placeholder="email@univ.dz">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Téléphone</label>
                        <input type="tel" x-model="form.phone" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-slate-100 outline-none text-sm" placeholder="05XX XX XX XX">
                    </div>
                </div>

                {{-- Password (only on create or if changing) --}}
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">
                        <span x-text="editMode ? 'Nouveau mot de passe (laisser vide pour ne pas changer)' : 'Mot de passe'"></span>
                        <span class="text-rose-500" x-show="!editMode">*</span>
                    </label>
                    <input type="password" x-model="form.password" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-slate-100 outline-none text-sm" placeholder="••••••••">
                </div>

                {{-- Role --}}
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Rôle <span class="text-rose-500">*</span></label>
                    <select x-model="form.role" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-slate-100 outline-none text-sm font-bold">
                        <option value="">— Sélectionner un rôle —</option>
                        <option value="postulant">Postulant</option>
                        <option value="examiner">Examinateur</option>
                        <option value="president-sub">Président Sous-Commission</option>
                        <option value="president-council">Président Conseil</option>
                        <option value="admin">Administrateur</option>
                    </select>
                </div>

                {{-- Department --}}
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Département</label>
                    <input type="text" x-model="form.dept" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-slate-100 outline-none text-sm" placeholder="Ex: Informatique">
                </div>

                {{-- Active toggle --}}
                <div class="flex items-center justify-between bg-slate-50 rounded-xl p-4">
                    <div>
                        <p class="text-sm font-bold text-slate-700">Compte actif</p>
                        <p class="text-xs text-slate-400">L'utilisateur pourra se connecter</p>
                    </div>
                    <button @click="form.active = !form.active" class="relative w-12 h-6 rounded-full transition-all"
                        :class="form.active ? 'bg-emerald-500' : 'bg-slate-300'">
                        <span class="absolute top-0.5 w-5 h-5 bg-white rounded-full shadow transition-all"
                            :class="form.active ? 'left-[26px]' : 'left-0.5'"></span>
                    </button>
                </div>

                {{-- Actions --}}
                <div class="flex gap-3 justify-end pt-4 border-t border-slate-100">
                    <button @click="showFormModal = false" class="px-5 py-2.5 text-sm font-bold bg-white text-slate-700 border border-slate-200 hover:bg-slate-50 rounded-xl transition-all">Annuler</button>
                    <button @click="saveUser()"
                        :class="canSave() ? 'bg-slate-800 hover:bg-slate-900 text-white shadow-lg' : 'bg-slate-100 text-slate-400 cursor-not-allowed'"
                        class="px-6 py-2.5 text-sm font-bold rounded-xl transition-all flex items-center gap-2">
                        <i class="fa-solid" :class="editMode ? 'fa-save' : 'fa-plus'"></i>
                        <span x-text="editMode ? 'Enregistrer' : 'Créer le Compte'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ══ MODAL: Delete Confirmation ══ --}}
    <div x-show="showDeleteModal" x-cloak class="fixed inset-0 z-[60] flex items-center justify-center p-4"
         x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" @click="showDeleteModal = false"></div>
        <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-md">
            <div class="p-8 text-center space-y-4">
                <div class="w-14 h-14 bg-rose-100 rounded-2xl flex items-center justify-center mx-auto"><i class="fa-solid fa-trash text-rose-600 text-xl"></i></div>
                <h3 class="text-xl font-bold text-slate-900">Supprimer le compte</h3>
                <p class="text-sm text-slate-500">Êtes-vous sûr de vouloir supprimer le compte de <strong x-text="deleteTarget?.name"></strong> ? Cette action est irréversible.</p>
                <div class="flex gap-3 justify-center pt-2">
                    <button @click="showDeleteModal = false" class="px-5 py-2.5 text-sm font-bold bg-white text-slate-700 border border-slate-200 hover:bg-slate-50 rounded-xl transition-all">Annuler</button>
                    <button @click="deleteUser()" class="px-6 py-2.5 text-sm font-bold bg-rose-600 text-white hover:bg-rose-700 rounded-xl transition-all shadow-lg shadow-rose-200 flex items-center gap-2">
                        <i class="fa-solid fa-trash"></i> Supprimer
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function usersAdmin() {
    return {
        search: '',
        filterRole: 'all',
        showFormModal: false,
        showDeleteModal: false,
        editMode: false,
        deleteTarget: null,
        form: { id: null, name: '', email: '', phone: '', password: '', role: '', dept: '', active: true },
        users: [
            { id: 1,  name: 'Pr. Mehdi Boudiaf',        email: 'boudiaf@univ.dz',       phone: '0551 23 45 67', role: 'postulant',         dept: 'Informatique',        active: true,  created: '2026-01-15' },
            { id: 2,  name: 'Dr. Ahmed Benali',          email: 'benali@univ.dz',        phone: '0661 34 56 78', role: 'postulant',         dept: 'Informatique',        active: true,  created: '2026-02-20' },
            { id: 3,  name: 'Dr. Samira Kaci',           email: 'kaci@univ.dz',          phone: '0770 45 67 89', role: 'postulant',         dept: 'Informatique',        active: true,  created: '2026-03-10' },
            { id: 4,  name: 'Pr. Hamid El Moussaoui',    email: 'elmoussaoui@usthb.dz',  phone: '0551 78 90 12', role: 'examiner',          dept: 'Intelligence Artificielle', active: true,  created: '2025-09-01' },
            { id: 5,  name: 'Pr. Fatima Benbouzid',      email: 'benbouzid@usthb.dz',    phone: '0661 89 01 23', role: 'examiner',          dept: 'Intelligence Artificielle', active: true,  created: '2025-09-01' },
            { id: 6,  name: 'Dr. Rachid Boudour',        email: 'boudour@univ-annaba.dz', phone: '0770 90 12 34', role: 'examiner',          dept: 'Deep Learning',       active: true,  created: '2025-10-15' },
            { id: 7,  name: 'Pr. Amel Zenati',           email: 'zenati@esi.dz',         phone: '',              role: 'examiner',          dept: 'Traitement d\'Images', active: false, created: '2025-10-15' },
            { id: 8,  name: 'Pr. Karim Bouziane',        email: 'bouziane@usthb.dz',     phone: '0551 56 78 90', role: 'president-sub',     dept: 'Informatique & IA',   active: true,  created: '2025-06-01' },
            { id: 9,  name: 'Pr. Nadia Bensalem',        email: 'bensalem@univ-blida.dz', phone: '0661 67 89 01', role: 'president-sub',     dept: 'Traitement du Signal', active: true,  created: '2025-06-01' },
            { id: 10, name: 'Pr. M. L. Khelifi',         email: 'khelifi@univ.dz',       phone: '0551 12 34 56', role: 'president-council', dept: 'Direction',           active: true,  created: '2025-01-01' },
        ],
        get filteredUsers() {
            return this.users.filter(u => {
                const matchRole = this.filterRole === 'all' || u.role === this.filterRole;
                const matchSearch = this.search === '' ||
                    u.name.toLowerCase().includes(this.search.toLowerCase()) ||
                    u.email.toLowerCase().includes(this.search.toLowerCase()) ||
                    (u.phone && u.phone.includes(this.search));
                return matchRole && matchSearch;
            });
        },
        roleLabel(r) {
            return { postulant:'Postulant', examiner:'Examinateur', 'president-sub':'Prés. Sous-Comm.', 'president-council':'Prés. Conseil', admin:'Admin' }[r] || r;
        },
        roleClass(r) {
            return {
                postulant: 'bg-blue-50 text-blue-700 border border-blue-200',
                examiner: 'bg-teal-50 text-teal-700 border border-teal-200',
                'president-sub': 'bg-indigo-50 text-indigo-700 border border-indigo-200',
                'president-council': 'bg-amber-50 text-amber-700 border border-amber-200',
                admin: 'bg-slate-100 text-slate-700 border border-slate-300',
            }[r] || '';
        },
        openCreate() {
            this.editMode = false;
            this.form = { id: null, name: '', email: '', phone: '', password: '', role: '', dept: '', active: true };
            this.showFormModal = true;
        },
        openEdit(user) {
            this.editMode = true;
            this.form = { ...user, password: '' };
            this.showFormModal = true;
        },
        canSave() {
            if (!this.form.name || !this.form.email || !this.form.role) return false;
            if (!this.editMode && !this.form.password) return false;
            return true;
        },
        saveUser() {
            if (!this.canSave()) return;
            if (this.editMode) {
                const idx = this.users.findIndex(u => u.id === this.form.id);
                if (idx > -1) { this.users[idx] = { ...this.users[idx], name: this.form.name, email: this.form.email, phone: this.form.phone, role: this.form.role, dept: this.form.dept, active: this.form.active }; }
                showToast('Utilisateur modifié avec succès !');
            } else {
                this.users.push({ id: Date.now(), name: this.form.name, email: this.form.email, phone: this.form.phone, role: this.form.role, dept: this.form.dept, active: this.form.active, created: new Date().toISOString().split('T')[0] });
                showToast('Compte créé avec succès !');
            }
            this.showFormModal = false;
        },
        confirmDelete(user) { this.deleteTarget = user; this.showDeleteModal = true; },
        deleteUser() {
            this.users = this.users.filter(u => u.id !== this.deleteTarget.id);
            this.showDeleteModal = false;
            showToast('Compte supprimé.', 'error');
        }
    };
}
</script>
@endsection
