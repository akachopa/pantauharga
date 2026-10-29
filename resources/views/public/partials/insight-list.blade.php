@php
    $toneStyles = [
        'danger' => ['icon' => 'bg-rose-50 text-rose-600', 'label' => 'text-rose-700', 'path' => 'M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941'],
        'warning' => ['icon' => 'bg-amber-50 text-amber-600', 'label' => 'text-amber-700', 'path' => 'M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z'],
        'info' => ['icon' => 'bg-sky-50 text-sky-600', 'label' => 'text-sky-700', 'path' => 'M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z'],
        'success' => ['icon' => 'bg-brand-50 text-brand-600', 'label' => 'text-brand-700', 'path' => 'M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
        'neutral' => ['icon' => 'bg-slate-100 text-slate-500', 'label' => 'text-slate-500', 'path' => 'M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5'],
    ];
@endphp
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3 sm:gap-4">
    @foreach($insights as $insight)
        @php $tone = $toneStyles[$insight['tone']] ?? $toneStyles['neutral']; @endphp
        <article class="bg-white rounded-2xl ring-1 ring-slate-200/70 p-5 flex gap-4" data-insight-theme="{{ $insight['theme'] }}">
            <span class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 {{ $tone['icon'] }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $tone['path'] }}"/></svg>
            </span>
            <div class="min-w-0">
                <p class="text-xs font-semibold {{ $tone['label'] }}">{{ $insight['label'] }}</p>
                <h3 class="mt-0.5 font-bold text-ink leading-snug">{{ $insight['title'] }}</h3>
                <p class="mt-1.5 text-sm text-slate-600 leading-relaxed">{{ $insight['message'] }}</p>
            </div>
        </article>
    @endforeach
</div>
