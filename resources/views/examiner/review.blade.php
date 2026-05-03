@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto space-y-6" x-data="{
    step: 1,
    avis: '',
    comment: '',
    confidence: 50,
    submitted: false,
    article: {
        ref: 'SC-2491',
        title: 'Deep Learning in Medical Imaging',
        postulant: 'Dr. Ahmed Benali',
        grade: 'Maître de Conférences A',
        dept: 'Informatique',
        type: 'Journal',
        source: 'IEEE Trans. Medical Imaging',
        issn: '0278-0062',
        impactFactor: '10.6',
        abstract: 'This paper presents a novel deep learning framework for automated medical image analysis. We propose a multi-scale attention mechanism that significantly improves segmentation accuracy on chest X-rays and MRI scans. Our approach achieves state-of-the-art results on three benchmark datasets, demonstrating a 12% improvement over existing methods. The framework is designed for clinical deployment with real-time inference capabilities, making it suitable for resource-constrained healthcare settings. We also introduce a new data augmentation strategy specifically tailored for medical imaging that addresses the common challenge of limited annotated training data.',
        coAuthors: ['Dr. S. Kaci', 'Pr. M. Boudiaf'],
        date: '2026-04-28',
        deadline: '2026-05-15',
    },
    submitReview() {
        if (this.avis && this.comment.trim()) {
            this.submitted = true;
            showToast('Avis soumis avec succès !');
        }
    }
}">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-slate-400">
        <a href="/examiner/dashboard" @click.prevent="navigateTo('examiner/dashboard')" class="hover:text-teal-600 transition-colors">Dashboard</a>
        <i class="fa-solid fa-chevron-right text-[10px]"></i>
        <span class="text-slate-700 font-bold">Évaluation</span>
    </div>

    {{-- Article Header --}}
    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
        <div class="flex items-start justify-between flex-wrap gap-4">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <span class="text-[10px] font-black text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded tracking-tighter uppercase" x-text="article.ref"></span>
                    <span class="text-xs font-bold px-2 py-1 rounded-full bg-indigo-100 text-indigo-700" x-text="article.type"></span>
                </div>
                <h2 class="text-xl font-extrabold text-slate-900" x-text="article.title"></h2>
                <p class="text-sm text-slate-500 mt-1" x-text="article.postulant + ' · ' + article.source"></p>
            </div>
            <div class="text-right text-xs text-slate-400">
                <p>Deadline: <strong class="text-slate-700" x-text="article.deadline"></strong></p>
            </div>
        </div>
    </div>

    {{-- Step Progress --}}
    <div class="flex items-center mb-2 bg-white border border-slate-200 rounded-2xl p-4">
        @foreach([1=>'Consulter Article', 2=>'Lire le Résumé', 3=>'Rendre l\'Avis', 4=>'Confirmation'] as $n => $label)
        <div class="flex-1 flex flex-col items-center gap-1.5">
            <div class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-sm transition-all"
                 :class="step >= {{ $n }} ? 'bg-teal-600 text-white shadow-lg shadow-teal-200' : 'bg-white border-2 border-slate-200 text-slate-400'">
                <span x-show="step > {{ $n }}"><i class="fa-solid fa-check text-xs"></i></span>
                <span x-show="step <= {{ $n }}">{{ $n }}</span>
            </div>
            <span class="text-[10px] font-bold uppercase tracking-wider" :class="step >= {{ $n }} ? 'text-teal-600' : 'text-slate-400'">{{ $label }}</span>
        </div>
        @if($n < 4)<div class="flex-shrink-0 w-8 h-0.5 bg-slate-200 mt-[-18px]"></div>@endif
        @endforeach
    </div>

    {{-- Success State --}}
    <template x-if="submitted">
        <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-8 text-center space-y-4">
            <div class="w-16 h-16 bg-emerald-100 rounded-2xl flex items-center justify-center mx-auto">
                <i class="fa-solid fa-check-double text-emerald-600 text-2xl"></i>
            </div>
            <h3 class="text-xl font-bold text-emerald-800">Avis Soumis avec Succès</h3>
            <p class="text-sm text-emerald-600">Votre évaluation a été transmise à la sous-commission. Merci pour votre contribution.</p>
            <a href="/examiner/dashboard" @click.prevent="navigateTo('examiner/dashboard')"
               class="inline-flex items-center gap-2 px-6 py-3 text-sm font-bold bg-teal-600 text-white rounded-xl hover:bg-teal-700 transition-all shadow-lg shadow-teal-200">
                <i class="fa-solid fa-arrow-left"></i> Retour au Dashboard
            </a>
        </div>
    </template>

    <template x-if="!submitted">
        <div>
            {{-- STEP 1: Consulter Article --}}
            <div x-show="step === 1" x-transition>
                <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100">
                        <h3 class="text-lg font-bold text-slate-800"><i class="fa-solid fa-file-lines text-teal-600 mr-2"></i>Détails de la Publication</h3>
                    </div>
                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-5">
                            <div><p class="text-xs font-bold text-slate-400 uppercase mb-1">Postulant</p><p class="font-bold text-slate-800" x-text="article.postulant"></p></div>
                            <div><p class="text-xs font-bold text-slate-400 uppercase mb-1">Grade</p><p class="font-bold text-slate-800" x-text="article.grade"></p></div>
                            <div><p class="text-xs font-bold text-slate-400 uppercase mb-1">Département</p><p class="font-bold text-slate-800" x-text="article.dept"></p></div>
                            <div><p class="text-xs font-bold text-slate-400 uppercase mb-1">Source</p><p class="font-bold text-slate-800" x-text="article.source"></p></div>
                            <div><p class="text-xs font-bold text-slate-400 uppercase mb-1">ISSN</p><p class="font-bold text-slate-800" x-text="article.issn"></p></div>
                            <div><p class="text-xs font-bold text-slate-400 uppercase mb-1">Facteur d'Impact</p><p class="font-bold text-emerald-600" x-text="article.impactFactor"></p></div>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase mb-2">Co-auteurs</p>
                            <div class="flex gap-2 flex-wrap">
                                <template x-for="author in article.coAuthors" :key="author">
                                    <span class="text-xs font-bold bg-slate-100 text-slate-600 px-3 py-1 rounded-lg" x-text="author"></span>
                                </template>
                            </div>
                        </div>
                        {{-- PDF preview placeholder --}}
                        <div class="border-2 border-dashed border-slate-200 rounded-2xl p-8 text-center bg-slate-50">
                            <i class="fa-solid fa-file-pdf text-4xl text-red-400 mb-3"></i>
                            <p class="font-bold text-slate-600">Document PDF de la publication</p>
                            <p class="text-xs text-slate-400 mt-1">deep_learning_medical_imaging_2026.pdf — 2.4 MB</p>
                            <button class="mt-4 inline-flex items-center gap-2 px-4 py-2 text-sm font-bold bg-white text-slate-700 border border-slate-200 rounded-xl hover:bg-slate-50 transition-all">
                                <i class="fa-solid fa-download"></i> Télécharger le PDF
                            </button>
                        </div>

                        <div class="flex justify-end pt-4 border-t border-slate-100">
                            <button @click="step = 2" class="flex items-center gap-2 text-sm font-bold px-6 py-3 rounded-xl bg-teal-600 hover:bg-teal-700 text-white shadow-lg shadow-teal-200 transition-all">
                                Suivant <i class="fa-solid fa-arrow-right"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- STEP 2: Lire le Résumé --}}
            <div x-show="step === 2" x-transition>
                <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100">
                        <h3 class="text-lg font-bold text-slate-800"><i class="fa-solid fa-book-open text-teal-600 mr-2"></i>Résumé / Abstract</h3>
                    </div>
                    <div class="p-6 space-y-6">
                        <div class="bg-slate-50 rounded-2xl p-8 border border-slate-100">
                            <p class="text-slate-700 leading-relaxed italic text-[15px]" x-text="'\"' + article.abstract + '\"'"></p>
                        </div>
                        <div class="bg-teal-50 border border-teal-200 rounded-xl p-4 flex items-start gap-3">
                            <i class="fa-solid fa-lightbulb text-teal-500 mt-0.5"></i>
                            <div>
                                <p class="text-sm font-bold text-teal-800">Consignes d'évaluation</p>
                                <p class="text-xs text-teal-700 mt-1">Évaluez la qualité scientifique, l'originalité de la contribution, la rigueur méthodologique et la clarté de la rédaction. Votre avis doit être objectif et constructif.</p>
                            </div>
                        </div>

                        <div class="flex justify-between pt-4 border-t border-slate-100">
                            <button @click="step = 1" class="flex items-center gap-2 text-sm font-bold text-slate-500 hover:text-slate-800 px-4 py-2 rounded-xl hover:bg-slate-100 transition-all">
                                <i class="fa-solid fa-arrow-left"></i> Retour
                            </button>
                            <button @click="step = 3" class="flex items-center gap-2 text-sm font-bold px-6 py-3 rounded-xl bg-teal-600 hover:bg-teal-700 text-white shadow-lg shadow-teal-200 transition-all">
                                Rendre mon Avis <i class="fa-solid fa-arrow-right"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- STEP 3: Rendre l'Avis --}}
            <div x-show="step === 3" x-transition>
                <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100">
                        <h3 class="text-lg font-bold text-slate-800"><i class="fa-solid fa-pen-nib text-teal-600 mr-2"></i>Rendre l'Avis</h3>
                    </div>
                    <div class="p-6 space-y-6">
                        {{-- Decision --}}
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-3">Votre Avis <span class="text-rose-500">*</span></label>
                            <div class="grid grid-cols-2 gap-3">
                                <button @click="avis = 'favorable'" class="p-5 border-2 rounded-xl text-center transition-all" :class="avis === 'favorable' ? 'border-emerald-500 bg-emerald-50' : 'border-slate-200 hover:border-emerald-300'">
                                    <i class="fa-solid fa-thumbs-up text-emerald-500 text-2xl mb-2"></i>
                                    <p class="text-sm font-bold text-slate-800">Avis Favorable</p>
                                    <p class="text-[10px] text-slate-400 mt-1">Publication recommandée</p>
                                </button>
                                <button @click="avis = 'defavorable'" class="p-5 border-2 rounded-xl text-center transition-all" :class="avis === 'defavorable' ? 'border-rose-500 bg-rose-50' : 'border-slate-200 hover:border-rose-300'">
                                    <i class="fa-solid fa-thumbs-down text-rose-500 text-2xl mb-2"></i>
                                    <p class="text-sm font-bold text-slate-800">Avis Défavorable</p>
                                    <p class="text-[10px] text-slate-400 mt-1">Publication non recommandée</p>
                                </button>
                            </div>
                        </div>

                        {{-- Comment --}}
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Commentaire détaillé <span class="text-rose-500">*</span></label>
                            <textarea x-model="comment" rows="6" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-teal-100 focus:border-teal-500 outline-none resize-none transition-all text-sm" placeholder="Fournissez une évaluation détaillée : qualité scientifique, originalité, méthodologie, rédaction..."></textarea>
                        </div>

                        {{-- Confidence --}}
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Niveau de confiance</label>
                            <input type="range" x-model="confidence" class="w-full h-2 bg-slate-100 rounded-lg appearance-none cursor-pointer accent-teal-600" min="0" max="100">
                            <div class="flex justify-between text-[10px] font-bold text-slate-400 mt-1">
                                <span>Connaissance basique</span>
                                <span x-text="confidence + '%'" class="text-teal-600"></span>
                                <span>Expert du domaine</span>
                            </div>
                        </div>

                        <div class="flex justify-between pt-4 border-t border-slate-100">
                            <button @click="step = 2" class="flex items-center gap-2 text-sm font-bold text-slate-500 hover:text-slate-800 px-4 py-2 rounded-xl hover:bg-slate-100 transition-all">
                                <i class="fa-solid fa-arrow-left"></i> Retour
                            </button>
                            <button @click="avis && comment.trim() ? step = 4 : null"
                                :class="avis && comment.trim() ? 'bg-teal-600 hover:bg-teal-700 text-white shadow-lg shadow-teal-200' : 'bg-slate-100 text-slate-400 cursor-not-allowed'"
                                class="flex items-center gap-2 text-sm font-bold px-6 py-3 rounded-xl transition-all">
                                Vérifier <i class="fa-solid fa-arrow-right"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- STEP 4: Confirmation --}}
            <div x-show="step === 4" x-transition>
                <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100">
                        <h3 class="text-lg font-bold text-slate-800"><i class="fa-solid fa-clipboard-check text-teal-600 mr-2"></i>Confirmation</h3>
                    </div>
                    <div class="p-6 space-y-5">
                        <div class="bg-slate-50 rounded-2xl p-6 space-y-4">
                            <h4 class="font-black text-slate-800 flex items-center gap-2"><i class="fa-solid fa-clipboard-check text-teal-500"></i> Récapitulatif</h4>
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div><p class="text-xs font-bold text-slate-400 uppercase mb-1">Dossier</p><p class="font-bold text-slate-800" x-text="article.ref + ' — ' + article.title"></p></div>
                                <div><p class="text-xs font-bold text-slate-400 uppercase mb-1">Postulant</p><p class="font-bold text-slate-800" x-text="article.postulant"></p></div>
                                <div>
                                    <p class="text-xs font-bold text-slate-400 uppercase mb-1">Votre Avis</p>
                                    <span class="text-sm font-bold px-3 py-1 rounded-full border"
                                        :class="avis === 'favorable' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-rose-50 text-rose-700 border-rose-200'"
                                        x-text="avis === 'favorable' ? '✅ Favorable' : '❌ Défavorable'"></span>
                                </div>
                                <div><p class="text-xs font-bold text-slate-400 uppercase mb-1">Confiance</p><p class="font-bold text-slate-800" x-text="confidence + '%'"></p></div>
                                <div class="col-span-2"><p class="text-xs font-bold text-slate-400 uppercase mb-1">Commentaire</p><p class="text-sm text-slate-700 italic bg-white p-3 rounded-lg" x-text="comment"></p></div>
                            </div>
                        </div>

                        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 text-sm text-amber-800 flex gap-3">
                            <i class="fa-solid fa-triangle-exclamation mt-0.5 flex-shrink-0"></i>
                            <p>En soumettant cet avis, vous certifiez que votre évaluation est <strong>impartiale</strong> et conforme à l'éthique universitaire. Cette action est <strong>définitive</strong>.</p>
                        </div>

                        <div class="flex justify-between pt-4 border-t border-slate-100">
                            <button @click="step = 3" class="flex items-center gap-2 text-sm font-bold text-slate-500 hover:text-slate-800 px-4 py-2 rounded-xl hover:bg-slate-100 transition-all">
                                <i class="fa-solid fa-arrow-left"></i> Modifier
                            </button>
                            <button @click="submitReview()" class="flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-6 py-3 rounded-xl transition-all shadow-lg shadow-emerald-200 text-sm">
                                <i class="fa-solid fa-paper-plane"></i> Soumettre mon Avis
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>

</div>
@endsection
