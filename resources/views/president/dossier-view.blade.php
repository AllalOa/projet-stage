@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <div class="flex items-center gap-4">
            <x-button type="ghost" size="sm" icon="fa-solid fa-arrow-left" @click="history.back()">Back</x-button>
            <div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Dossier Review: #P-24912</h1>
                <p class="text-sm text-slate-500">Comparison of expert evaluations and final recommendation.</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <x-button type="secondary" size="md" icon="fa-solid fa-print">Print Dossier</x-button>
        </div>
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
        <!-- Comparison Area (Left) -->
        <div class="lg:col-span-2 space-y-10">
            <!-- Publication Summary -->
            <section class="bg-white rounded-3xl p-8 border border-slate-200 shadow-sm">
                <div class="flex items-start justify-between mb-6">
                    <div>
                        <span class="text-[10px] font-black text-indigo-600 bg-indigo-50 px-2 py-1 rounded uppercase tracking-widest">Journal Article</span>
                        <h2 class="text-2xl font-black text-slate-800 mt-2">Optimization of Serverless Architectures for Big Data</h2>
                    </div>
                    <x-badge status="in_review" label="Ready for Decision" />
                </div>
                <div class="grid grid-cols-3 gap-6 bg-slate-50/50 rounded-2xl p-6 border border-slate-100">
                    <div><p class="text-[9px] font-bold text-slate-400 uppercase">Author</p><p class="text-sm font-bold text-slate-800">Dr. Samir Alaoui</p></div>
                    <div><p class="text-[9px] font-bold text-slate-400 uppercase">Journal</p><p class="text-sm font-bold text-slate-800 italic">IEEE Cloud Computing</p></div>
                    <div><p class="text-[9px] font-bold text-slate-400 uppercase">Submission Date</p><p class="text-sm font-bold text-slate-800">April 12, 2026</p></div>
                </div>
            </section>

            <!-- Experts Side-by-Side -->
            <section>
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-sm font-black text-slate-500 uppercase tracking-widest flex items-center gap-2">
                        <i class="fa-solid fa-users-viewfinder text-indigo-600"></i>
                        Expert Evaluations (3/3 Received)
                    </h3>
                    <span class="text-xs font-bold text-emerald-600 flex items-center gap-1">
                        <i class="fa-solid fa-circle-check"></i> Enforced Minimum Met
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @php $experts = [
                        ['name' => 'Prof. H. Moussaoui', 'rec' => 'approved', 'score' => 92, 'text' => 'Brilliant approach to cold start issues. The methodology is robust and the results are reproducible.'],
                        ['name' => 'Dr. L. Benjelloun', 'rec' => 'approved', 'score' => 88, 'text' => 'Solid contribution. The comparison with existing frameworks is clear and provides good insights.'],
                        ['name' => 'Prof. S. Tahiri', 'rec' => 'approved', 'score' => 95, 'text' => 'Highly innovative. This could set a new standard for serverless optimization in distributed systems.'],
                    ]; @endphp

                    @foreach($experts as $ex)
                        <div class="bg-white p-6 rounded-3xl border border-slate-200 hover:border-indigo-400 transition-all group">
                            <div class="flex justify-between items-start mb-4">
                                <img src="https://ui-avatars.com/api/?name={{ $ex['name'] }}&bg=f8fafc" class="w-10 h-10 rounded-xl" alt="">
                                <x-badge :status="$ex['rec']" />
                            </div>
                            <h4 class="font-bold text-slate-800 group-hover:text-indigo-600 transition-colors">{{ $ex['name'] }}</h4>
                            <p class="text-[10px] text-slate-400 font-black uppercase mt-0.5">Confidence: {{ $ex['score'] }}%</p>
                            <p class="text-xs text-slate-500 mt-4 leading-relaxed line-clamp-4 italic border-l-2 border-slate-100 pl-3">"{{ $ex['text'] }}"</p>
                            <x-button type="ghost" size="sm" class="mt-4 w-full border border-slate-100 group-hover:border-indigo-100">Full Details</x-button>
                        </div>
                    @endforeach
                </div>
            </section>
        </div>

        <!-- Decision Box (Right) -->
        <div class="lg:col-span-1">
            <div class="sticky top-10 space-y-6">
                <x-card title="Final Verdict" subtitle="Signed by Council President">
                    <div class="space-y-6">
                        <div class="p-6 bg-emerald-50 rounded-2xl border border-emerald-100">
                            <div class="flex items-center gap-3 mb-2 text-emerald-700">
                                <i class="fa-solid fa-chart-line text-xl"></i>
                                <h4 class="font-bold">Average Index</h4>
                            </div>
                            <p class="text-3xl font-black text-emerald-600">91.6 <span class="text-sm font-normal text-emerald-400">/ 100</span></p>
                            <p class="text-[10px] text-emerald-500 font-bold uppercase mt-1">High Scientific Consensus</p>
                        </div>

                        <div class="space-y-4 pt-4">
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest">Decision Comment</label>
                            <textarea class="w-full px-4 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-indigo-100 outline-none h-32 text-sm" placeholder="Optional notes for the researcher..."></textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <x-button type="danger" size="lg" class="w-full">Reject</x-button>
                            <x-button type="success" size="lg" class="w-full">Approve</x-button>
                        </div>
                    </div>
                </x-card>

                <div class="bg-amber-50 p-6 rounded-3xl border border-amber-200 flex gap-4">
                    <i class="fa-solid fa-shield-halved text-2xl text-amber-500"></i>
                    <div>
                        <p class="text-sm font-bold text-amber-900">Legal Compliance</p>
                        <p class="text-xs text-amber-700 opacity-80 mt-1">This decision will be digitally signed and archived as a permanent record.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
