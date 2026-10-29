@php
    $referenceLabels = [
        'above' => 'Di atas acuan '.($card['vs_reference_pct'] > 0 ? '+' : '').number_format((float) $card['vs_reference_pct'], 1, ',', '.').'%',
        'below' => 'Di bawah acuan '.number_format((float) $card['vs_reference_pct'], 1, ',', '.').'%',
        'on' => 'Sesuai acuan',
        'none' => 'Acuan belum ditetapkan',
    ];
    $referenceClasses = [
        'above' => 'text-amber-700',
        'below' => 'text-sky-700',
        'on' => 'text-slate-600',
        'none' => 'text-slate-400',
    ];
@endphp
<article data-commodity-name="{{ strtolower($card['name']) }}" class="group bg-white rounded-lg p-3.5 sm:p-5 border border-slate-200 hover:shadow-card-hover hover:-translate-y-0.5 hover:border-brand-200 transition flex flex-col gap-3 sm:gap-4">
    <div class="flex items-start gap-2.5 sm:gap-3 min-w-0">
        <div class="scale-90 sm:scale-100 origin-top-left -mr-1 sm:mr-0">
            @include('prices.partials.commodity-icon', ['name' => $card['name']])
        </div>
        <div class="min-w-0 flex-1">
            <h3 class="font-bold text-sm sm:text-base text-slate-900 leading-tight line-clamp-2">{{ $card['name'] }}</h3>
            <p class="text-[11px] sm:text-xs text-slate-500 mt-0.5 truncate">{{ $card['category'] ?: 'Komoditi' }} · {{ $card['base_unit'] }}</p>
        </div>
    </div>

    <div>
        <div class="text-[11px] text-slate-500">Rata-rata per {{ $card['base_unit'] }}</div>
        <div class="text-lg sm:text-2xl font-bold text-slate-900 tabular-nums tracking-tight mt-0.5 sm:mt-1 leading-tight">
            Rp {{ number_format($card['avg_price'], 0, ',', '.') }}
        </div>
        <div class="mt-1.5">
            @include('public.partials.change-badge', ['changePct' => $card['change_pct'], 'compact' => true])
        </div>
        <p class="hidden sm:block text-xs text-slate-500 mt-2 tabular-nums">
            Rentang Rp {{ number_format($card['min_price'], 0, ',', '.') }} – {{ number_format($card['max_price'], 0, ',', '.') }}
        </p>
        @if(! empty($card['last_updated']))
            <p class="text-[10px] sm:text-[11px] text-slate-400 mt-1">
                Diperbarui {{ \Carbon\Carbon::parse($card['last_updated'])->translatedFormat('d M Y H:i') }}
            </p>
        @endif
        @if(($card['citizen_report_count'] ?? 0) > 0)
            <p class="text-[10px] sm:text-[11px] font-semibold text-brand-700 mt-1">{{ $card['citizen_report_count'] }} laporan warga hari ini</p>
        @endif
    </div>

    <div class="mt-auto pt-2.5 border-t border-dashed border-slate-200 flex flex-col sm:flex-row sm:items-end justify-between gap-2 sm:gap-3">
        <span class="text-[11px] font-medium {{ $referenceClasses[$card['reference_status']] }}">
            {{ $referenceLabels[$card['reference_status']] }}
        </span>
        @if($card['sparkline'] !== '')
            <svg viewBox="0 0 88 28" class="w-full sm:w-24 h-7" preserveAspectRatio="none" role="img" aria-label="Tren 7 hari {{ $card['name'] }}">
                <polyline fill="none" stroke="{{ $card['sparkline_color'] }}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" vector-effect="non-scaling-stroke" points="{{ $card['sparkline'] }}"/>
            </svg>
        @endif
    </div>
</article>
