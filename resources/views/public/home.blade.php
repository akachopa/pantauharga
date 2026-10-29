@extends('layouts.public')

@section('title', 'Informasi Harga Pangan - PANTAU HARGA Bengkulu Tengah')

@section('content')
    @php
        $selectedDateLabel = \Carbon\Carbon::parse($selectedDate)->translatedFormat('l, d F Y');
        $risingCount = $commodityCards->filter(fn (array $card): bool => ($card['change_pct'] ?? 0) >= 0.05)->count();
        $fallingCount = $commodityCards->filter(fn (array $card): bool => ($card['change_pct'] ?? 0) <= -0.05)->count();
        $activeFilterCount = collect([$selectedKecamatan, $selectedMarket, $selectedCommodity])->filter()->count()
            + (request()->filled('date') ? 1 : 0);
        $selectedKecamatanName = $selectedKecamatan ? $kecamatans->firstWhere('id', $selectedKecamatan)?->name : null;
        $selectedMarketName = $selectedMarket ? $markets->firstWhere('id', $selectedMarket)?->name : null;
        $selectedCommodityName = $selectedCommodity ? $commodities->firstWhere('id', $selectedCommodity)?->name : null;
        $baseFilterQuery = request()->except(['page']);
        $activeFilterChips = collect([
            ['label' => $selectedKecamatanName, 'remove' => 'kecamatan_id'],
            ['label' => $selectedMarketName, 'remove' => 'market_id'],
            ['label' => $selectedCommodityName, 'remove' => 'commodity_id'],
            ['label' => request()->filled('date') ? $shortDateLabel : null, 'remove' => 'date'],
        ])->filter(fn (array $chip): bool => $chip['label'] !== null);
        $quickCommodities = ($selectedCommodity ? $commodities : $commodityCards)
            ->map(fn ($commodity): array => ['id' => data_get($commodity, 'id'), 'name' => data_get($commodity, 'name')])
            ->sortByDesc(fn (array $commodity): bool => (string) $commodity['id'] === (string) $selectedCommodity)
            ->take(8)
            ->values();
        $heroStats = [
            ['label' => 'Komoditi', 'hint' => 'ada harga hari ini', 'value' => $commodityCards->count(), 'class' => 'text-ink', 'icon' => 'bg-slate-100 text-slate-600', 'path' => 'M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z'],
            ['label' => 'Pasar', 'hint' => 'aktif dipantau', 'value' => $markets->count(), 'class' => 'text-ink', 'icon' => 'bg-sky-50 text-sky-600', 'path' => 'M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72m-13.5 8.65h3.75a.75.75 0 00.75-.75V13.5a.75.75 0 00-.75-.75H6.75a.75.75 0 00-.75.75v3.75c0 .415.336.75.75.75z'],
            ['label' => 'Naik', 'hint' => 'dari hari sebelumnya', 'value' => $risingCount, 'class' => 'text-rose-600', 'icon' => 'bg-rose-50 text-rose-600', 'path' => 'M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941'],
            ['label' => 'Turun', 'hint' => 'dari hari sebelumnya', 'value' => $fallingCount, 'class' => 'text-brand-600', 'icon' => 'bg-brand-50 text-brand-600', 'path' => 'M2.25 6L9 12.75l4.286-4.286a11.948 11.948 0 014.306 6.43l.776 2.898m0 0l3.182-5.511m-3.182 5.51l-5.511-3.181'],
        ];
    @endphp

    <section class="relative bg-white border-b border-slate-200 overflow-hidden">
        <div aria-hidden="true" class="hidden lg:block absolute inset-y-0 right-0 w-[calc(50%-96px)] bg-brand-50/70 rounded-bl-[48px]"></div>
        <div class="relative max-w-6xl mx-auto px-4 sm:px-6 py-8 sm:py-12 grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
            <div class="lg:col-span-7 relative">
                <span class="inline-flex items-center gap-2 text-xs font-medium text-slate-600 bg-white ring-1 ring-slate-200 px-3 py-1.5 rounded-full">
                    <span class="w-1.5 h-1.5 rounded-full bg-brand-500"></span>
                    {{ $selectedDateLabel }}
                    @if($lastUpdated)
                        <span class="text-slate-300">|</span> diperbarui {{ $lastUpdated->format('H:i') }} WIB
                    @endif
                </span>

                <h1 class="mt-5 text-[30px] leading-[1.1] sm:text-[52px] sm:leading-[1.05] font-black tracking-tight text-ink">
                    Harga pangan hari ini<br>
                    <span class="text-brand-600">di Bengkulu Tengah</span>
                </h1>
                <p class="mt-4 text-base sm:text-lg text-slate-600 max-w-lg">
                    Rata-rata harga bahan pokok dari pasar yang dipantau petugas. Cek dulu sebelum berbelanja.
                </p>

                <dl class="mt-8 grid grid-cols-2 sm:grid-cols-4 gap-3">
                    @foreach($heroStats as $stat)
                        <div class="rounded-2xl bg-white ring-1 ring-slate-200 p-4">
                            <dt class="flex items-center gap-2 text-xs font-medium text-slate-500">
                                <span class="w-7 h-7 rounded-lg flex items-center justify-center {{ $stat['icon'] }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $stat['path'] }}"/></svg>
                                </span>
                                {{ $stat['label'] }}
                            </dt>
                            <dd class="mt-3 font-display text-3xl font-extrabold tabular-nums leading-none {{ $stat['class'] }}">{{ $stat['value'] }}</dd>
                            <dd class="mt-1 text-[11px] text-slate-400">{{ $stat['hint'] }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>

            <form method="GET" action="{{ route('home') }}" class="lg:col-span-5 relative rounded-3xl bg-white ring-1 ring-slate-200 shadow-[0_24px_48px_-24px_rgba(16,24,40,0.18)] overflow-hidden">
                @if(request('lat'))
                    <input type="hidden" name="lat" value="{{ request('lat') }}">
                @endif
                @if(request('lng'))
                    <input type="hidden" name="lng" value="{{ request('lng') }}">
                @endif
                @if(request('nearby_commodity_id'))
                    <input type="hidden" name="nearby_commodity_id" value="{{ request('nearby_commodity_id') }}">
                @endif

                <div class="bg-ink text-white px-5 py-4 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <span class="w-9 h-9 rounded-xl bg-white/10 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-brand-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                        </span>
                        <div class="min-w-0">
                            <p class="font-display font-bold leading-tight">Cek harga di lokasi Anda</p>
                            <p class="text-xs text-slate-300 truncate">
                                {{ $activeFilterCount > 0 ? $activeFilterCount.' filter aktif' : 'Semua kecamatan · semua pasar' }}
                            </p>
                        </div>
                    </div>
                    <button type="button" id="filterToggle" aria-controls="filterPanel" aria-expanded="{{ $activeFilterCount > 0 ? 'true' : 'false' }}"
                            class="lg:hidden shrink-0 w-9 h-9 rounded-xl bg-white/10 flex items-center justify-center" aria-label="Buka filter">
                        <svg data-chevron class="w-5 h-5 transition-transform {{ $activeFilterCount > 0 ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                </div>

                <div id="filterPanel" class="{{ $activeFilterCount > 0 ? '' : 'hidden' }} lg:block p-5 space-y-4">
                    @include('public.partials.filter-fields')

                    @if($quickCommodities->isNotEmpty())
                    <div>
                        <p class="text-xs font-medium text-slate-500 mb-2">Komoditi cepat</p>
                        <div class="flex gap-1.5 overflow-x-auto no-scrollbar -mx-5 px-5 [mask-image:linear-gradient(to_right,black_85%,transparent)]">
                            <a href="{{ route('home', \Illuminate\Support\Arr::except($baseFilterQuery, ['commodity_id'])) }}"
                               class="shrink-0 px-3 py-1.5 rounded-full text-xs font-semibold whitespace-nowrap transition-colors {{ $selectedCommodity ? 'bg-slate-100 text-slate-600 hover:bg-slate-200' : 'bg-ink text-white' }}">Semua</a>
                            @foreach($quickCommodities as $quickCommodity)
                                <a href="{{ route('home', array_merge($baseFilterQuery, ['commodity_id' => $quickCommodity['id']])) }}"
                                   class="shrink-0 px-3 py-1.5 rounded-full text-xs font-semibold whitespace-nowrap transition-colors {{ (string) $selectedCommodity === (string) $quickCommodity['id'] ? 'bg-ink text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">{{ $quickCommodity['name'] }}</a>
                            @endforeach
                        </div>
                    </div>

                    @endif

                    @if($activeFilterChips->isNotEmpty())
                        <div class="flex flex-wrap items-center gap-1.5 pt-1">
                            <span class="text-xs text-slate-500 mr-1">Aktif:</span>
                            @foreach($activeFilterChips as $chip)
                                <a href="{{ route('home', \Illuminate\Support\Arr::except($baseFilterQuery, [$chip['remove']])) }}"
                                   class="inline-flex items-center gap-1 pl-2.5 pr-1.5 py-1 rounded-full bg-brand-50 text-brand-800 ring-1 ring-brand-200 text-xs font-semibold hover:bg-brand-100" title="Hapus filter">
                                    {{ $chip['label'] }}
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                </a>
                            @endforeach
                            <a href="{{ route('home') }}" class="text-xs font-semibold text-slate-500 hover:text-ink underline underline-offset-2 ml-1">Reset semua</a>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-1">
                        <button type="submit" class="inline-flex items-center justify-center gap-2 bg-brand-600 hover:bg-brand-700 text-white font-semibold text-sm px-4 py-3 rounded-xl transition-colors">
                            Tampilkan harga
                        </button>
                        <button type="button" data-use-location data-location-url="{{ route('public.markets', $publicFilterQuery) }}"
                                class="inline-flex items-center justify-center gap-2 bg-white hover:bg-slate-50 disabled:opacity-60 text-ink ring-1 ring-slate-200 font-semibold text-sm px-4 py-3 rounded-xl transition-colors">
                            <svg class="w-4 h-4 text-brand-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                            <span data-label class="truncate">Pasar terdekat saya</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </section>

    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-10 sm:py-14 space-y-12 sm:space-y-16">
        <section id="harga-hari-ini" class="scroll-mt-20">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-5">
                <div>
                    <h2 class="text-xl sm:text-2xl font-extrabold tracking-tight text-ink">Harga Hari Ini</h2>
                    <p class="text-sm text-slate-500 mt-1">Rata-rata harga terverifikasi per komoditi, {{ $shortDateLabel }}.</p>
                </div>
                @if($commodityCards->count() > 4)
                    <label class="relative sm:w-72">
                        <span class="sr-only">Cari komoditi</span>
                        <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                        <input type="search" id="commoditySearch" placeholder="Cari komoditi, mis. beras"
                               class="w-full bg-white border-0 ring-1 ring-slate-200 rounded-xl pl-10 pr-3 py-2.5 text-sm placeholder:text-slate-400 focus:ring-2 focus:ring-brand-500">
                    </label>
                @endif
            </div>

            @if($commodityCards->isNotEmpty())
                <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 sm:gap-4" id="commodityGrid">
                    @foreach($commodityCards as $card)
                        @include('public.partials.price-card', ['card' => $card])
                    @endforeach
                </div>
                <p id="commoditySearchEmpty" class="hidden text-center text-sm text-slate-500 py-10">Komoditi tidak ditemukan.</p>
            @else
                @include('public.partials.empty-state', [
                    'title' => 'Belum ada harga terverifikasi',
                    'message' => 'Coba ubah tanggal atau filter lokasi.',
                ])
            @endif
        </section>

        @if($insights->isNotEmpty())
            <section>
                <div class="flex items-end justify-between gap-4 mb-5">
                    <div>
                        <h2 class="text-xl sm:text-2xl font-extrabold tracking-tight text-ink">Insight Harga</h2>
                        <p class="text-sm text-slate-500 mt-1">Ringkasan otomatis kondisi harga hari ini.</p>
                    </div>
                    <a href="{{ route('public.trends', $publicFilterQuery) }}#insight" class="shrink-0 text-sm font-semibold text-brand-700 hover:text-brand-800">Lihat semua &rarr;</a>
                </div>
                @include('public.partials.insight-list', ['insights' => $insights])
            </section>
        @endif

        <section>
            <h2 class="text-xl sm:text-2xl font-extrabold tracking-tight text-ink mb-5">Jelajahi informasi lain</h2>
            @include('public.partials.explore-links')
        </section>
    </div>

    <script>
        (function () {
            const searchInput = document.getElementById('commoditySearch');
            const grid = document.getElementById('commodityGrid');
            const emptyState = document.getElementById('commoditySearchEmpty');
            if (! searchInput || ! grid) {
                return;
            }
            searchInput.addEventListener('input', function () {
                const keyword = searchInput.value.trim().toLowerCase();
                let visibleCount = 0;
                grid.querySelectorAll('[data-commodity-name]').forEach(function (card) {
                    const isMatch = card.dataset.commodityName.includes(keyword);
                    card.classList.toggle('hidden', ! isMatch);
                    visibleCount += isMatch ? 1 : 0;
                });
                emptyState?.classList.toggle('hidden', visibleCount > 0);
            });
        })();
    </script>
@endsection
