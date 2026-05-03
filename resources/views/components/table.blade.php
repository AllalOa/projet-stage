@props(['headers' => []])

<div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white">
    <table class="w-full text-left border-collapse">
        <thead class="bg-slate-50/50 border-b border-slate-200">
            <tr>
                @foreach($headers as $header)
                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest">{{ $header }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            {{ $slot }}
        </tbody>
    </table>
</div>
