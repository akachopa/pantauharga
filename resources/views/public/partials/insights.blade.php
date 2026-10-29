@php
    $toneClasses = [
        'danger' => ['border' => 'border-l-rose-600', 'label' => 'text-rose-700'],
        'warning' => ['border' => 'border-l-amber-500', 'label' => 'text-amber-700'],
        'info' => ['border' => 'border-l-sky-600', 'label' => 'text-sky-700'],
        'success' => ['border' => 'border-l-emerald-600', 'label' => 'text-emerald-700'],
        'neutral' => ['border' => 'border-l-slate-400', 'label' => 'text-slate-500'],
    ];
@endphp
@if($insights->isNotEmpty())
    <section id="insight" class="scroll-mt-20 space-y-4">
        <div class="border-b border-slate-300 pb-3">
            <h2 class="text-lg sm:text-xl font-bold text-slate-900 tracking-tight">Insight Harga</h2>
            <p class="text-sm text-slate-500 mt-1">Dirangkum otomatis dari data harga terverifikasi sesuai filter yang dipilih.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3">
            @foreach($insights as $insight)
                @php $tone = $toneClasses[$insight['tone']] ?? $toneClasses['neutral']; @endphp
                <article class="bg-white border border-slate-300 border-l-4 {{ $tone['border'] }} rounded-md px-4 py-3.5" data-insight-theme="{{ $insight['theme'] }}">
                    <p class="text-[11px] font-semibold {{ $tone['label'] }}">{{ $insight['label'] }}</p>
                    <h3 class="mt-1 font-bold text-slate-900 leading-snug">{{ $insight['title'] }}</h3>
                    <p class="mt-1 text-sm text-slate-600">{{ $insight['message'] }}</p>
                </article>
            @endforeach
        </div>
    </section>
@endif
