@php
    $fieldClass = 'relative bg-slate-50 rounded-xl ring-1 ring-slate-200 pl-10 pr-3 py-2 focus-within:ring-2 focus-within:ring-brand-500 focus-within:bg-white transition';
    $iconClass = 'absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none';
    $labelClass = 'block text-[11px] font-medium text-slate-500';
    $inputClass = 'form-plain text-sm font-semibold text-ink mt-0.5';
    $isLatestDate = ! request()->filled('date');
    $inline = $inline ?? false;
    $wideSpan = $inline ? 'col-span-2 lg:col-span-1' : 'col-span-2';
    $fieldClass .= $inline ? ' bg-white' : '';
@endphp
<div class="grid grid-cols-2 {{ $inline ? 'lg:grid-cols-[1.4fr_1fr_1fr_1.2fr]' : '' }} gap-2.5">
    <div class="{{ $wideSpan }} flex gap-2">
        <div class="flex-1 {{ $fieldClass }}">
            <svg class="{{ $iconClass }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
            <label class="{{ $labelClass }}">Tanggal pantau</label>
            <input type="date" name="date" value="{{ $selectedDate }}" onchange="this.form.submit()" class="{{ $inputClass }}">
        </div>
        <a href="{{ url()->current().'?'.http_build_query(\Illuminate\Support\Arr::except(request()->except('page'), ['date'])) }}"
           class="shrink-0 inline-flex items-center gap-1.5 px-3.5 rounded-xl text-xs font-semibold ring-1 transition-colors {{ $isLatestDate ? 'bg-brand-50 text-brand-700 ring-brand-200' : 'bg-white text-slate-600 ring-slate-200 hover:bg-slate-50' }}">
            @if($isLatestDate)
                <span class="w-1.5 h-1.5 rounded-full bg-brand-500"></span>
            @endif
            Terbaru
        </a>
    </div>
    <div class="{{ $fieldClass }}">
        <svg class="{{ $iconClass }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 6.75V15m6-6v8.25m.503 3.498l4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 00-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0z"/></svg>
        <label class="{{ $labelClass }}">Kecamatan</label>
        <select name="kecamatan_id" onchange="this.form.submit()" class="{{ $inputClass }} truncate">
            <option value="">Semua</option>
            @foreach($kecamatans as $kecamatan)
                <option value="{{ $kecamatan->id }}" @selected((string) $selectedKecamatan === (string) $kecamatan->id)>{{ $kecamatan->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="{{ $fieldClass }}">
        <svg class="{{ $iconClass }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
        <label class="{{ $labelClass }}">Pasar</label>
        <select name="market_id" onchange="this.form.submit()" class="{{ $inputClass }} truncate">
            <option value="">Semua</option>
            @foreach($markets as $market)
                <option value="{{ $market->id }}" @selected((string) $selectedMarket === (string) $market->id)>{{ $market->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="{{ $wideSpan }} {{ $fieldClass }}">
        <svg class="{{ $iconClass }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/></svg>
        <label class="{{ $labelClass }}">Komoditi</label>
        <select name="commodity_id" onchange="this.form.submit()" class="{{ $inputClass }}">
            <option value="">Semua komoditi</option>
            @foreach($commodities as $commodity)
                <option value="{{ $commodity->id }}" @selected((string) $selectedCommodity === (string) $commodity->id)>{{ $commodity->name }}</option>
            @endforeach
        </select>
    </div>
</div>
