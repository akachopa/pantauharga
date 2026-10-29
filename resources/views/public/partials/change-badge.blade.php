@php
    $changePct = $changePct ?? null;
    $compact = $compact ?? false;
    $chipClass = 'inline-flex items-center gap-1 rounded-md font-semibold tabular-nums whitespace-nowrap '.($compact ? 'px-1.5 py-0.5 text-[11px]' : 'px-2 py-1 text-xs');
@endphp
@if($changePct === null)
    <span class="{{ $chipClass }} bg-slate-100 text-slate-500">Belum ada pembanding</span>
@elseif(abs($changePct) < 0.05)
    <span class="{{ $chipClass }} bg-slate-100 text-slate-600">Stabil {{ number_format($changePct, 2, ',', '.') }}%</span>
@elseif($changePct > 0)
    <span class="{{ $chipClass }} bg-rose-50 text-rose-700">
        <svg class="w-3 h-3" viewBox="0 0 12 12" fill="currentColor" aria-hidden="true"><path d="M6 2l4 5H2z"/></svg>
        Naik {{ number_format($changePct, 2, ',', '.') }}%
    </span>
@else
    <span class="{{ $chipClass }} bg-brand-50 text-brand-700">
        <svg class="w-3 h-3" viewBox="0 0 12 12" fill="currentColor" aria-hidden="true"><path d="M6 10L2 5h8z"/></svg>
        Turun {{ number_format(abs($changePct), 2, ',', '.') }}%
    </span>
@endif
