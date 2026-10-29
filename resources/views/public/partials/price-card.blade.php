@php
    $referenceLabels = [
        'above' => 'Di atas acuan '.($card['vs_reference_pct'] > 0 ? '+' : '').number_format((float) $card['vs_reference_pct'], 1, ',', '.').'%',
        'below' => 'Di bawah acuan '.number_format((float) $card['vs_reference_pct'], 1, ',', '.').'%',
        'on' => 'Sesuai acuan',
        'none' => 'Acuan belum ditetapkan',
    ];
    $referenceDots = [
        'above' => 'bg-amber-500',
        'below' => 'bg-sky-500',
        'on' => 'bg-brand-500',
        'none' => 'bg-slate-300',
    ];
@endphp
<article data-commodity-name="{{ strtolower($card['name']) }}" class="bg-white rounded-2xl ring-1 ring-slate-200/70 hover:ring-slate-300 transition p-4 sm:p-5 flex flex-col">
    <div class="flex items-start gap-3">
        <div class="scale-90 sm:scale-100 origin-top-left -mr-1 sm:mr-0">
            @include('prices.partials.commodity-icon', ['name' => $card['name']])
        </div>
        <div class="min-w-0 flex-1">
            <h3 class="font-bold text-sm sm:text-[15px] text-ink leading-snug line-clamp-2">{{ $card['name'] }}</h3>
            <p class="text-xs text-slate-500 mt-0.5 truncate">{{ $card['category'] ?: 'Komoditi' }}</p>
        </div>
    </div>

    <div class="mt-4">
        <div class="flex items-baseline gap-1 flex-wrap">
            <span class="font-display text-xl sm:text-[26px] font-extrabold text-ink tabular-nums tracking-tight leading-none">Rp {{ number_format($card['avg_price'], 0, ',', '.') }}</span>
            <span class="text-xs text-slate-400">/{{ $card['base_unit'] }}</span>
        </div>
        <div class="mt-2">
            @include('public.partials.change-badge', ['changePct' => $card['change_pct'], 'compact' => true])
        </div>
    </div>

    @if($card['sparkline'] !== '')
        <svg viewBox="0 0 88 28" class="mt-4 w-full h-9" preserveAspectRatio="none" role="img" aria-label="Tren 7 hari {{ $card['name'] }}">
            <polyline fill="none" stroke="{{ $card['sparkline_color'] }}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" vector-effect="non-scaling-stroke" points="{{ $card['sparkline'] }}"/>
        </svg>
    @endif

    <dl class="mt-4 pt-3 border-t border-slate-100 space-y-1 text-[11px] sm:text-xs text-slate-500">
        <div class="flex items-center gap-1.5">
            <span class="w-1.5 h-1.5 rounded-full shrink-0 {{ $referenceDots[$card['reference_status']] }}"></span>
            <span class="truncate">{{ $referenceLabels[$card['reference_status']] }}</span>
        </div>
        <div class="hidden sm:block tabular-nums">Rentang Rp {{ number_format($card['min_price'], 0, ',', '.') }} – {{ number_format($card['max_price'], 0, ',', '.') }}</div>
        @if(! empty($card['last_updated']))
            <div class="text-slate-400">Diperbarui {{ \Carbon\Carbon::parse($card['last_updated'])->translatedFormat('d M, H:i') }}</div>
        @endif
        @if(($card['citizen_report_count'] ?? 0) > 0)
            <div class="font-semibold text-brand-700">{{ $card['citizen_report_count'] }} laporan warga hari ini</div>
        @endif
    </dl>
</article>
