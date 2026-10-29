@extends('layouts.app')

@section('title', 'Input Harga dan Tabulasi - PANTAU HARGA')
@section('breadcrumb', 'Input Harga dan Tabulasi')

@section('content')
    @php
        $verifiedTabCount = $verifiedReports->where('status', 'approved')->count();
        $pendingTabCount = count($pendingReports);
        $coverageMarkets = $markets->take(2)->pluck('name')->filter()->implode(' dan ');
        $filterQuery = array_filter([
            'date' => $selectedDate,
            'market_id' => $selectedMarket,
            'commodity_id' => $selectedCommodity,
        ]);
        $showTable = in_array($activeTab, ['tabulasi', 'input', 'verifikasi'], true);
    @endphp

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">Input Harga dan Tabulasi</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Data lapangan yang rapi dan terverifikasi.</p>
        </div>
        <div class="hidden sm:flex items-center gap-2.5">
            <button type="button" onclick="openAddPriceModal()" class="bg-brand-600 hover:bg-brand-700 text-white font-semibold text-xs sm:text-sm px-4 py-2.5 rounded-xl flex items-center gap-2 shadow-xs transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"/></svg>
                <span>Input Data</span>
            </button>
            @if($canCrawlSp2kp)
                <button type="button" onclick="openSp2kpModal()" class="bg-white hover:bg-slate-50 text-brand-700 border border-brand-200 font-semibold text-xs sm:text-sm px-4 py-2.5 rounded-xl flex items-center gap-2 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    <span>Ambil SP2KP</span>
                </button>
            @endif
            <a href="{{ route('prices.import.template') }}" class="bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 font-semibold text-xs sm:text-sm px-4 py-2.5 rounded-xl flex items-center gap-2 transition-colors">
                <span>Template CSV</span>
            </a>
            <form action="{{ route('prices.import') }}" method="POST" enctype="multipart/form-data" class="flex items-center gap-2">
                @csrf
                <input type="file" name="csv_file" accept=".csv,text/csv" required class="text-xs max-w-[9rem]">
                <button type="submit" class="bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 font-semibold text-xs sm:text-sm px-4 py-2.5 rounded-xl flex items-center gap-2 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    <span>Impor</span>
                </button>
            </form>
        </div>
    </div>

    <div class="border-b border-slate-200 flex items-center gap-6 overflow-x-auto custom-scrollbar">
        <a href="{{ route('prices.index', array_merge($filterQuery, ['tab' => 'tabulasi'])) }}"
           class="tabulasi-subtab pb-3 text-xs sm:text-sm font-semibold whitespace-nowrap border-b-2 transition-colors {{ $activeTab === 'tabulasi' || $activeTab === 'input' ? 'border-brand-600 text-brand-700' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
            Tabulasi
        </a>
        <button type="button" onclick="openAddPriceModal()" class="tabulasi-subtab pb-3 text-xs sm:text-sm font-semibold whitespace-nowrap border-b-2 border-transparent text-slate-500 hover:text-slate-800">
            Input Data
        </button>
        <a href="{{ route('prices.index', array_merge($filterQuery, ['tab' => 'verifikasi'])) }}"
           class="tabulasi-subtab pb-3 text-xs sm:text-sm font-semibold whitespace-nowrap border-b-2 transition-colors {{ $activeTab === 'verifikasi' ? 'border-brand-600 text-brand-700' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
            Verifikasi
        </a>
        <a href="{{ route('prices.index', array_merge($filterQuery, ['tab' => 'koreksi'])) }}"
           class="tabulasi-subtab pb-3 text-xs sm:text-sm font-semibold whitespace-nowrap border-b-2 transition-colors {{ $activeTab === 'koreksi' ? 'border-brand-600 text-brand-700' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
            Koreksi
        </a>
    </div>

    @if($showTable)
        <form method="GET" action="{{ route('prices.index') }}" class="bg-white rounded-2xl p-4 border border-slate-200/70 card-shadow">
            <input type="hidden" name="tab" value="{{ $activeTab === 'input' ? 'tabulasi' : $activeTab }}">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                <div>
                    <label class="block text-[11px] font-semibold text-slate-500 mb-1">Tanggal</label>
                    <div class="flex items-center border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50">
                        <svg class="w-4 h-4 text-slate-400 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <input type="date" name="date" value="{{ $selectedDate }}" onchange="this.form.submit()" class="form-plain w-full text-xs sm:text-sm font-medium text-slate-800 bg-transparent">
                    </div>
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-slate-500 mb-1">Pasar</label>
                    <div class="flex items-center border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50">
                        <svg class="w-4 h-4 text-slate-400 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 9l2-4h14l2 4M4 9v10a1 1 0 001 1h14a1 1 0 001-1V9M9 19v-6h6v6"/></svg>
                        <select name="market_id" onchange="this.form.submit()" class="form-plain w-full text-xs sm:text-sm font-medium text-slate-800 bg-transparent">
                            <option value="">Semua Pasar</option>
                            @foreach($markets as $m)
                                <option value="{{ $m->id }}" @selected((string) $selectedMarket === (string) $m->id)>{{ $m->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-slate-500 mb-1">Komoditi</label>
                    <div class="flex items-center border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50">
                        <svg class="w-4 h-4 text-slate-400 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 21c-4.5-1.5-7.5-5.5-7.5-10.5C4.5 5.5 9 3 15 3c0 6 2.5 9.5-3 18z"/></svg>
                        <select name="commodity_id" onchange="this.form.submit()" class="form-plain w-full text-xs sm:text-sm font-medium text-slate-800 bg-transparent">
                            <option value="">Semua Komoditi</option>
                            @foreach($commodities as $c)
                                <option value="{{ $c->id }}" @selected((string) $selectedCommodity === (string) $c->id)>{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </form>

        @if($canCrawlSp2kp && is_array($sp2kpPreview) && ($sp2kpPreview['total'] ?? 0) > 0)
            <div class="bg-white rounded-2xl border border-brand-200/70 card-shadow overflow-hidden">
                <div class="px-5 py-3.5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Pratinjau crawling SP2KP</h3>
                        <p class="text-xs text-slate-500 mt-0.5">
                            {{ $sp2kpPreview['total'] }} baris terpetakan ({{ $sp2kpPreview['start_date'] }} – {{ $sp2kpPreview['end_date'] }}). Stok tidak tersedia di sumber ini.
                        </p>
                    </div>
                    <form action="{{ route('prices.sp2kp.import') }}" method="POST" onsubmit="return startSp2kpLoading(this, 'Menyimpan data SP2KP...')">
                        @csrf
                        <input type="hidden" name="start_date" value="{{ $sp2kpPreview['start_date'] }}">
                        <input type="hidden" name="end_date" value="{{ $sp2kpPreview['end_date'] }}">
                        <input type="hidden" name="point_id" value="{{ $sp2kpPreview['point_id'] }}">
                        <input type="hidden" name="kode_provinsi" value="{{ $sp2kpPreview['kode_provinsi'] }}">
                        <input type="hidden" name="kode_kab_kota" value="{{ $sp2kpPreview['kode_kab_kota'] }}">
                        <button type="submit" class="bg-brand-600 hover:bg-brand-700 disabled:opacity-70 text-white font-semibold text-xs sm:text-sm px-4 py-2.5 rounded-xl shadow-xs inline-flex items-center gap-2">
                            <svg class="hidden w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24" aria-hidden="true"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>
                            <span>Simpan ke tabulasi</span>
                        </button>
                    </form>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-100 text-xs font-semibold text-slate-500">
                                <th class="py-3 px-5">Tanggal</th>
                                <th class="py-3 px-4">Komoditi lokal</th>
                                <th class="py-3 px-4">Sumber SP2KP</th>
                                <th class="py-3 px-4 text-right">Harga (Rp)</th>
                                <th class="py-3 px-5 text-right">Perubahan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @foreach($sp2kpPreview['rows'] as $row)
                                <tr class="hover:bg-slate-50/80">
                                    <td class="py-3 px-5 text-slate-600">{{ $row['date'] }}</td>
                                    <td class="py-3 px-4 font-bold text-slate-900">{{ $row['local_name'] }}</td>
                                    <td class="py-3 px-4 text-slate-600">{{ $row['commodity_name'] }}</td>
                                    <td class="py-3 px-4 text-right font-semibold">{{ number_format($row['price'], 0, ',', '.') }}</td>
                                    <td class="py-3 px-5 text-right font-semibold {{ $row['change_pct'] > 0 ? 'text-rose-600' : ($row['change_pct'] < 0 ? 'text-emerald-700' : 'text-slate-500') }}">
                                        {{ ($row['change_pct'] > 0 ? '+' : '').number_format($row['change_pct'], 2, ',', '.') }}%
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if(($sp2kpPreview['unmapped'] ?? []) !== [])
                    <div class="px-5 py-3 border-t border-slate-100 text-xs text-slate-500">
                        Tidak dipetakan: {{ implode(', ', $sp2kpPreview['unmapped']) }}
                    </div>
                @endif
            </div>
        @endif

        <div class="bg-white rounded-2xl border border-slate-200/70 card-shadow overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between flex-wrap gap-2">
                <div class="flex items-center gap-3 text-xs font-semibold">
                    <span class="flex items-center gap-1.5 text-slate-700">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                        {{ $verifiedTabCount }} terverifikasi
                    </span>
                    <span class="text-slate-300">•</span>
                    <span class="flex items-center gap-1.5 text-slate-700">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                        {{ $pendingTabCount }} menunggu
                    </span>
                </div>
                <span class="text-[11px] text-slate-400">Klik status untuk verifikasi cepat</span>
            </div>

            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-100 text-xs font-semibold text-slate-500">
                            <th class="py-3.5 px-5 w-14">No</th>
                            <th class="py-3.5 px-4">Komoditi</th>
                            <th class="py-3.5 px-4">Satuan</th>
                            <th class="py-3.5 px-4">Harga (Rp)</th>
                            <th class="py-3.5 px-4">Stok</th>
                            <th class="py-3.5 px-4">Status</th>
                            <th class="py-3.5 px-5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @forelse($priceItems as $row)
                            @php $isVerified = $row->report_status === 'approved'; @endphp
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-3.5 px-5 text-slate-500 font-medium">{{ $priceItems->firstItem() + $loop->index }}</td>
                                <td class="py-3.5 px-4">
                                    <div class="flex items-center gap-3.5">
                                        @include('prices.partials.commodity-icon', ['name' => $row->commodity_name])
                                        <div>
                                            <span class="font-bold text-slate-900">{{ $row->commodity_name }}</span>
                                            @if(($row->version_no ?? 1) > 1 || !empty($row->previous_report_id))
                                                <div class="text-[10px] text-slate-400 mt-0.5">v{{ $row->version_no ?? 1 }}@if(!empty($row->previous_report_id)) · revisi dari #{{ $row->previous_report_id }}@endif</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4 text-slate-600">{{ $row->base_unit }}</td>
                                <td class="py-3.5 px-4 font-bold text-slate-900">Rp {{ number_format($row->price, 0, ',', '.') }}</td>
                                <td class="py-3.5 px-4 text-slate-700 font-medium">{{ number_format($row->stock, 0, ',', '.') }}</td>
                                <td class="py-3.5 px-4">
                                    <form action="{{ route('prices.verify', $row->report_id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="intent" value="quick">
                                        <input type="hidden" name="return_tab" value="{{ $activeTab === 'input' ? 'tabulasi' : $activeTab }}">
                                        <input type="hidden" name="date" value="{{ $selectedDate }}">
                                        <input type="hidden" name="market_id" value="{{ $selectedMarket }}">
                                        <input type="hidden" name="commodity_id" value="{{ $selectedCommodity }}">
                                        <input type="hidden" name="decision" value="{{ $isVerified ? 'returned' : 'approved' }}">
                                        <input type="hidden" name="note" value="Verifikasi cepat dari tabulasi">
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold transition-colors {{ $isVerified ? 'bg-emerald-50 text-emerald-700 border border-emerald-200/60' : 'bg-amber-50 text-amber-700 border border-amber-200/60' }}">
                                            <span class="w-2 h-2 rounded-full {{ $isVerified ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>
                                            <span>{{ $isVerified ? 'Terverifikasi' : 'Menunggu' }}</span>
                                        </button>
                                    </form>
                                </td>
                                <td class="py-3.5 px-5 text-right">
                                    <div class="inline-flex items-center gap-1 justify-end">
                                        @if(! empty($row->evidence_path))
                                            <a href="{{ route('prices.evidence', $row->report_id) }}" target="_blank" class="text-[11px] font-semibold text-brand-700 hover:text-brand-800 px-2 py-1 rounded-lg hover:bg-brand-50">Bukti</a>
                                        @endif
                                        <button type="button"
                                                onclick="openEditItemModal({{ $row->item_id }}, {{ \Illuminate\Support\Js::from($row->commodity_name) }}, {{ $row->price }}, {{ $row->stock }})"
                                                class="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100" aria-label="Edit {{ $row->commodity_name }}">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><circle cx="6" cy="12" r="1.8"/><circle cx="12" cy="12" r="1.8"/><circle cx="18" cy="12" r="1.8"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-slate-400">Tidak ada catatan harga ditemukan untuk filter ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="md:hidden divide-y divide-slate-100">
                @forelse($priceItems as $row)
                    @php $isVerified = $row->report_status === 'approved'; @endphp
                    <div class="p-4 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3.5">
                            @include('prices.partials.commodity-icon', ['name' => $row->commodity_name])
                            <div>
                                <div class="font-bold text-slate-900 text-sm">{{ $row->commodity_name }}</div>
                                <div class="text-xs text-slate-400">{{ $row->base_unit }}</div>
                                <div class="font-bold text-slate-900 text-base mt-0.5">Rp {{ number_format($row->price, 0, ',', '.') }}</div>
                            </div>
                        </div>
                        <div class="text-right flex flex-col items-end gap-1.5">
                            <div class="flex items-center gap-2">
                                <div class="text-xs text-slate-400">Stok <strong class="text-slate-800 font-bold block text-sm">{{ number_format($row->stock, 0, ',', '.') }}</strong></div>
                                <button type="button" onclick="openEditItemModal({{ $row->item_id }}, {{ \Illuminate\Support\Js::from($row->commodity_name) }}, {{ $row->price }}, {{ $row->stock }})" class="p-1 text-slate-400 hover:text-slate-700">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><circle cx="6" cy="12" r="1.8"/><circle cx="12" cy="12" r="1.8"/><circle cx="18" cy="12" r="1.8"/></svg>
                                </button>
                            </div>
                            <form action="{{ route('prices.verify', $row->report_id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="intent" value="quick">
                                <input type="hidden" name="return_tab" value="{{ $activeTab === 'input' ? 'tabulasi' : $activeTab }}">
                                <input type="hidden" name="date" value="{{ $selectedDate }}">
                                <input type="hidden" name="market_id" value="{{ $selectedMarket }}">
                                <input type="hidden" name="commodity_id" value="{{ $selectedCommodity }}">
                                <input type="hidden" name="decision" value="{{ $isVerified ? 'returned' : 'approved' }}">
                                <input type="hidden" name="note" value="Verifikasi cepat dari tabulasi">
                                <button type="submit" class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold {{ $isVerified ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $isVerified ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>
                                    <span>{{ $isVerified ? 'Terverifikasi' : 'Menunggu' }}</span>
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-slate-400 text-sm">Tidak ada catatan harga ditemukan untuk filter ini.</div>
                @endforelse
            </div>

            @if($priceItems->hasPages())
                <div class="px-5 py-3 border-t border-slate-100">
                    {{ $priceItems->appends(request()->query())->links() }}
                </div>
            @endif
        </div>

        <div class="sm:hidden space-y-2">
            <button type="button" onclick="openAddPriceModal()" class="w-full bg-brand-600 hover:bg-brand-700 text-white font-semibold text-sm py-3 rounded-xl shadow-md flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"/></svg>
                <span>Input Data</span>
            </button>
            @if($canCrawlSp2kp)
                <button type="button" onclick="openSp2kpModal()" class="w-full bg-white hover:bg-slate-50 text-brand-700 border border-brand-200 font-semibold text-sm py-3 rounded-xl flex items-center justify-center gap-2">
                    <span>Ambil SP2KP</span>
                </button>
            @endif
        </div>

        <div class="bg-emerald-50/90 border border-emerald-200/80 rounded-xl px-4 py-3 flex items-center gap-2.5 text-xs sm:text-sm font-semibold text-emerald-900">
            <svg class="w-5 h-5 text-brand-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>Cakupan input: {{ $coverageMarkets !== '' ? $coverageMarkets : 'Pasar Simulasi A dan B' }}</span>
        </div>
    @endif

    @if($activeTab === 'koreksi')
        @php
            $statusLabels = [
                'pending' => 'Menunggu tinjauan',
                'reviewed' => 'Ditinjau',
                'returned' => 'Perlu perbaikan',
                'dismissed' => 'Diabaikan',
            ];
        @endphp
        <div class="bg-white rounded-2xl border border-slate-200/70 card-shadow p-5 sm:p-6 space-y-4">
            <div class="border-b border-slate-100 pb-3">
                <h3 class="text-base font-bold text-slate-900">Laporan Koreksi Harga dari Masyarakat</h3>
                <p class="text-xs text-slate-500 mt-0.5">Masukan warga tidak mengubah harga resmi. Tinjau, minta perbaikan, atau buat revisi resmi.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @forelse($communityCorrections as $corr)
                    <div class="border border-slate-200/70 rounded-2xl p-4 space-y-3 bg-slate-50/40">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <div class="font-bold text-slate-900 text-sm">{{ $corr->reporter_name }}</div>
                                <div class="text-xs text-slate-400 mt-0.5">{{ $corr->phone ?: 'Tanpa telepon' }} • {{ $corr->created_at?->translatedFormat('d M Y H:i') }}</div>
                            </div>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $corr->status === 'reviewed' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200/60' : ($corr->status === 'dismissed' ? 'bg-slate-100 text-slate-600 border border-slate-200' : ($corr->status === 'returned' ? 'bg-rose-50 text-rose-700 border border-rose-200/60' : 'bg-amber-50 text-amber-700 border border-amber-200/60')) }}">
                                {{ $statusLabels[$corr->status] ?? $corr->status }}
                            </span>
                        </div>
                        <div class="text-xs bg-white p-3 rounded-xl border border-slate-100 space-y-1.5">
                            <div class="flex justify-between gap-3">
                                <span class="text-slate-500">Komoditi & Lokasi</span>
                                <span class="font-semibold text-slate-800 text-right">{{ $corr->commodity?->name }} ({{ $corr->market?->name }})</span>
                            </div>
                            <div class="flex justify-between gap-3">
                                <span class="text-slate-500">Harga dilaporkan</span>
                                <span class="font-bold text-rose-600">Rp {{ number_format($corr->reported_price, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between gap-3">
                                <span class="text-slate-500">Harga resmi saat lapor</span>
                                <span class="font-medium text-slate-700">{{ $corr->official_price !== null ? 'Rp '.number_format($corr->official_price, 0, ',', '.') : '—' }}</span>
                            </div>
                        </div>
                        @if($corr->note)
                            <p class="text-xs text-slate-600 italic">"{{ $corr->note }}"</p>
                        @endif
                        @if($corr->review_note)
                            <p class="text-xs text-slate-700">Catatan petugas: {{ $corr->review_note }}</p>
                        @endif
                        @php $corrAudits = ($correctionAuditLogs[$corr->id] ?? collect())->take(3); @endphp
                        @if($corrAudits->isNotEmpty())
                            <div class="text-[11px] text-slate-500 space-y-1 border-t border-slate-100 pt-2">
                                <div class="font-semibold text-slate-600">Jejak</div>
                                @foreach($corrAudits as $log)
                                    <div>{{ \Carbon\Carbon::parse($log->created_at)->translatedFormat('d M Y H:i') }} · {{ $log->action }}</div>
                                @endforeach
                            </div>
                        @endif
                        @if($corr->status === 'pending')
                            <div class="flex flex-wrap items-center gap-2">
                                <form method="POST" action="{{ route('prices.corrections.review', $corr) }}">
                                    @csrf
                                    <input type="hidden" name="status" value="reviewed">
                                    <button type="submit" class="px-3 py-1.5 rounded-xl text-xs font-semibold bg-brand-600 text-white hover:bg-brand-700">Tinjau</button>
                                </form>
                                <form method="POST" action="{{ route('prices.corrections.review', $corr) }}">
                                    @csrf
                                    <input type="hidden" name="status" value="returned">
                                    <input type="hidden" name="review_note" value="Mohon perbaiki data dan laporkan ulang.">
                                    <button type="submit" class="px-3 py-1.5 rounded-xl text-xs font-semibold bg-amber-50 border border-amber-200 text-amber-800 hover:bg-amber-100">Minta perbaikan</button>
                                </form>
                                <form method="POST" action="{{ route('prices.corrections.review', $corr) }}">
                                    @csrf
                                    <input type="hidden" name="status" value="dismissed">
                                    <button type="submit" class="px-3 py-1.5 rounded-xl text-xs font-semibold bg-white border border-slate-200 text-slate-700 hover:bg-slate-50">Abaikan</button>
                                </form>
                            </div>
                        @elseif($corr->status === 'reviewed')
                            <form method="POST" action="{{ route('prices.corrections.revise', $corr) }}">
                                @csrf
                                <button type="submit" class="px-3 py-1.5 rounded-xl text-xs font-semibold bg-brand-600 text-white hover:bg-brand-700">Buat revisi resmi</button>
                            </form>
                        @endif
                    </div>
                @empty
                    <p class="text-sm text-slate-400 md:col-span-2 text-center py-8">Belum ada laporan warga.</p>
                @endforelse
            </div>
        </div>
    @endif

    <div id="modal-add-price" class="hidden fixed inset-0 bg-slate-900/40 backdrop-blur-xs z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-100 space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-900">Input Harga & Stok Harian</h3>
                <button type="button" onclick="closeAddPriceModal()" class="text-slate-400 hover:text-slate-700 text-lg">&times;</button>
            </div>
            <form action="{{ route('prices.store') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                @csrf
                <input type="hidden" name="observed_at" value="{{ $selectedDate }}">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Nama Komoditi</label>
                    <select name="commodity_id" required class="form-field w-full border border-slate-200 rounded-xl px-3 py-2 text-sm">
                        <option value="">Pilih komoditi</option>
                        @foreach($commodities as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Titik Pantau</label>
                    <select name="point_id" required class="form-field w-full border border-slate-200 rounded-xl px-3 py-2 text-sm">
                        <option value="">Pilih titik pantau</option>
                        @foreach($points as $p)
                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Sumber data</label>
                    <select name="source_id" class="form-field w-full border border-slate-200 rounded-xl px-3 py-2 text-sm">
                        @foreach($dataSources as $source)
                            <option value="{{ $source->id }}">{{ $source->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Harga (Rp)</label>
                        <input type="number" name="price" value="35000" required min="1" step="0.01" class="form-field w-full border border-slate-200 rounded-xl px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Stok (kg)</label>
                        <input type="number" name="stock" value="65" required min="0" step="0.01" class="form-field w-full border border-slate-200 rounded-xl px-3 py-2 text-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Bukti (foto/PDF, opsional)</label>
                    <input type="file" name="evidence" accept=".jpg,.jpeg,.png,.pdf" class="form-field w-full border border-slate-200 rounded-xl px-3 py-2 text-sm">
                </div>
                <div class="pt-2 flex justify-end gap-2">
                    <button type="button" onclick="closeAddPriceModal()" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600">Batal</button>
                    <button type="submit" name="status" value="draft" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700">Simpan draft</button>
                    <button type="submit" name="status" value="submitted" class="px-4 py-2 rounded-xl bg-brand-600 text-white text-xs font-semibold">Submit</button>
                </div>
            </form>
        </div>
    </div>

    <div id="modal-sp2kp" class="hidden fixed inset-0 bg-slate-900/40 backdrop-blur-xs z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-100 space-y-4 relative overflow-hidden">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-900">Ambil data SP2KP Kemendag</h3>
                <button type="button" onclick="closeSp2kpModal()" class="text-slate-400 hover:text-slate-700 text-lg" data-sp2kp-close>&times;</button>
            </div>
            <p class="text-xs text-slate-500">Tarik harga harian Kabupaten Bengkulu Tengah, Provinsi Bengkulu, lalu simpan ke titik pantau yang dipilih.</p>
            <form action="{{ route('prices.sp2kp.preview') }}" method="POST" class="space-y-3" onsubmit="return startSp2kpLoading(this, 'Mengambil data SP2KP...')">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Dari</label>
                        <input type="date" name="start_date" value="{{ old('start_date', \Carbon\Carbon::parse($selectedDate)->subDays(6)->toDateString()) }}" required class="form-field w-full border border-slate-200 rounded-xl px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Sampai</label>
                        <input type="date" name="end_date" value="{{ old('end_date', $selectedDate) }}" required class="form-field w-full border border-slate-200 rounded-xl px-3 py-2 text-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Titik pantau tujuan</label>
                    <select name="point_id" required class="form-field w-full border border-slate-200 rounded-xl px-3 py-2 text-sm">
                        <option value="">Pilih titik pantau</option>
                        @foreach($points as $p)
                            <option value="{{ $p->id }}" @selected((string) old('point_id', data_get($sp2kpPreview, 'point_id')) === (string) $p->id)>{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
                <input type="hidden" name="kode_provinsi" value="{{ config('services.sp2kp.province_code') }}">
                <input type="hidden" name="kode_kab_kota" value="{{ config('services.sp2kp.regency_code') }}">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Provinsi</label>
                        <div class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm font-semibold text-slate-800 bg-slate-50">{{ config('services.sp2kp.province_name') }}</div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Kab/kota</label>
                        <div class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm font-semibold text-slate-800 bg-slate-50">{{ config('services.sp2kp.regency_name') }}</div>
                    </div>
                </div>
                @if($errors->sp2kp->any())
                    <div class="text-xs text-rose-600 space-y-1">
                        @foreach($errors->sp2kp->all() as $message)
                            <p>{{ $message }}</p>
                        @endforeach
                    </div>
                @endif
                <div class="pt-2 flex justify-end gap-2">
                    <button type="button" onclick="closeSp2kpModal()" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600" data-sp2kp-close>Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-brand-600 hover:bg-brand-700 disabled:opacity-70 text-white text-xs font-semibold inline-flex items-center gap-2">
                        <svg class="hidden w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24" aria-hidden="true"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>
                        <span>Ambil data</span>
                    </button>
                </div>
            </form>
            <div id="sp2kp-loading" class="hidden absolute inset-0 z-10 bg-white/85 backdrop-blur-sm flex flex-col items-center justify-center gap-3 px-6 text-center" aria-live="polite" aria-busy="true">
                <span class="relative flex h-12 w-12 items-center justify-center">
                    <span class="absolute inline-flex h-full w-full rounded-full bg-brand-600/20 animate-ping"></span>
                    <svg class="relative w-8 h-8 animate-spin text-brand-600" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                    </svg>
                </span>
                <div>
                    <p id="sp2kp-loading-text" class="text-sm font-bold text-slate-800">Mengambil data SP2KP...</p>
                    <p class="text-[11px] text-slate-500 mt-0.5">Mohon tunggu, permintaan ke Kemendag sedang diproses.</p>
                </div>
            </div>
        </div>
    </div>

    <div id="modal-edit-item" class="hidden fixed inset-0 bg-slate-900/40 backdrop-blur-xs z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-100 space-y-4">
            <div class="flex items-center justify-between">
                <h3 id="edit-item-title" class="text-sm font-bold text-slate-900">Koreksi Data</h3>
                <button type="button" onclick="closeEditItemModal()" class="text-slate-400 hover:text-slate-700 text-lg">&times;</button>
            </div>
            <form id="form-edit-item" method="POST" class="space-y-3">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Harga (Rp)</label>
                    <input type="number" name="price" id="edit-price" required class="form-field w-full border border-slate-200 rounded-xl px-3 py-2 text-sm font-semibold">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Stok Tersedia</label>
                    <input type="number" name="stock" id="edit-stock" required class="form-field w-full border border-slate-200 rounded-xl px-3 py-2 text-sm font-semibold">
                </div>
                <div class="pt-2 flex justify-end gap-2">
                    <button type="button" onclick="closeEditItemModal()" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-brand-600 text-white text-xs font-semibold">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function openAddPriceModal() {
        document.getElementById('modal-add-price').classList.remove('hidden');
    }
    function closeAddPriceModal() {
        document.getElementById('modal-add-price').classList.add('hidden');
    }
    function openEditItemModal(id, name, price, stock) {
        document.getElementById('form-edit-item').action = '/prices/' + id;
        document.getElementById('edit-item-title').textContent = 'Koreksi Data — ' + name;
        document.getElementById('edit-price').value = price;
        document.getElementById('edit-stock').value = stock;
        document.getElementById('modal-edit-item').classList.remove('hidden');
    }
    function closeEditItemModal() {
        document.getElementById('modal-edit-item').classList.add('hidden');
    }
    function openSp2kpModal() {
        document.getElementById('modal-sp2kp')?.classList.remove('hidden');
    }
    function closeSp2kpModal() {
        if (document.body.dataset.sp2kpLoading === '1') {
            return;
        }
        document.getElementById('modal-sp2kp')?.classList.add('hidden');
    }
    function startSp2kpLoading(form, message) {
        if (document.body.dataset.sp2kpLoading === '1') {
            return false;
        }
        document.body.dataset.sp2kpLoading = '1';
        const overlay = document.getElementById('sp2kp-loading');
        const text = document.getElementById('sp2kp-loading-text');
        if (text && message) {
            text.textContent = message;
        }
        overlay?.classList.remove('hidden');
        document.getElementById('modal-sp2kp')?.classList.remove('hidden');
        form.querySelectorAll('button').forEach(function (button) {
            button.disabled = true;
        });
        const spinner = form.querySelector('svg');
        if (spinner) {
            spinner.classList.remove('hidden');
        }
        document.querySelectorAll('[data-sp2kp-close]').forEach(function (button) {
            button.disabled = true;
            button.classList.add('opacity-40', 'pointer-events-none');
        });
        return true;
    }
    @if($activeTab === 'input')
        openAddPriceModal();
    @endif
    @if($errors->sp2kp->any())
        openSp2kpModal();
    @endif
</script>
@endpush
