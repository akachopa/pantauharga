@extends('layouts.app')

@section('title', 'Laporan Pangan - PANTAU HARGA')
@section('breadcrumb', 'Laporan')

@section('content')
    @php
        $reportedPoints = (int) $items->max('reporting_points');
        $coverage = (int) min(100, round($reportedPoints / $pointTotal * 100));
        $chartCommodities = $items->take(4);
        $tabs = [
            'harga' => 'Harga & Stok',
            'kepatuhan' => 'Kepatuhan',
            'kebijakan' => 'Kebijakan',
        ];
        $filterQuery = request()->except('tab');
    @endphp

    <div>
        <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">Laporan</h1>
        <p class="text-xs sm:text-sm text-slate-500 mt-1">Ringkasan yang siap dibaca dan ditindaklanjuti.</p>
    </div>

    <form method="GET" action="{{ route('reports.index') }}" class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
        <input type="hidden" name="tab" value="{{ $activeTab }}">
        <input type="hidden" name="verified" value="0">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:flex items-center gap-2.5 flex-1">
            <div class="bg-white border border-slate-200 rounded-xl px-3.5 py-2 flex items-center gap-2 card-shadow">
                <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <input type="date" name="from" value="{{ $fromDate->toDateString() }}" onchange="this.form.submit()" class="form-plain text-xs sm:text-sm font-medium text-slate-800">
                <span class="text-slate-400 text-xs">–</span>
                <input type="date" name="to" value="{{ $toDate->toDateString() }}" onchange="this.form.submit()" class="form-plain text-xs sm:text-sm font-medium text-slate-800">
            </div>

            <div class="bg-white border border-slate-200 rounded-xl px-3.5 py-2 flex items-center gap-2 card-shadow">
                <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                <select name="kecamatan_id" onchange="this.form.submit()" class="form-plain text-xs sm:text-sm font-medium text-slate-800">
                    <option value="">Seluruh kecamatan</option>
                    @foreach($kecamatans as $k)
                        <option value="{{ $k->id }}" @selected($selectedKecamatan == $k->id)>{{ $k->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl px-3.5 py-2 flex items-center gap-2 card-shadow">
                <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                <select name="market_id" onchange="this.form.submit()" class="form-plain text-xs sm:text-sm font-medium text-slate-800">
                    <option value="">Semua pasar</option>
                    @foreach($markets as $m)
                        <option value="{{ $m->id }}" @selected($selectedMarket == $m->id)>{{ $m->name }}</option>
                    @endforeach
                </select>
            </div>

            <label class="bg-white border border-slate-200 rounded-xl px-3.5 py-2 flex items-center gap-2.5 card-shadow cursor-pointer">
                <input type="checkbox" name="verified" value="1" class="sr-only peer" onchange="this.form.submit()" @checked($onlyVerified)>
                <span class="w-9 h-5 rounded-full bg-slate-300 peer-checked:bg-brand-600 flex items-center px-0.5 justify-start peer-checked:justify-end transition-colors">
                    <span class="w-4 h-4 rounded-full bg-white shadow-xs"></span>
                </span>
                <span class="text-xs sm:text-sm font-semibold text-slate-800">Hanya terverifikasi</span>
            </label>
        </div>

        <div class="hidden sm:flex items-center gap-2.5">
            <a href="{{ route('reports.export.pdf', $filterQuery) }}" target="_blank" class="bg-white hover:bg-slate-50 text-brand-700 border border-brand-200 font-semibold text-xs sm:text-sm px-4 py-2 rounded-xl flex items-center gap-2 card-shadow transition-colors">
                <span class="px-1.5 py-0.5 rounded bg-emerald-50 text-[10px] font-bold">PDF</span>
                <span>PDF</span>
            </a>
            <a href="{{ route('reports.export.excel', $filterQuery) }}" class="bg-brand-600 hover:bg-brand-700 text-white font-semibold text-xs sm:text-sm px-4 py-2 rounded-xl flex items-center gap-2 shadow-xs transition-colors">
                <span class="px-1.5 py-0.5 rounded bg-brand-700 text-[10px] font-bold">XLS</span>
                <span>Excel</span>
            </a>
        </div>
    </form>

    <div class="border-b border-slate-200 flex items-center gap-6 overflow-x-auto custom-scrollbar">
        @foreach($tabs as $tabKey => $tabLabel)
            <a href="{{ route('reports.index', array_merge($filterQuery, ['tab' => $tabKey])) }}"
               class="pb-3 text-xs sm:text-sm font-semibold whitespace-nowrap border-b-2 transition-colors {{ $activeTab === $tabKey ? 'border-brand-600 text-brand-700' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
                {{ $tabLabel }}
            </a>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-stretch">
        <div class="order-2 lg:order-1 lg:col-span-8 bg-white rounded-2xl p-5 border border-slate-200/70 card-shadow flex flex-col justify-between">
            <div class="flex items-center justify-between gap-2 mb-4">
                <h3 class="text-base font-bold text-slate-900">Tren harga komoditi</h3>
                <select name="commodity_id" form="reports-commodity-form" onchange="this.form.submit()" class="form-field border border-slate-200 rounded-xl px-3 py-1.5 text-xs font-medium text-slate-700 bg-white">
                    <option value="">{{ $chartCommodities->count() }} komoditi dipilih</option>
                    @foreach($commodities as $c)
                        <option value="{{ $c->id }}" @selected($selectedCommodity == $c->id)>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="w-full overflow-x-auto">
                <svg viewBox="0 0 680 230" class="w-full h-56 min-w-[440px]">
                    <g stroke="#E2E8F0" stroke-width="1">
                        <line x1="50" y1="20" x2="660" y2="20" />
                        <line x1="50" y1="48" x2="660" y2="48" />
                        <line x1="50" y1="76" x2="660" y2="76" />
                        <line x1="50" y1="104" x2="660" y2="104" />
                        <line x1="50" y1="132" x2="660" y2="132" />
                        <line x1="50" y1="160" x2="660" y2="160" />
                        <line x1="50" y1="188" x2="660" y2="188" />
                    </g>
                    <g fill="#64748B" font-size="10.5" text-anchor="end" font-family="Inter, sans-serif">
                        @foreach($priceChart['yLabels'] as $yLabel)
                            <text x="43" y="{{ $yLabel['y'] }}">{{ $yLabel['text'] }}</text>
                        @endforeach
                    </g>
                    <g fill="#64748B" font-size="10.5" text-anchor="middle" font-family="Inter, sans-serif">
                        @foreach($priceChart['labels'] as $lbl)
                            <text x="{{ $lbl['x'] }}" y="208">{{ $lbl['text'] }}</text>
                        @endforeach
                    </g>
                    @foreach($priceChart['series'] as $series)
                        <polyline fill="none" stroke="{{ $series['color'] }}" stroke-width="2.2" points="{{ $series['points'] }}" />
                    @endforeach
                </svg>
            </div>

            <div class="flex items-center justify-center flex-wrap gap-4 sm:gap-6 pt-2 text-xs font-medium text-slate-600">
                @foreach($priceChart['series'] as $series)
                    <div class="flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full" style="background: {{ $series['color'] }}"></span>
                        <span>{{ $series['name'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="order-1 lg:order-2 lg:col-span-4 bg-white rounded-2xl p-5 border border-slate-200/70 card-shadow flex flex-col justify-between gap-4">
            <div class="flex items-center justify-between">
                <h3 class="text-base font-bold text-slate-900">Kelengkapan laporan</h3>
                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-brand-700 border border-emerald-200">Simulasi</span>
            </div>
            <div class="flex items-center justify-between gap-4 py-1">
                <div class="flex items-center gap-4">
                    <div class="relative w-24 h-24 shrink-0">
                        <svg viewBox="0 0 36 36" class="w-24 h-24 -rotate-90">
                            <circle cx="18" cy="18" r="15.5" fill="none" stroke="#E2E8F0" stroke-width="3.8"></circle>
                            <circle cx="18" cy="18" r="15.5" fill="none" stroke="#0D9462" stroke-width="3.8" stroke-dasharray="{{ $coverage }}, 100" stroke-linecap="round"></circle>
                        </svg>
                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                            <span class="text-lg font-bold text-slate-900 leading-none">{{ $coverage }}%</span>
                            <span class="text-[10px] text-slate-500 mt-0.5">terlapor</span>
                        </div>
                    </div>
                    <div>
                        <div class="text-base sm:text-lg font-bold text-slate-900">{{ $reportedPoints }} dari {{ $pointTotal }}</div>
                        <div class="text-xs text-slate-500">titik melapor</div>
                    </div>
                </div>
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </div>
            <div class="space-y-3 text-xs">
                @foreach(['Harga harian' => 92, 'Stok tersedia' => 85, 'Foto dan dokumen' => 78, 'Verifikasi petugas' => $coverage] as $label => $pct)
                    <div>
                        <div class="flex justify-between font-medium text-slate-700 mb-1"><span>{{ $label }}</span><span class="font-bold">{{ $pct }}%</span></div>
                        <div class="h-2 rounded-full bg-slate-100 overflow-hidden"><div class="h-full bg-brand-600 rounded-full" style="width:{{ $pct }}%"></div></div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl p-5 border border-slate-200/70 card-shadow space-y-4">
        <h3 class="text-base font-bold text-slate-900">Ringkasan komoditi</h3>

        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-100 text-xs font-semibold text-slate-500">
                        <th class="py-3 px-4 rounded-l-xl">Komoditi</th>
                        <th class="py-3 px-4 text-center">Minimum (Rp/kg)</th>
                        <th class="py-3 px-4 text-center">Rata-rata (Rp/kg)</th>
                        <th class="py-3 px-4 text-center">Maksimum (Rp/kg)</th>
                        <th class="py-3 px-4 text-center rounded-r-xl">Titik melapor</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @foreach($items as $row)
                        <tr class="hover:bg-slate-50/60">
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-amber-50 border border-amber-100 flex items-center justify-center text-base shrink-0">{{ $row->icon }}</div>
                                    <span class="font-bold text-slate-900">{{ $row->name }}</span>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-center text-slate-700">{{ number_format($row->min_price, 0, ',', '.') }}</td>
                            <td class="py-3.5 px-4 text-center font-bold text-slate-900">{{ number_format($row->avg_price, 0, ',', '.') }}</td>
                            <td class="py-3.5 px-4 text-center text-slate-700">{{ number_format($row->max_price, 0, ',', '.') }}</td>
                            <td class="py-3.5 px-4 text-center font-semibold text-slate-700">{{ max(1, $row->reporting_points) }} / {{ $pointTotal }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="sm:hidden divide-y divide-slate-100">
            @foreach($items as $row)
                <div class="py-3 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-amber-50 border border-amber-100 flex items-center justify-center text-base shrink-0">{{ $row->icon }}</div>
                        <span class="font-bold text-slate-900 text-sm">{{ $row->name }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-emerald-600">Rp {{ number_format($row->avg_price, 0, ',', '.') }}/kg</span>
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </div>
                </div>
            @endforeach
        </div>

        <a href="{{ route('reports.export.pdf', $filterQuery) }}" target="_blank" class="sm:hidden w-full bg-brand-600 hover:bg-brand-700 text-white font-semibold text-sm py-3 rounded-xl flex items-center justify-center gap-2 shadow-xs">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            <span>Unduh laporan</span>
        </a>
    </div>

    <div class="bg-white rounded-2xl p-5 border border-slate-200/70 card-shadow space-y-4">
        <h3 class="text-base font-bold text-slate-900">Selisih harga antar kecamatan</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-100 text-xs font-semibold text-slate-500">
                        <th class="py-3 px-4">Komoditi</th>
                        <th class="py-3 px-4">Kecamatan</th>
                        <th class="py-3 px-4 text-right">Rata-rata (Rp)</th>
                        <th class="py-3 px-4 text-right">Vs acuan</th>
                        <th class="py-3 px-4 text-right">Sebaran wilayah</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($regionalSpread as $row)
                        <tr class="hover:bg-slate-50/60">
                            <td class="py-3.5 px-4 font-bold text-slate-900">{{ $row->commodity_name }}</td>
                            <td class="py-3.5 px-4 text-slate-600">{{ $row->region_name }}</td>
                            <td class="py-3.5 px-4 text-right font-semibold">{{ number_format($row->avg_price, 0, ',', '.') }}</td>
                            <td class="py-3.5 px-4 text-right font-semibold {{ $row->vs_reference_pct > 0 ? 'text-rose-600' : 'text-emerald-700' }}">
                                {{ ($row->vs_reference_pct > 0 ? '+' : '').number_format($row->vs_reference_pct, 1, ',', '.') }}%
                            </td>
                            <td class="py-3.5 px-4 text-right {{ $row->region_spread_pct > 8 ? 'text-amber-700 font-bold' : 'text-slate-600' }}">
                                {{ number_format($row->region_spread_pct, 1, ',', '.') }}%
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-6 text-center text-slate-400">Belum ada cukup observasi untuk menghitung sebaran wilayah.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="flex items-center gap-2 text-xs text-slate-500">
        <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span>Stok terpantau bukan total persediaan kabupaten.</span>
    </div>

    <form id="reports-commodity-form" method="GET" action="{{ route('reports.index') }}" class="hidden">
        <input type="hidden" name="tab" value="{{ $activeTab }}">
        <input type="hidden" name="kecamatan_id" value="{{ $selectedKecamatan }}">
        <input type="hidden" name="market_id" value="{{ $selectedMarket }}">
        <input type="hidden" name="verified" value="{{ $onlyVerified ? '1' : '0' }}">
        <input type="hidden" name="from" value="{{ $fromDate->toDateString() }}">
        <input type="hidden" name="to" value="{{ $toDate->toDateString() }}">
    </form>
@endsection
