@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex items-center justify-between mb-10">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Personnel Management</h1>
            <p class="text-slate-500 mt-1">Manage system researchers, examiners, and board members.</p>
        </div>
        <x-button type="primary" size="lg" icon="fa-solid fa-user-plus" @click="$store.modals.openModal('add-user')">
            Add Personnel
        </x-button>
    </div>

    <!-- Filters & Search -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200 mb-10 flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-6">
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-slate-400 uppercase">Role:</span>
                <select class="bg-slate-50 border-0 rounded-xl text-sm font-bold text-slate-700 px-4 py-2 focus:ring-2 focus:ring-indigo-500">
                    <option>All Roles</option>
                    <option>Postulant</option>
                    <option>Examiner</option>
                    <option>Sub-Comm Pres.</option>
                </select>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-slate-400 uppercase">Status:</span>
                <div class="flex gap-2">
                    <button class="w-3 h-3 rounded-full bg-emerald-500 ring-4 ring-emerald-50"></button>
                    <button class="w-3 h-3 rounded-full bg-slate-200 hover:bg-rose-400 transition-all"></button>
                </div>
            </div>
        </div>
        <div class="relative flex-1 max-w-sm">
            <span class="absolute inset-y-0 left-4 flex items-center text-slate-400">
                <i class="fa-solid fa-magnifying-glass"></i>
            </span>
            <input type="text" placeholder="Search by name or email..." class="w-full pl-12 pr-4 py-2.5 bg-slate-50 border-0 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500">
        </div>
    </div>

    <!-- User Table -->
    <x-card noPadding="true">
        <x-table :headers="['User', 'Role', 'Sub-Commission', 'Requests', 'Account', 'Actions']">
            @php $users = [
                ['name' => 'Dr. Ahmed Bennani', 'email' => 'ahmed.b@univ.edu', 'role' => 'Postulant', 'comm' => 'Computer Science', 'stats' => '12 Total', 'status' => 'active'],
                ['name' => 'Prof. Karima Loukili', 'email' => 'karima.l@univ.edu', 'role' => 'Examiner', 'comm' => 'Bio-Medicine', 'stats' => '42 Reviews', 'status' => 'active'],
                ['name' => 'Dr. Yassine Mansouri', 'email' => 'yassine.m@univ.edu', 'role' => 'Pres. Sub-Comm', 'comm' => 'Physics', 'stats' => '54 Dossiers', 'status' => 'active'],
            ]; @endphp

            @foreach($users as $user)
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="px-6 py-5">
                        <div class="flex items-center gap-4">
                            <img src="https://ui-avatars.com/api/?name={{ $user['name'] }}&background=6366f1&color=fff" class="w-10 h-10 rounded-xl" alt="">
                            <div>
                                <p class="text-sm font-bold text-slate-800">{{ $user['name'] }}</p>
                                <p class="text-[10px] text-slate-400 font-bold uppercase">{{ $user['email'] }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-5">
                        <span class="text-xs font-black text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-lg uppercase tracking-tight">{{ $user['role'] }}</span>
                    </td>
                    <td class="px-6 py-5">
                        <p class="text-sm text-slate-600 font-medium">{{ $user['comm'] }}</p>
                    </td>
                    <td class="px-6 py-5">
                        <span class="text-sm font-bold text-slate-800">{{ $user['stats'] }}</span>
                    </td>
                    <td class="px-6 py-5">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                            <span class="text-xs font-bold text-slate-500 uppercase">Active</span>
                        </div>
                    </td>
                    <td class="px-6 py-5">
                        <div class="flex items-center gap-2">
                            <x-button type="ghost" size="sm" icon="fa-solid fa-user-pen"></x-button>
                            <x-button type="ghost" size="sm" class="text-rose-400 hover:text-rose-600" icon="fa-solid fa-user-minus"></x-button>
                        </div>
                    </td>
                </tr>
            @endforeach
        </x-table>
    </x-card>

    <!-- Modal for Adding User -->
    <x-modal id="add-user" title="Add New Personnel" size="md">
        <form class="space-y-6">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Full Name</label>
                    <input type="text" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-4 focus:ring-indigo-100" placeholder="e.g. Dr. John Doe">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Email</label>
                    <input type="email" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-4 focus:ring-indigo-100" placeholder="name@univ.edu">
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">System Role</label>
                    <select class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-4 focus:ring-indigo-100">
                        <option>Postulant (Researcher)</option>
                        <option>Examiner (Doctorant)</option>
                        <option>President Sub-Commission</option>
                        <option>Scientific Council President</option>
                        <option>System Admin</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Commission Assignment</label>
                    <select class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-4 focus:ring-indigo-100">
                        <option>Computer Science</option>
                        <option>Artificial Intelligence</option>
                        <option>Bio-Technology</option>
                        <option>Humanities</option>
                    </select>
                </div>
            </div>

            <div class="pt-6 border-t border-slate-100 flex justify-end gap-3">
                <x-button type="secondary" @click="$store.modals.close()">Cancel</x-button>
                <x-button type="primary" @click="$store.modals.close(); showToast('User account created!')">Create Account</x-button>
            </div>
        </form>
    </x-modal>
</div>
@endsection
