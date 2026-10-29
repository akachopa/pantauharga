@props([
    'title',
    'subtitle' => '',
    'badge' => 'Modul Resmi',
    'breadcrumb' => 'Bengkulu Tengah',
])

<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-1">
    <div>
        <div class="flex items-center gap-2 mb-1.5">
            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold tracking-wide bg-lime-100 text-emerald-800 border border-lime-300/80">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                {{ $badge }}
            </span>
            <span class="text-xs text-slate-400 font-medium">{{ $breadcrumb }}</span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight leading-tight">{{ $title }}</h1>
        @if($subtitle)
            <p class="text-xs sm:text-sm text-slate-500 mt-1 font-medium">{{ $subtitle }}</p>
        @endif
    </div>
    @isset($actions)
        <div class="flex items-center gap-2.5 flex-wrap">
            {{ $actions }}
        </div>
    @endisset
</div>
