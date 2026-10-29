@php
    $changePct = $changePct ?? null;
    $compact = $compact ?? false;
    $sizeClass = $compact ? 'text-[11px]' : 'text-xs';
@endphp
@if($changePct === null)
    <span class="inline-flex items-center gap-1 {{ $sizeClass }} font-medium text-slate-400">
        <span aria-hidden="true">–</span>
        <span>Belum ada pembanding</span>
    </span>
@elseif(abs($changePct) < 0.05)
    <span class="inline-flex items-center gap-1 {{ $sizeClass }} font-semibold text-slate-600 tabular-nums">
        <span aria-hidden="true">→</span>
        <span>Stabil {{ number_format($changePct, 2, ',', '.') }}%</span>
    </span>
@elseif($changePct > 0)
    <span class="inline-flex items-center gap-1 {{ $sizeClass }} font-semibold text-rose-700 tabular-nums">
        <span aria-hidden="true">▲</span>
        <span>Naik {{ number_format($changePct, 2, ',', '.') }}%</span>
    </span>
@else
    <span class="inline-flex items-center gap-1 {{ $sizeClass }} font-semibold text-emerald-700 tabular-nums">
        <span aria-hidden="true">▼</span>
        <span>Turun {{ number_format(abs($changePct), 2, ',', '.') }}%</span>
    </span>
@endif
