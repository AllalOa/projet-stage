@extends('layouts.app')

@section('content')
<div class="h-full flex flex-col -m-6">
    <!-- Review Header -->
    <div class="px-8 py-4 bg-white border-b border-slate-200 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <x-button type="ghost" size="sm" icon="fa-solid fa-arrow-left" @click="history.back()">Back</x-button>
            <div class="h-8 w-px bg-slate-200"></div>
            <div>
                <h1 class="text-lg font-black text-slate-900 leading-tight">Serverless Optimization for Big Data</h1>
                <p class="text-xs text-slate-500 italic">Journal Article • Posted by Dr. Samir Alaoui</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <x-button type="secondary" size="md" icon="fa-solid fa-download">Download PDF</x-button>
            <x-button type="primary" size="md">Submit Review</x-button>
        </div>
    </div>

    <!-- Main Review Grid -->
    <div class="flex-1 flex overflow-hidden">
        <!-- Content Panel (Left) -->
        <div class="flex-1 overflow-y-auto p-8 border-r border-slate-200 bg-slate-50/30">
            <div class="max-w-4xl mx-auto space-y-10">
                <section>
                    <h3 class="text-xs font-black text-slate-400 uppercase tracking-[0.2em] mb-4">Abstract</h3>
                    <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 text-slate-700 leading-relaxed italic">
                        "Serverless computing has emerged as a compelling paradigm for deploying applications in the cloud, offering automatic scaling, high availability, and a pay-as-you-go billing model. However, optimizing serverless architectures for big data processing remains a challenge due to cold starts and resource limitations..."
                    </div>
                </section>

                <section>
                    <h3 class="text-xs font-black text-slate-400 uppercase tracking-[0.2em] mb-4">Publication Details</h3>
                    <div class="grid grid-cols-2 gap-6">
                        <x-card title="Journal Info" class="shadow-none border-slate-200">
                            <ul class="space-y-3">
                                <li class="flex justify-between text-sm"><span class="text-slate-500">Name</span> <span class="font-bold text-slate-800">IEEE Cloud</span></li>
                                <li class="flex justify-between text-sm"><span class="text-slate-500">ISSN</span> <span class="font-bold text-slate-800">2325-6095</span></li>
                                <li class="flex justify-between text-sm"><span class="text-slate-500">Impact Factor</span> <span class="font-bold text-emerald-600">5.42</span></li>
                            </ul>
                        </x-card>
                        <x-card title="Co-Authors" class="shadow-none border-slate-200">
                            <div class="flex -space-x-2">
                                <img class="w-8 h-8 rounded-full ring-2 ring-white" src="https://ui-avatars.com/api/?name=JS&bg=6366f1&color=fff" alt="">
                                <img class="w-8 h-8 rounded-full ring-2 ring-white" src="https://ui-avatars.com/api/?name=MK&bg=ec4899&color=fff" alt="">
                                <div class="w-8 h-8 rounded-full bg-slate-100 ring-2 ring-white flex items-center justify-center text-[10px] font-bold text-slate-400">+2</div>
                            </div>
                            <p class="text-xs text-slate-500 mt-4">Listed researchers from UI University.</p>
                        </x-card>
                    </div>
                </section>
            </div>
        </div>

        <!-- Evaluation Panel (Right) -->
        <div class="w-[450px] bg-white overflow-y-auto p-8">
            <h3 class="text-lg font-bold text-slate-900 mb-6 flex items-center gap-2">
                <i class="fa-solid fa-pen-nib text-indigo-600"></i>
                Expert Evaluation
            </h3>

            <form class="space-y-8">
                <div>
                    <label class="block text-sm font-bold text-slate-800 mb-4">1. Overall Recommendation</label>
                    <div class="grid grid-cols-1 gap-3">
                        <label class="flex items-center gap-3 p-4 rounded-2xl border-2 border-emerald-500 bg-emerald-50 cursor-pointer">
                            <input type="radio" name="rec" class="hidden" checked>
                            <i class="fa-solid fa-circle-check text-emerald-600"></i>
                            <div>
                                <p class="text-sm font-bold text-emerald-900">Highly Recommend</p>
                                <p class="text-[10px] text-emerald-600 uppercase font-black tracking-widest">Approve</p>
                            </div>
                        </label>
                        <label class="flex items-center gap-3 p-4 rounded-2xl border-2 border-slate-100 hover:border-indigo-100 transition-colors cursor-pointer">
                            <input type="radio" name="rec" class="hidden">
                            <i class="fa-solid fa-triangle-exclamation text-amber-500"></i>
                            <div>
                                <p class="text-sm font-bold text-slate-800">Minor Revisions</p>
                                <p class="text-[10px] text-slate-400 uppercase font-black tracking-widest">In Review</p>
                            </div>
                        </label>
                        <label class="flex items-center gap-3 p-4 rounded-2xl border-2 border-slate-100 hover:border-indigo-100 transition-colors cursor-pointer">
                            <input type="radio" name="rec" class="hidden">
                            <i class="fa-solid fa-circle-xmark text-rose-500"></i>
                            <div>
                                <p class="text-sm font-bold text-slate-800">Not Recommended</p>
                                <p class="text-[10px] text-slate-400 uppercase font-black tracking-widest">Reject</p>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="space-y-4">
                    <label class="block text-sm font-bold text-slate-800">2. Evaluation Comments</label>
                    <textarea class="w-full px-4 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 outline-none h-40" placeholder="Provide detailed feedback for the scientific council..."></textarea>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-800 mb-2">3. Confidence Level</label>
                    <input type="range" class="w-full h-2 bg-slate-100 rounded-lg appearance-none cursor-pointer accent-indigo-600" min="0" max="100">
                    <div class="flex justify-between text-[10px] font-bold text-slate-400 mt-2">
                        <span>BASIC</span>
                        <span>EXPERT</span>
                    </div>
                </div>

                <div class="pt-6 border-t border-slate-100">
                    <x-button type="primary" class="w-full py-4 text-base">Submit Final Review</x-button>
                    <p class="text-[10px] text-slate-400 text-center mt-4 italic uppercase tracking-widest leading-loose">
                        By submitting, you certify that this review is impartial and follows university ethics.
                    </p>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
