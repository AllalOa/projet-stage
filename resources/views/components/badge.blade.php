@props(['status' => 'pending', 'label' => null])

@php
    $colors = [
        'pending' => 'bg-slate-100 text-slate-600 border-slate-200',
        'in_review' => 'bg-amber-50 text-amber-600 border-amber-200',
        'approved' => 'bg-emerald-50 text-emerald-600 border-emerald-200',
        'rejected' => 'bg-rose-50 text-rose-600 border-rose-200',
        'info' => 'bg-indigo-50 text-indigo-600 border-indigo-200',
        // Support direct DB status
        'proposition' => 'bg-blue-50 text-blue-600 border-blue-200',
        'en_attente' => 'bg-slate-100 text-slate-600 border-slate-200',
        'en_cours' => 'bg-amber-50 text-amber-600 border-amber-200',
        'termine' => 'bg-emerald-50 text-emerald-600 border-emerald-200',
        'favorable' => 'bg-emerald-50 text-emerald-600 border-emerald-200',
        'defavorable' => 'bg-rose-50 text-rose-600 border-rose-200',
        'reserve' => 'bg-amber-50 text-amber-600 border-amber-200',
        'ajourne' => 'bg-orange-50 text-orange-600 border-orange-200',
    ];

    $colorClass = $colors[$status] ?? 'bg-slate-50 text-slate-600 border-slate-200';
    $label = $label ?? ucwords(str_replace('_', ' ', $status));
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border {$colorClass} uppercase tracking-wider"]) }}>
    <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ str_replace('text-', 'bg-', explode(' ', $colorClass)[1] ?? 'bg-slate-600') }}"></span>
    {{ $label }}
</span>
