@php
    $originLabels = [
        'lokasi' => 'dari lokasi Anda',
        'pasar' => 'dari pasar yang dipilih',
        'kabupaten' => 'dari pusat Kabupaten Bengkulu Tengah',
    ];
@endphp
<section id="pasar-terdekat" class="scroll-mt-20">
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-5">
        <div>
            <h2 class="text-xl sm:text-2xl font-extrabold tracking-tight text-ink">{{ $nearbyCommodity->name ?? 'Komoditi' }}</h2>
            <p class="text-sm text-slate-500 mt-1">
                Harga resmi {{ $nearbyCommodity->name ?? 'komoditi' }} {{ $originLabels[$originSource] ?? '' }}.
            </p>
        </div>
        <div class="grid grid-cols-2 sm:flex items-center gap-2">
            <form method="GET" action="{{ url()->current() }}" class="min-w-0">
                @foreach(request()->except('nearby_commodity_id') as $key => $value)
                    @if(! is_array($value) && $value !== null && $value !== '')
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endif
                @endforeach
                <select name="nearby_commodity_id" onchange="this.form.submit()" aria-label="Pilih komoditi"
                        class="form-field w-full bg-white border-0 ring-1 ring-slate-200 rounded-xl pl-3 pr-8 py-2.5 text-sm font-medium text-ink focus:ring-2 focus:ring-brand-500">
                    @foreach($commodities as $commodity)
                        <option value="{{ $commodity->id }}" @selected((string) $nearbyCommodityId === (string) $commodity->id)>{{ $commodity->name }}</option>
                    @endforeach
                </select>
            </form>
            <button type="button" data-use-location class="inline-flex items-center justify-center gap-1.5 bg-white hover:bg-slate-50 disabled:opacity-60 text-ink ring-1 ring-slate-200 font-semibold text-sm px-3.5 py-2.5 rounded-xl transition-colors">
                <svg class="w-4 h-4 shrink-0 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                <span data-label class="truncate">Lokasi saya</span>
            </button>
        </div>
    </div>

    @if($nearbyMarkets->isNotEmpty())
        <div class="flex md:grid md:grid-cols-2 xl:grid-cols-3 gap-3 sm:gap-4 overflow-x-auto md:overflow-visible snap-x snap-mandatory no-scrollbar -mx-4 px-4 sm:mx-0 sm:px-0">
            @foreach($nearbyMarkets as $market)
                <article class="snap-start shrink-0 w-[82%] sm:w-[60%] md:w-auto bg-white rounded-2xl ring-1 {{ $market['is_cheapest'] ? 'ring-brand-300' : 'ring-slate-200/70' }} p-5 flex flex-col">
                    <div class="flex flex-wrap gap-1.5 mb-3 min-h-[22px]">
                        @if($market['is_closest'])
                            <span class="text-[11px] font-semibold text-sky-700 bg-sky-50 px-2 py-0.5 rounded-md">Paling dekat</span>
                        @endif
                        @if($market['is_cheapest'])
                            <span class="text-[11px] font-semibold text-brand-700 bg-brand-50 px-2 py-0.5 rounded-md">Paling hemat</span>
                        @endif
                    </div>
                    <h3 class="font-bold text-ink">{{ $market['name'] }}</h3>
                    <p class="text-xs text-slate-500 mt-0.5">{{ $market['distance_km'] }} km · {{ $market['market_days'] ?: 'Hari pasar tidak tercatat' }}</p>
                    <div class="mt-4 font-display text-2xl font-extrabold text-ink tabular-nums tracking-tight">Rp {{ number_format($market['official_price'], 0, ',', '.') }}</div>
                    <p class="mt-1 text-xs font-medium {{ $market['diff_pct'] > 0.5 ? 'text-rose-600' : ($market['diff_pct'] < -0.5 ? 'text-brand-700' : 'text-slate-500') }}">
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
        @include('public.partials.empty-state', [
            'title' => 'Belum ada harga resmi di pasar berkoordinat',
            'message' => 'Pilih komoditi atau tanggal lain yang sudah terverifikasi.',
        ])
    @endif
</section>
