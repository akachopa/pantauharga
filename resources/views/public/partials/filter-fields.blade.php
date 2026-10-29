<div class="grid grid-cols-2 lg:grid-cols-5 gap-2.5 sm:gap-3">
    <div class="col-span-2 lg:col-span-1 bg-white border border-slate-300 rounded-md px-3 py-1.5 focus-within:border-brand-700">
        <label class="text-[11px] font-medium text-slate-500">Tanggal pantau</label>
        <input type="date" name="date" value="{{ $selectedDate }}" onchange="this.form.submit()"
               class="form-plain text-sm font-semibold text-slate-800 mt-0.5">
    </div>
    <div class="bg-white border border-slate-300 rounded-md px-3 py-1.5 focus-within:border-brand-700">
        <label class="text-[11px] font-medium text-slate-500">Kecamatan</label>
        <select name="kecamatan_id" onchange="this.form.submit()" class="form-plain text-sm font-semibold text-slate-800 mt-0.5 truncate">
            <option value="">Semua kecamatan</option>
            @foreach($kecamatans as $kecamatan)
                <option value="{{ $kecamatan->id }}" @selected((string) $selectedKecamatan === (string) $kecamatan->id)>{{ $kecamatan->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="bg-white border border-slate-300 rounded-md px-3 py-1.5 focus-within:border-brand-700">
        <label class="text-[11px] font-medium text-slate-500">Pasar</label>
        <select name="market_id" onchange="this.form.submit()" class="form-plain text-sm font-semibold text-slate-800 mt-0.5 truncate">
            <option value="">Semua pasar</option>
            @foreach($markets as $market)
                <option value="{{ $market->id }}" @selected((string) $selectedMarket === (string) $market->id)>{{ $market->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-span-2 lg:col-span-1 bg-white border border-slate-300 rounded-md px-3 py-1.5 focus-within:border-brand-700">
        <label class="text-[11px] font-medium text-slate-500">Komoditi</label>
        <select name="commodity_id" onchange="this.form.submit()" class="form-plain text-sm font-semibold text-slate-800 mt-0.5">
            <option value="">Semua komoditi</option>
            @foreach($commodities as $commodity)
                <option value="{{ $commodity->id }}" @selected((string) $selectedCommodity === (string) $commodity->id)>{{ $commodity->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-span-2 lg:col-span-1 flex items-stretch">
        @if($hasActiveFilters)
            <a href="{{ route('home') }}" class="w-full inline-flex items-center justify-center gap-1.5 text-brand-800 font-semibold text-sm px-3 py-2 underline underline-offset-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/></svg>
                Reset filter
            </a>
        @else
            <span class="w-full inline-flex items-center justify-center text-center text-xs text-slate-400 px-3.5 py-2.5">Menampilkan data terbaru</span>
        @endif
    </div>
</div>
