@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex items-center justify-between mb-10">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Sub-Commissions</h1>
            <p class="text-slate-500 mt-1">Configure scientific groups and assign presidents.</p>
        </div>
        <x-button type="primary" size="lg" icon="fa-solid fa-layer-group">New Commission</x-button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @php $commissions = [
            ['name' => 'Computer Science & AI', 'pres' => 'Dr. Yassine Mansouri', 'members' => 12, 'active' => 24, 'color' => 'indigo'],
            ['name' => 'Biological Sciences', 'pres' => 'Prof. Amine Slaoui', 'members' => 8, 'active' => 15, 'color' => 'emerald'],
            ['name' => 'Physics & Materials', 'pres' => 'Dr. Meriem Bennani', 'members' => 10, 'active' => 18, 'color' => 'blue'],
            ['name' => 'Humanities & Social', 'pres' => 'Prof. Fatima Zahra', 'members' => 6, 'active' => 9, 'color' => 'amber'],
            ['name' => 'Mathematics', 'pres' => 'Dr. Omar Tazi', 'members' => 5, 'active' => 12, 'color' => 'rose'],
        ]; @endphp

        @foreach($commissions as $comm)
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm hover:shadow-xl transition-all duration-300 group overflow-hidden">
                <div class="h-2 bg-{{ $comm['color'] }}-500"></div>
                <div class="p-8">
                    <div class="flex items-center justify-between mb-6">
                        <div class="w-12 h-12 bg-{{ $comm['color'] }}-50 rounded-2xl flex items-center justify-center text-{{ $comm['color'] }}-600 text-xl shadow-inner border border-{{ $comm['color'] }}-100 group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-atom"></i>
                        </div>
                        <x-button type="ghost" size="sm" icon="fa-solid fa-ellipsis"></x-button>
                    </div>
                    <h3 class="text-xl font-black text-slate-800 mb-2">{{ $comm['name'] }}</h3>
                    <div class="flex items-center gap-2 mb-6">
                        <img src="https://ui-avatars.com/api/?name={{ $comm['pres'] }}&background=f1f5f9" class="w-6 h-6 rounded-full" alt="">
                        <p class="text-xs text-slate-500 font-bold uppercase tracking-tight">President: <span class="text-slate-800">{{ $comm['pres'] }}</span></p>
                    </div>

                    <div class="grid grid-cols-2 gap-4 pt-6 border-t border-slate-50">
                        <div>
                            <p class="text-2xl font-black text-slate-900">{{ $comm['members'] }}</p>
                            <p class="text-[10px] font-bold text-slate-400 uppercase">Board Members</p>
                        </div>
                        <div>
                            <p class="text-2xl font-black text-{{ $comm['color'] }}-600">{{ $comm['active'] }}</p>
                            <p class="text-[10px] font-bold text-slate-400 uppercase">Active Dossiers</p>
                        </div>
                    </div>
                </div>
                <div class="px-8 py-4 bg-slate-50 flex justify-between items-center opacity-0 group-hover:opacity-100 transition-opacity">
                    <button class="text-xs font-black text-slate-400 hover:text-indigo-600 uppercase tracking-widest">Configuration</button>
                    <x-button type="primary" size="sm">Manage</x-button>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
