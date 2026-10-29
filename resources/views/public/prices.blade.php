@extends('layouts.public')

@section('title', 'Informasi Harga Pangan - PANTAU HARGA Bengkulu Tengah')

@section('content')
    @php
        $filterQuery = request()->except('period');
    @endphp

    @php
        $selectedDateLabel = \Carbon\Carbon::parse($selectedDate)->translatedFormat('l, d F Y');
        $risingCount = $commodityCards->filter(fn (array $card): bool => ($card['change_pct'] ?? 0) >= 0.05)->count();
        $fallingCount = $commodityCards->filter(fn (array $card): bool => ($card['change_pct'] ?? 0) <= -0.05)->count();
        $activeFilterCount = collect([$selectedKecamatan, $selectedMarket, $selectedCommodity])->filter()->count()
            + (request()->filled('date') ? 1 : 0);
    @endphp

    <section class="bg-brand-800 text-white border-b-4 border-amber-400">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
            <p class="text-xs sm:text-sm font-medium text-amber-300">{{ $selectedDateLabel }}</p>
            <h1 class="mt-2 text-2xl sm:text-4xl font-bold tracking-tight leading-tight max-w-3xl">
                Harga pangan hari ini di Bengkulu Tengah
            </h1>
            <p class="mt-3 text-sm sm:text-base text-emerald-50/85 max-w-xl">
                Rata-rata harga bahan pokok dari pasar yang dipantau petugas.
            </p>

            <div class="mt-6 sm:mt-8 flex flex-col sm:flex-row sm:items-end justify-between gap-6">
                <dl class="flex divide-x divide-white/20">
                    <div class="pr-5 sm:pr-8">
                        <dt class="text-xs text-emerald-100/80">Komoditi</dt>
                        <dd class="text-2xl sm:text-3xl font-bold tabular-nums">{{ $commodityCards->count() }}</dd>
                    </div>
                    <div class="px-5 sm:px-8">
                        <dt class="text-xs text-emerald-100/80">Naik</dt>
                        <dd class="text-2xl sm:text-3xl font-bold tabular-nums">{{ $risingCount }}</dd>
                    </div>
                    <div class="pl-5 sm:pl-8">
                        <dt class="text-xs text-emerald-100/80">Turun</dt>
                        <dd class="text-2xl sm:text-3xl font-bold tabular-nums">{{ $fallingCount }}</dd>
                    </div>
                </dl>

                <div class="flex items-center gap-5">
                    <a href="#harga-hari-ini" class="bg-amber-400 hover:bg-amber-300 text-slate-900 font-semibold text-sm px-5 py-2.5 rounded-md transition-colors">
                        Lihat harga
                    </a>
                    <a href="#lapor-harga" class="text-sm font-semibold text-white underline underline-offset-4 decoration-white/40 hover:decoration-white">
                        Lapor harga
                    </a>
                </div>
            </div>
        </div>
    </section>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-5 sm:pt-6 space-y-8 sm:space-y-10 pb-12">
        <form method="GET" action="{{ route('home') }}" class="bg-white rounded-lg border border-slate-300">
            <input type="hidden" name="period" value="{{ $chartPeriod }}">
            @if(request('lat'))
                <input type="hidden" name="lat" value="{{ request('lat') }}">
            @endif
            @if(request('lng'))
                <input type="hidden" name="lng" value="{{ request('lng') }}">
            @endif
            @if(request('nearby_commodity_id'))
                <input type="hidden" name="nearby_commodity_id" value="{{ request('nearby_commodity_id') }}">
            @endif

            <button type="button" id="filterToggle" aria-controls="filterPanel" aria-expanded="{{ $activeFilterCount > 0 ? 'true' : 'false' }}"
                    class="lg:hidden w-full flex items-center justify-between gap-3 px-4 py-3.5 text-left">
                <span class="flex items-center gap-2.5 min-w-0">
                    <span class="text-slate-500 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75"/></svg>
                    </span>
                    <span class="min-w-0">
                        <span class="block text-sm font-bold text-slate-900">Filter lokasi & tanggal</span>
                        <span class="block text-xs text-slate-500 truncate">
                            {{ $activeFilterCount > 0 ? $activeFilterCount.' filter aktif' : 'Semua kecamatan · Semua pasar' }}
                        </span>
                    </span>
                </span>
                <svg data-chevron class="w-5 h-5 text-slate-400 transition-transform {{ $activeFilterCount > 0 ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>

            <div id="filterPanel" class="{{ $activeFilterCount > 0 ? '' : 'hidden' }} lg:block px-4 pb-4 pt-3 lg:p-4 border-t border-slate-100 lg:border-t-0">
                @include('public.partials.filter-fields')
            </div>
        </form>

        @include('public.partials.insights')

        <section id="harga-hari-ini" class="scroll-mt-20 space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-3 border-b border-slate-300 pb-3">
                <div>
                    <h2 class="text-lg sm:text-xl font-bold text-slate-900 tracking-tight">Harga Hari Ini</h2>
                    <p class="text-sm text-slate-500 mt-1">Rata-rata harga terverifikasi per komoditi pada {{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('d M Y') }}.</p>
                </div>
                @if($commodityCards->count() > 4)
                    <label class="relative sm:w-64">
                        <span class="sr-only">Cari komoditi</span>
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                        <input type="search" id="commoditySearch" placeholder="Cari komoditi, mis. beras"
                               class="w-full bg-white border border-slate-300 rounded-md pl-9 pr-3 py-2 text-sm placeholder:text-slate-400 focus:border-brand-500 focus:ring-brand-500">
                    </label>
                @endif
            </div>

            @if($commodityCards->isNotEmpty())
                <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 sm:gap-4" id="commodityGrid">
                    @foreach($commodityCards as $card)
                        @include('public.partials.price-card', ['card' => $card])
                    @endforeach
                </div>
                <p id="commoditySearchEmpty" class="hidden text-center text-sm text-slate-500 py-8">Komoditi tidak ditemukan.</p>
            @else
                <div class="bg-white rounded-lg border border-slate-200 px-6 py-14 text-center">
                    <div class="mx-auto w-12 h-12 rounded-lg bg-slate-50 border border-slate-200 flex items-center justify-center text-slate-400 mb-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 21c-4.5-1.5-7.5-5.5-7.5-10.5C4.5 5.5 9 3 15 3c0 6 2.5 9.5-3 18z"/></svg>
                    </div>
                    <p class="text-sm font-semibold text-slate-700">Belum ada harga terverifikasi</p>
                    <p class="text-xs text-slate-400 mt-1">Coba ubah tanggal atau filter lokasi.</p>
                </div>
            @endif
        </section>

        @include('public.partials.nearby-markets')

        <section id="tren" class="scroll-mt-20 grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-5">
            <div class="lg:col-span-2 bg-white rounded-lg p-4 sm:p-5 border border-slate-300">
                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3 mb-4">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Tren</h2>
                        <p class="text-xs text-slate-500 mt-0.5 font-medium">Rata-rata harga harian (Rp) dari laporan yang telah diverifikasi.</p>
                    </div>
                    <div class="inline-flex shrink-0 border border-slate-300 rounded overflow-hidden self-start">
                        <a href="{{ route('home', array_merge($filterQuery, ['period' => 7])) }}"
                           class="px-3 py-1.5 text-xs font-semibold {{ $chartPeriod === 7 ? 'bg-slate-800 text-white' : 'bg-white text-slate-600 hover:bg-slate-50' }}">7 Hari</a>
                        <a href="{{ route('home', array_merge($filterQuery, ['period' => 30])) }}"
                           class="px-3 py-1.5 text-xs font-semibold border-l border-slate-300 {{ $chartPeriod === 30 ? 'bg-slate-800 text-white' : 'bg-white text-slate-600 hover:bg-slate-50' }}">30 Hari</a>
                    </div>
                </div>

                @if($hasChartData)
                    <p class="sm:hidden text-[11px] text-slate-400 mb-1">Geser grafik ke samping untuk melihat seluruh periode.</p>
                    <div class="w-full overflow-x-auto custom-scrollbar pt-1 -mx-1 px-1">
                        <svg viewBox="0 0 660 225" class="w-full h-56 min-w-[560px] sm:min-w-0" role="img" aria-label="Grafik tren harga komoditi">
                            <g stroke="#E2E8F0" stroke-width="1">
                                @foreach([25, 60, 95, 130, 165, 195] as $y)
                                    <line x1="55" y1="{{ $y }}" x2="640" y2="{{ $y }}" stroke-dasharray="3 3"/>
                                @endforeach
                            </g>
                            <g fill="#64748B" font-size="11" text-anchor="end" font-weight="500">
                                @foreach($priceChart['yLabels'] as $yLabel)
                                    <text x="48" y="{{ $yLabel['y'] }}">{{ $yLabel['text'] }}</text>
                                @endforeach
                            </g>
                            <g fill="#64748B" font-size="11" text-anchor="middle" font-weight="500">
                                @foreach($priceChart['labels'] as $label)
                                    <text x="{{ $label['x'] }}" y="215">{{ $label['text'] }}</text>
                                @endforeach
                            </g>
                            @foreach($priceChart['series'] as $series)
                                <polyline fill="none" stroke="{{ $series['color'] }}" stroke-width="2.5" stroke-linecap="round" points="{{ $series['points'] }}"/>
                            @endforeach
                        </svg>
                    </div>
                    <div class="flex items-center sm:justify-center flex-wrap gap-2 pt-3 border-t border-slate-100 mt-2">
                        @foreach($priceChart['series'] as $series)
                            <span class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-600">
                                <span class="w-3 h-0.5" style="background: {{ $series['color'] }}"></span>
                                {{ $series['name'] }}
                            </span>
                        @endforeach
                    </div>
                @else
                    <div class="py-14 text-center">
                        <div class="mx-auto w-12 h-12 rounded-lg bg-slate-50 border border-slate-200 flex items-center justify-center text-slate-400 mb-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 20V10m5 10V4m5 16v-7"/></svg>
                        </div>
                        <p class="text-sm font-semibold text-slate-700">Belum ada deret harga</p>
                        <p class="text-xs text-slate-400 mt-1">Tidak ada laporan terverifikasi pada rentang ini.</p>
                    </div>
                @endif
            </div>

            <aside class="bg-white rounded-lg p-4 sm:p-5 border border-slate-300">
                <h2 class="text-base font-bold text-slate-900">Sorotan</h2>
                <p class="text-xs text-slate-500 mt-0.5 mb-3">Perubahan dibanding hari pantau sebelumnya.</p>
                <dl class="divide-y divide-slate-200 border-t border-slate-200">
                    <div class="py-3 flex items-start justify-between gap-3">
                        <div>
                            <dt class="text-xs text-slate-500">Kenaikan tertinggi</dt>
                            <dd class="font-semibold text-slate-900 mt-0.5">{{ $highlights['up']['name'] ?? '—' }}</dd>
                        </div>
                        <dd class="shrink-0 text-right">
                            @if($highlights['up'])
                                @include('public.partials.change-badge', ['changePct' => $highlights['up']['change_pct']])
                            @else
                                <span class="text-xs text-slate-400">Tidak ada kenaikan.</span>
                            @endif
                        </dd>
                    </div>
                    <div class="py-3 flex items-start justify-between gap-3">
                        <div>
                            <dt class="text-xs text-slate-500">Penurunan tertinggi</dt>
                            <dd class="font-semibold text-slate-900 mt-0.5">{{ $highlights['down']['name'] ?? '—' }}</dd>
                        </div>
                        <dd class="shrink-0 text-right">
                            @if($highlights['down'])
                                @include('public.partials.change-badge', ['changePct' => $highlights['down']['change_pct']])
                            @else
                                <span class="text-xs text-slate-400">Tidak ada penurunan.</span>
                            @endif
                        </dd>
                    </div>
                    <div class="py-3 flex items-start justify-between gap-3">
                        <div>
                            <dt class="text-xs text-slate-500">Paling stabil</dt>
                            <dd class="font-semibold text-slate-900 mt-0.5">{{ $highlights['stable']['name'] ?? '—' }}</dd>
                        </div>
                        <dd class="shrink-0 text-right">
                            @if($highlights['stable'])
                                @include('public.partials.change-badge', ['changePct' => $highlights['stable']['change_pct']])
                            @else
                                <span class="text-xs text-slate-400">Belum ada pembanding.</span>
                            @endif
                        </dd>
                    </div>
                </dl>
            </aside>
        </section>

        <section id="tabel" class="scroll-mt-20 bg-white rounded-lg border border-slate-300 overflow-hidden">
            <div class="px-5 sm:px-6 py-4 border-b border-slate-100">
                <h2 class="text-base font-bold text-slate-900">Tabel</h2>
                <p class="text-xs text-slate-500 mt-0.5 font-medium">Harga per pasar pada {{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('d M Y') }}.</p>
            </div>

            <div class="hidden md:block overflow-x-auto custom-scrollbar">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-slate-100 text-xs font-semibold text-slate-500">
                            <th class="py-3 px-5 sticky top-0 bg-white">Komoditi</th>
                            <th class="py-3 px-4 sticky top-0 bg-white">Pasar</th>
                            <th class="py-3 px-4 sticky top-0 bg-white">Harga</th>
                            <th class="py-3 px-4 sticky top-0 bg-white">Perubahan</th>
                            <th class="py-3 px-4 sticky top-0 bg-white">Stok</th>
                            <th class="py-3 px-5 sticky top-0 bg-white">Waktu pantau</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-slate-700">
                        @forelse($priceItems as $row)
                            @php $change = (float) $row->price_change_pct; @endphp
                            <tr class="border-b border-slate-100 even:bg-slate-50/50 hover:bg-slate-50 transition-colors">
                                <td class="py-3.5 px-5">
                                    <div class="flex items-center gap-3">
                                        @include('prices.partials.commodity-icon', ['name' => $row->commodity_name])
                                        <div>
                                            <div class="font-bold text-slate-900">{{ $row->commodity_name }}</div>
                                            <div class="text-xs text-slate-400">{{ $row->base_unit }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4">{{ $row->market_name ?: '—' }}</td>
                                <td class="py-3.5 px-4 font-bold text-slate-900 tabular-nums">Rp {{ number_format($row->price, 0, ',', '.') }}</td>
                                <td class="py-3.5 px-4">@include('public.partials.change-badge', ['changePct' => $change, 'compact' => true])</td>
                                <td class="py-3.5 px-4 tabular-nums">{{ number_format($row->stock, 0, ',', '.') }}</td>
                                <td class="py-3.5 px-5 text-slate-500 whitespace-nowrap">{{ \Carbon\Carbon::parse($row->observed_at)->translatedFormat('d M Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-14 text-center">
                                    <p class="text-sm font-semibold text-slate-700">Tidak ada harga terverifikasi</p>
                                    <p class="text-xs text-slate-400 mt-1">Tidak ada baris untuk filter ini.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="md:hidden divide-y divide-slate-100">
                @forelse($priceItems as $row)
                    @php $change = (float) $row->price_change_pct; @endphp
                    <div class="px-4 py-3.5 flex items-start justify-between gap-3">
                        <div class="flex items-start gap-3 min-w-0">
                            @include('prices.partials.commodity-icon', ['name' => $row->commodity_name])
                            <div class="min-w-0">
                                <div class="font-bold text-slate-900 text-sm">{{ $row->commodity_name }}</div>
                                <div class="text-xs text-slate-400 mt-0.5">{{ $row->market_name ?: '—' }} · {{ $row->base_unit }}</div>
                                <div class="font-bold text-slate-900 text-base tabular-nums mt-1">Rp {{ number_format($row->price, 0, ',', '.') }}</div>
                            </div>
                        </div>
                        <div class="text-right shrink-0 space-y-1.5">
                            @include('public.partials.change-badge', ['changePct' => $change, 'compact' => true])
                            <div class="text-xs text-slate-400">Stok {{ number_format($row->stock, 0, ',', '.') }}</div>
                            <div class="text-[11px] text-slate-400">{{ \Carbon\Carbon::parse($row->observed_at)->translatedFormat('d M Y') }}</div>
                        </div>
                    </div>
                @empty
                    <div class="py-14 text-center">
                        <p class="text-sm font-semibold text-slate-700">Tidak ada harga terverifikasi</p>
                        <p class="text-xs text-slate-400 mt-1">Tidak ada baris untuk filter ini.</p>
                    </div>
                @endforelse
            </div>

            @if($priceItems->hasPages())
                <div class="px-5 py-3 border-t border-slate-100">
                    {{ $priceItems->links() }}
                </div>
            @endif
        </section>

        @include('public.partials.report-form')
    </div>

    <script>
        document.getElementById('filterToggle')?.addEventListener('click', function () {
            const panel = document.getElementById('filterPanel');
            const isOpen = ! panel.classList.toggle('hidden');
            this.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            this.querySelector('[data-chevron]').classList.toggle('rotate-180', isOpen);
        });

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

        document.getElementById('useLocationBtn')?.addEventListener('click', function () {
            if (! navigator.geolocation) {
                return;
            }
            const button = this;
            button.disabled = true;
            button.querySelector('[data-label]').textContent = 'Mencari lokasi…';
            navigator.geolocation.getCurrentPosition(function (position) {
                const url = new URL(window.location.href);
                url.searchParams.set('lat', position.coords.latitude.toFixed(6));
                url.searchParams.set('lng', position.coords.longitude.toFixed(6));
                window.location.href = url.toString();
            }, function () {
                button.disabled = false;
                button.querySelector('[data-label]').textContent = 'Lokasi tidak tersedia';
            });
        });
    </script>
@endsection
