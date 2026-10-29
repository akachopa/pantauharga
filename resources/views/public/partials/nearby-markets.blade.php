@php
    $originLabels = [
        'lokasi' => 'dari lokasi Anda',
        'pasar' => 'dari pasar yang dipilih',
        'kabupaten' => 'dari pusat Kabupaten Bengkulu Tengah',
    ];
@endphp
<section id="pasar-terdekat" class="scroll-mt-20 space-y-4">
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-3 border-b border-slate-300 pb-3">
        <div>
            <h2 class="text-lg sm:text-xl font-bold text-slate-900 tracking-tight">Pasar Terdekat</h2>
            <p class="text-sm text-slate-500 mt-1">
                Harga resmi {{ $nearbyCommodity->name ?? 'komoditi' }} {{ $originLabels[$originSource] ?? '' }}.
            </p>
        </div>
        <div class="grid grid-cols-2 sm:flex sm:flex-wrap items-center gap-2">
            <form method="GET" action="{{ route('home') }}" class="flex items-center gap-2 min-w-0">
                @foreach(request()->except('nearby_commodity_id') as $key => $value)
                    @if(! is_array($value) && $value !== null && $value !== '')
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endif
                @endforeach
                <select name="nearby_commodity_id" onchange="this.form.submit()" class="form-field w-full border border-slate-300 rounded-md px-3 py-2 text-xs font-semibold text-slate-700 bg-white focus:border-brand-700 focus:ring-brand-700" aria-label="Pilih komoditi">
                    @foreach($commodities as $commodity)
                        <option value="{{ $commodity->id }}" @selected((string) $nearbyCommodityId === (string) $commodity->id)>{{ $commodity->name }}</option>
                    @endforeach
                </select>
            </form>
            <button type="button" id="useLocationBtn" class="inline-flex items-center justify-center gap-1.5 bg-white hover:bg-slate-50 disabled:opacity-60 text-slate-800 border border-slate-300 font-semibold text-xs px-3 py-2 rounded-md transition-colors">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                <span data-label class="truncate">Gunakan lokasi saya</span>
            </button>
        </div>
    </div>

    @if($nearbyMarkets->isNotEmpty())
        <div class="flex md:grid md:grid-cols-2 xl:grid-cols-3 gap-3 sm:gap-4 overflow-x-auto md:overflow-visible snap-x snap-mandatory no-scrollbar -mx-4 px-4 sm:mx-0 sm:px-0 pb-1">
            @foreach($nearbyMarkets as $market)
                <article class="snap-start shrink-0 w-[82%] sm:w-[60%] md:w-auto bg-white rounded-lg p-4 border border-slate-300 flex flex-col gap-2 {{ $market['is_cheapest'] ? 'border-l-4 border-l-brand-700' : '' }}">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <h3 class="font-bold text-slate-900">{{ $market['name'] }}</h3>
                            <p class="text-xs text-slate-500 mt-0.5">{{ $market['distance_km'] }} km · {{ $market['market_days'] ?: 'Hari pasar tidak tercatat' }}</p>
                        </div>
                        <div class="flex flex-col items-end gap-0.5 shrink-0">
                            @if($market['is_closest'])
                                <span class="text-[11px] font-semibold text-sky-800">Paling dekat</span>
                            @endif
                            @if($market['is_cheapest'])
                                <span class="text-[11px] font-semibold text-brand-800">Paling hemat</span>
                            @endif
                        </div>
                    </div>
                    <div class="text-xl font-bold text-slate-900 tabular-nums mt-auto">Rp {{ number_format($market['official_price'], 0, ',', '.') }}</div>
                    <p class="text-xs font-medium {{ $market['diff_pct'] > 0.5 ? 'text-rose-600' : ($market['diff_pct'] < -0.5 ? 'text-emerald-700' : 'text-slate-500') }}">
                        @if(abs($market['diff_pct']) < 0.5)
                            Setara rata-rata pasar terdekat
                        @elseif($market['diff_pct'] > 0)
                            {{ number_format($market['diff_pct'], 1, ',', '.') }}% lebih mahal dari rata-rata
                        @else
                            {{ number_format(abs($market['diff_pct']), 1, ',', '.') }}% lebih murah dari rata-rata
                        @endif
                    </p>
                </article>
            @endforeach
        </div>
    @else
        <div class="bg-white rounded-lg border border-slate-200 px-6 py-12 text-center">
            <p class="text-sm font-semibold text-slate-700">Belum ada harga resmi di pasar berkoordinat</p>
            <p class="text-xs text-slate-400 mt-1">Pilih komoditi atau tanggal lain yang sudah terverifikasi.</p>
        </div>
    @endif
</section>
