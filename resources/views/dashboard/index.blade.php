@extends('layouts.app')

@section('title', 'PANTAU HARGA - Bengkulu Tengah Dashboard')
@section('breadcrumb', 'Dashboard')

@section('content')
    <div class="space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <h1 class="hidden lg:block text-3xl font-bold text-slate-900 tracking-tight">Dashboard</h1>
            <div class="flex items-center gap-2.5 flex-wrap">
                <a href="{{ route('prices.index', ['tab' => 'input']) }}" class="bg-brand-600 hover:bg-brand-700 text-white font-semibold text-xs sm:text-sm px-4 py-2.5 rounded-xl inline-flex items-center gap-2 shadow-xs transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"/></svg>
                    <span>Input Data Baru</span>
                </a>
                <a href="{{ route('reports.index') }}" class="bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 font-semibold text-xs sm:text-sm px-4 py-2.5 rounded-xl inline-flex items-center gap-2 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Lihat Laporan</span>
                </a>
            </div>
        </div>

        <form method="GET" action="{{ route('dashboard') }}" class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <input type="hidden" name="period" value="{{ $chartPeriod }}">
            <div class="flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-2xl bg-amber-50 border border-amber-200/60 flex items-center justify-center text-amber-500 shrink-0">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="5"/>
                        <path stroke="currentColor" stroke-width="2" stroke-linecap="round" d="M12 2v2m0 16v2M4.93 4.93l1.41 1.41m11.32 11.32l1.41 1.41M2 12h2m16 0h2M4.93 19.07l1.41-1.41m11.32-11.32l1.41-1.41"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg sm:text-xl font-bold text-slate-900">Selamat pagi, {{ $currentUser->name ?? 'Admin' }}</h2>
                    <p class="text-xs sm:text-sm text-slate-500">Pantau kondisi pangan, tentukan langkah tepat.</p>
                </div>
            </div>
            <div class="grid grid-cols-2 sm:flex items-center gap-2.5">
                <div class="bg-white border border-slate-200/90 rounded-xl px-3.5 py-2 min-w-[160px] card-shadow">
                    <div class="text-[10px] font-medium text-slate-400 uppercase tracking-wider">Pilih tanggal</div>
                    <input type="date" name="date" value="{{ $selectedDate }}" onchange="this.form.submit()"
                           class="form-plain text-xs sm:text-sm font-semibold text-slate-800 mt-0.5">
                </div>
                <div class="bg-white border border-slate-200/90 rounded-xl px-3.5 py-2 min-w-[180px] card-shadow">
                    <div class="text-[10px] font-medium text-slate-400 uppercase tracking-wider">Pilih wilayah</div>
                    <select name="kecamatan_id" onchange="this.form.submit()" class="form-plain text-xs sm:text-sm font-semibold text-slate-800 mt-0.5">
                        <option value="">Semua Kecamatan</option>
                        @foreach($kecamatans as $kec)
                            <option value="{{ $kec->id }}" @selected($selectedKecamatan == $kec->id)>{{ $kec->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
        <a href="{{ route('master.index', ['tab' => 'komoditi']) }}" class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/70 card-shadow hover:shadow-card-hover hover:border-brand-500 transition-all flex items-center justify-between group">
            <div class="flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 border border-emerald-100 group-hover:scale-105 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 21c-4.5-1.5-7.5-5.5-7.5-10.5C4.5 5.5 9 3 15 3c0 6 2.5 9.5-3 18z"/></svg>
                </div>
                <div>
                    <div class="text-2xl font-bold text-slate-900 leading-tight">{{ $totalCommodities }}</div>
                    <div class="text-xs text-slate-500 font-medium">Komoditi Aktif</div>
                </div>
            </div>
            <svg class="w-4 h-4 text-slate-300 group-hover:text-brand-600 group-hover:translate-x-0.5 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
        <a href="{{ route('master.index', ['tab' => 'pasar']) }}" class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/70 card-shadow hover:shadow-card-hover hover:border-sky-500 transition-all flex items-center justify-between group">
            <div class="flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center shrink-0 border border-sky-100 group-hover:scale-105 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <div>
                    <div class="text-2xl font-bold text-slate-900 leading-tight">{{ $totalPoints }}</div>
                    <div class="text-xs text-slate-500 font-medium">Titik Pantau</div>
                </div>
            </div>
            <svg class="w-4 h-4 text-slate-300 group-hover:text-sky-600 group-hover:translate-x-0.5 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
        <a href="{{ route('prices.index', ['tab' => 'verifikasi']) }}" class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/70 card-shadow hover:shadow-card-hover hover:border-brand-500 transition-all flex items-center justify-between group">
            <div class="flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-xl bg-emerald-50 text-brand-600 flex items-center justify-center shrink-0 border border-emerald-100 group-hover:scale-105 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <div class="text-2xl font-bold text-slate-900 leading-tight">{{ $verifiedCount }}</div>
                    <div class="text-xs text-slate-500 font-medium">Terverifikasi</div>
                </div>
            </div>
            <svg class="w-4 h-4 text-slate-300 group-hover:text-brand-600 group-hover:translate-x-0.5 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/70 card-shadow flex items-center justify-between">
            <div class="flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0 border border-indigo-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3v18h18M7 14l3-3 3 4 4-6"/></svg>
                </div>
                <div>
                    <div class="text-2xl font-bold text-slate-900 leading-tight">{{ number_format($foodIndex ?? 100, 1, ',', '.') }}</div>
                    <div class="text-xs text-slate-500 font-medium">Indeks Pangan (vs acuan)</div>
                </div>
            </div>
            <span class="text-[10px] font-bold px-2 py-1 rounded-full {{ ($foodIndex ?? 100) > 110 ? 'bg-rose-100 text-rose-700' : (($foodIndex ?? 100) < 95 ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600') }}">100 = acuan</span>
        </div>
        <a href="{{ route('actions.index') }}" class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/70 card-shadow hover:shadow-card-hover hover:border-rose-400 transition-all flex items-center justify-between group">
            <div class="flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0 border border-rose-100 group-hover:scale-105 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <div>
                    <div class="text-2xl font-bold text-rose-600 leading-tight">{{ $fluctuatingCount }}</div>
                    <div class="text-xs text-slate-500 font-medium">Perlu Perhatian</div>
                </div>
            </div>
            <svg class="w-4 h-4 text-slate-300 group-hover:text-rose-600 group-hover:translate-x-0.5 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <div class="lg:col-span-7 bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/70 card-shadow flex flex-col justify-between">
            <div>
                <div class="flex items-start justify-between gap-3 mb-4">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Tren Pergerakan Harga</h3>
                        <p class="text-xs text-slate-500 mt-0.5 font-medium">Perkembangan harga rata-rata komoditi utama di pasar Bengkulu Tengah.</p>
                    </div>
                    <div class="inline-flex bg-slate-100 p-1 rounded-xl shrink-0 border border-slate-200">
                        <a href="{{ route('dashboard', array_merge(request()->except('period'), ['period' => 7])) }}"
                           class="px-3 py-1 rounded-lg text-xs font-semibold transition-all {{ $chartPeriod === 7 ? 'bg-brand-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">7 Hari</a>
                        <a href="{{ route('dashboard', array_merge(request()->except('period'), ['period' => 30])) }}"
                           class="px-3 py-1 rounded-lg text-xs font-semibold transition-all {{ $chartPeriod === 30 ? 'bg-brand-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">30 Hari</a>
                    </div>
                </div>
                <div class="w-full overflow-x-auto custom-scrollbar pt-2">
                    <svg viewBox="0 0 660 225" class="w-full h-52 min-w-[440px]">
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
                            @foreach($priceChart['labels'] as $lbl)
                                <text x="{{ $lbl['x'] }}" y="215">{{ $lbl['text'] }}</text>
                            @endforeach
                        </g>
                        @foreach($priceChart['series'] as $series)
                            <polyline fill="none" stroke="{{ $series['color'] }}" stroke-width="2.5" stroke-linecap="round" points="{{ $series['points'] }}"/>
                        @endforeach
                    </svg>
                </div>
            </div>
            <div class="flex items-center justify-center gap-6 pt-3 border-t border-slate-100 text-xs font-semibold text-slate-600 mt-2">
                @foreach($priceChart['series'] as $series)
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full" style="background: {{ $series['color'] }}"></span>
                        <span>{{ $series['name'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="lg:col-span-5 bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/70 card-shadow flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Perlu Ditindaklanjuti</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Peringatan anomali harga dan pelaporan.</p>
                    </div>
                    <a href="{{ route('actions.index') }}" class="text-xs font-bold text-brand-600 hover:text-brand-700 flex items-center gap-1">
                        <span>Lihat Semua</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>
                <div class="space-y-3">
                    @foreach($alerts->take(3) as $alert)
                        <a href="{{ $alert['url'] }}" class="block rounded-2xl p-4 border {{ $alert['card_class'] }} hover:shadow-sm transition-all flex items-start justify-between gap-3 group">
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 rounded-xl {{ $alert['icon_class'] }} flex items-center justify-center shrink-0 mt-0.5 shadow-xs">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                </div>
                                <div>
                                    <div class="text-sm font-bold text-slate-900">{{ $alert['title'] }}</div>
                                    <p class="text-xs text-slate-600 mt-0.5 leading-relaxed">{{ $alert['description'] }}</p>
                                    <div class="flex items-center gap-2.5 pt-2">
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $alert['badge_class'] }}">{{ $alert['badge'] }}</span>
                                        <span class="text-[11px] text-slate-400 font-medium">{{ $alert['date'] }}</span>
                                    </div>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-slate-400 group-hover:translate-x-0.5 transition-all shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    @endforeach
                </div>
            </div>
            <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500 font-medium mt-3">
                <span>Status sistem: Sinkronisasi berjalan</span>
                <span class="font-bold text-emerald-700">100% Online</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <div class="lg:col-span-7 bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/70 card-shadow">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Aktivitas Terkini</h3>
                    <p class="text-xs text-slate-500 mt-0.5 font-medium">Log riwayat aksi operator dan verifikator di daerah.</p>
                </div>
                <a href="{{ route('prices.index') }}" class="text-xs font-bold text-brand-600 hover:text-brand-700 flex items-center gap-1">
                    <span>Lihat Semua</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-100 text-xs font-semibold text-slate-500">
                            <th class="py-2.5 px-3 rounded-l-lg">Waktu</th>
                            <th class="py-2.5 px-3">Aktivitas</th>
                            <th class="py-2.5 px-3">Komoditi</th>
                            <th class="py-2.5 px-3">Lokasi</th>
                            <th class="py-2.5 px-3 rounded-r-lg">Petugas</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
                        @forelse($recentActivities as $act)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-3 px-3 whitespace-nowrap text-slate-400">{{ $act['time'] }}</td>
                                <td class="py-3 px-3 whitespace-nowrap font-bold text-slate-900">
                                    <span class="inline-block w-2 h-2 rounded-full {{ $act['dot'] }} mr-2"></span>{{ $act['activity'] }}
                                </td>
                                <td class="py-3 px-3 font-semibold text-slate-800">{{ $act['commodity'] }}</td>
                                <td class="py-3 px-3 text-slate-600">{{ $act['location'] }}</td>
                                <td class="py-3 px-3"><span class="px-2 py-0.5 rounded-md bg-slate-100 font-semibold">{{ $act['officer'] }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-6 text-center text-slate-400">Belum ada aktivitas tercatat.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="lg:col-span-5 bg-gradient-to-br from-emerald-50/90 via-teal-50/60 to-white rounded-2xl p-5 border border-emerald-200/70 card-shadow flex flex-col justify-between">
            <div class="space-y-3">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl bg-emerald-100 text-brand-700 flex items-center justify-center shrink-0 border border-emerald-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-brand-700">Rekomendasi Kebijakan</span>
                        <h3 class="text-base font-bold text-slate-900">{{ $policyPackage->name ?? 'Koordinasikan Pasokan Strategis' }}</h3>
                    </div>
                </div>
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed font-medium">
                    {{ $policyPackage->description ?? 'Lakukan koordinasi langsung dengan distributor utama untuk menjaga ketersediaan komoditi yang mengalami tekanan harga atau stok.' }}
                </p>
            </div>
            <div class="pt-5">
                <a href="{{ route('policies.index') }}" class="w-full bg-brand-600 hover:bg-brand-700 text-white font-semibold text-xs sm:text-sm py-3 px-4 rounded-xl shadow-xs flex items-center justify-center gap-2 transition-all">
                    <span>Tinjau Paket Kebijakan Terkait</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/70 card-shadow space-y-4">
        <div>
            <h3 class="text-base font-bold text-slate-900">Selisih harga antar kecamatan</h3>
            <p class="text-xs text-slate-500 mt-0.5">Perbandingan harga rata-rata terhadap harga acuan/HET.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-100 text-xs font-semibold text-slate-500">
                        <th class="py-2.5 px-3">Komoditi</th>
                        <th class="py-2.5 px-3">Kecamatan</th>
                        <th class="py-2.5 px-3 text-right">Rata-rata (Rp)</th>
                        <th class="py-2.5 px-3 text-right">Vs acuan</th>
                        <th class="py-2.5 px-3 text-right">Sebaran wilayah</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($regionalSpread as $row)
                        <tr class="hover:bg-slate-50/80">
                            <td class="py-3 px-3 font-bold text-slate-900">{{ $row->commodity_name }}</td>
                            <td class="py-3 px-3 text-slate-600">{{ $row->region_name }}</td>
                            <td class="py-3 px-3 text-right font-semibold text-slate-800">{{ number_format($row->avg_price, 0, ',', '.') }}</td>
                            <td class="py-3 px-3 text-right font-semibold {{ $row->vs_reference_pct > 0 ? 'text-rose-600' : 'text-emerald-700' }}">
                                {{ ($row->vs_reference_pct > 0 ? '+' : '').number_format($row->vs_reference_pct, 1, ',', '.') }}%
                            </td>
                            <td class="py-3 px-3 text-right {{ $row->region_spread_pct > 8 ? 'text-amber-700 font-bold' : 'text-slate-600' }}">
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
@endsection
