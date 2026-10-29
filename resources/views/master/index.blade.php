@extends('layouts.app')

@section('title', 'Master Data - PANTAU HARGA')
@section('breadcrumb', 'Master Data')

@section('content')
    @php
        $masterTabs = [
            'pasar' => ['label' => 'Pasar & Titik Pantau', 'icon' => 'M3 9l2-4h14l2 4M4 9v10a1 1 0 001 1h14a1 1 0 001-1V9'],
            'mitra' => ['label' => 'Mitra', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
            'komoditi' => ['label' => 'Komoditi', 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
            'daerah' => ['label' => 'Daerah', 'icon' => 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z'],
        ];
        $addModalFn = match ($activeTab) {
            'mitra' => 'openAddBusinessModal()',
            'komoditi' => 'openAddCommodityModal()',
            'daerah' => 'openAddMarketModal()',
            default => 'openAddPointModal()',
        };
    @endphp

    <div>
        <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">Master Data</h1>
        <p class="text-xs sm:text-sm text-slate-500 mt-1">Satu referensi untuk seluruh pemantauan.</p>
    </div>

    <div class="border-b border-slate-200 flex items-center gap-6 overflow-x-auto custom-scrollbar">
        @foreach($masterTabs as $tabKey => $tab)
            <a href="{{ route('master.index', ['tab' => $tabKey]) }}"
               class="master-subtab pb-3 flex items-center gap-2 text-xs sm:text-sm font-semibold whitespace-nowrap border-b-2 transition-colors {{ $activeTab === $tabKey ? 'border-brand-600 text-brand-700' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $tab['icon'] }}"/></svg>
                <span>{{ $tab['label'] }}</span>
            </a>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <div class="{{ $activeTab === 'pasar' ? 'lg:col-span-8' : 'lg:col-span-12' }} space-y-4">
            <div class="grid grid-cols-3 gap-3 sm:gap-4">
                <div class="bg-white rounded-2xl p-3.5 sm:p-4 border border-slate-200/70 card-shadow flex items-center gap-3">
                    <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-full bg-emerald-50 text-brand-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                    </div>
                    <div>
                        <div class="text-lg sm:text-2xl font-bold text-slate-900 leading-tight">{{ $totalPoints }}</div>
                        <div class="text-[11px] sm:text-xs text-slate-500">Titik pantau</div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl p-3.5 sm:p-4 border border-slate-200/70 card-shadow flex items-center gap-3">
                    <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-full bg-emerald-50 text-brand-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <div>
                        <div class="text-lg sm:text-2xl font-bold text-slate-900 leading-tight">{{ $totalBusinesses }}</div>
                        <div class="text-[11px] sm:text-xs text-slate-500">Mitra</div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl p-3.5 sm:p-4 border border-slate-200/70 card-shadow flex items-center gap-3">
                    <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-full bg-lime-50 text-lime-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </div>
                    <div>
                        <div class="text-lg sm:text-2xl font-bold text-slate-900 leading-tight">{{ $totalCommodities }}</div>
                        <div class="text-[11px] sm:text-xs text-slate-500">Komoditi</div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="button" onclick="{{ $addModalFn }}" class="w-full sm:w-auto bg-brand-600 hover:bg-brand-700 text-white font-semibold text-xs sm:text-sm px-4 py-2.5 rounded-xl flex items-center justify-center gap-2 shadow-xs transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"/></svg>
                    <span>Tambah Data</span>
                </button>
            </div>

            @if($activeTab === 'pasar')
                <div id="master-info-banner" class="bg-lime-50 border border-lime-200/90 rounded-xl px-4 py-2.5 flex items-center justify-between gap-3 text-xs sm:text-sm text-emerald-900 font-medium">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Titik pantau dapat berada di luar pasar.</span>
                    </div>
                    <button type="button" onclick="document.getElementById('master-info-banner').remove()" class="text-slate-400 hover:text-slate-700 p-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form method="GET" action="{{ route('master.index') }}" class="flex flex-col sm:flex-row gap-3">
                    <input type="hidden" name="tab" value="pasar">
                    @if($selectedPoint)
                        <input type="hidden" name="point" value="{{ $selectedPoint->id }}">
                    @endif
                    <div class="flex-1 bg-white border border-slate-200 rounded-xl px-3.5 py-2 flex items-center">
                        <svg class="w-4 h-4 text-slate-400 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama titik pantau, pasar, atau operator..." class="form-plain w-full text-xs sm:text-sm text-slate-700 focus:outline-none border-0 p-0 bg-transparent">
                    </div>
                    <select name="kecamatan_id" onchange="this.form.submit()" class="form-field bg-white border border-slate-200 rounded-xl px-3.5 py-2 text-xs sm:text-sm font-medium text-slate-700 sm:w-48">
                        <option value="">Semua Kecamatan</option>
                        @foreach($kecamatans as $kec)
                            <option value="{{ $kec->id }}" @selected((string) $filterKecamatan === (string) $kec->id)>{{ $kec->name }}</option>
                        @endforeach
                    </select>
                </form>

                <div class="hidden md:block bg-white rounded-2xl border border-slate-200/70 card-shadow overflow-hidden">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-100 text-xs font-semibold text-slate-500">
                                <th class="py-3.5 px-4 w-12">No</th>
                                <th class="py-3.5 px-4">Nama Titik Pantau</th>
                                <th class="py-3.5 px-4">Kecamatan</th>
                                <th class="py-3.5 px-4">Lokasi Pasar</th>
                                <th class="py-3.5 px-4">Penanggung Jawab</th>
                                <th class="py-3.5 px-4">Status</th>
                                <th class="py-3.5 px-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs sm:text-sm">
                            @forelse($points as $index => $pt)
                                @php
                                    $isSelected = $selectedPoint && $selectedPoint->id === $pt->id;
                                    $pasarName = $pt->market->name ?? 'Di luar pasar';
                                    $picName = $pt->assignments->first()?->user?->name ?? 'Belum ditugaskan';
                                @endphp
                                <tr class="cursor-pointer transition-colors {{ $isSelected ? 'bg-emerald-50/70' : 'hover:bg-slate-50' }}">
                                    <td class="py-3.5 px-4 text-slate-500">
                                        <a href="{{ route('master.index', array_filter(['tab' => 'pasar', 'point' => $pt->id, 'search' => $search, 'kecamatan_id' => $filterKecamatan])) }}" class="block">{{ $index + 1 }}</a>
                                    </td>
                                    <td class="py-3.5 px-4 font-bold text-slate-900">
                                        <a href="{{ route('master.index', array_filter(['tab' => 'pasar', 'point' => $pt->id, 'search' => $search, 'kecamatan_id' => $filterKecamatan])) }}" class="block">{{ $pt->name }}</a>
                                    </td>
                                    <td class="py-3.5 px-4 text-slate-600">{{ $pt->region->name ?? 'Bengkulu Tengah' }}</td>
                                    <td class="py-3.5 px-4 text-slate-600">{{ $pasarName }}</td>
                                    <td class="py-3.5 px-4 text-slate-700">{{ $picName }}</td>
                                    <td class="py-3.5 px-4">
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $pt->is_active ? 'bg-lime-100 text-emerald-800' : 'bg-slate-100 text-slate-500' }}">{{ $pt->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                                    </td>
                                    <td class="py-3.5 px-4 text-right">
                                        <form action="{{ route('master.points.destroy', $pt->id) }}" method="POST" onsubmit="return confirm('Hapus titik pantau ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1 text-slate-400 hover:text-slate-700" title="Hapus">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="6" r="1.8"/><circle cx="12" cy="12" r="1.8"/><circle cx="12" cy="18" r="1.8"/></svg>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-6 text-center text-slate-400">Tidak ada data titik pantau.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="md:hidden space-y-3">
                    @forelse($points as $pt)
                        @php
                            $isSelected = $selectedPoint && $selectedPoint->id === $pt->id;
                            $pasarName = $pt->market->name ?? 'Di luar pasar';
                            $picName = $pt->assignments->first()?->user?->name ?? 'Belum ditugaskan';
                        @endphp
                        <a href="{{ route('master.index', array_filter(['tab' => 'pasar', 'point' => $pt->id, 'search' => $search, 'kecamatan_id' => $filterKecamatan])) }}"
                           class="block bg-white rounded-2xl p-4 border {{ $isSelected ? 'border-brand-500 ring-1 ring-brand-500/30' : 'border-slate-200/70' }} card-shadow space-y-3">
                            <div class="flex items-start justify-between">
                                <div>
                                    <div class="font-bold text-slate-900 text-sm">{{ $pt->name }}</div>
                                    <div class="text-xs text-slate-500 mt-0.5">{{ $pt->region->name ?? 'Bengkulu Tengah' }} • {{ $pasarName }}</div>
                                </div>
                                <span class="p-1 text-slate-400">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="6" r="1.8"/><circle cx="12" cy="12" r="1.8"/><circle cx="12" cy="18" r="1.8"/></svg>
                                </span>
                            </div>
                            <div class="flex items-center justify-between pt-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="px-2 py-0.5 rounded-md text-[11px] font-semibold {{ $pt->is_active ? 'bg-lime-100 text-emerald-800' : 'bg-slate-100 text-slate-500' }}">{{ $pt->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                                    <span class="text-xs text-slate-600 flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        {{ $picName }}
                                    </span>
                                </div>
                                <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 text-xs font-semibold flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    <span>Edit</span>
                                </span>
                            </div>
                        </a>
                    @empty
                        <div class="bg-white rounded-2xl p-6 border border-slate-200/70 text-center text-slate-400 text-sm">Tidak ada data titik pantau.</div>
                    @endforelse
                </div>
            @endif

            @if($activeTab === 'mitra')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @forelse($businesses as $biz)
                        <div class="bg-white rounded-2xl p-4 border border-slate-200/70 card-shadow space-y-3">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <h4 class="font-bold text-slate-900 text-sm">{{ $biz->name }}</h4>
                                    <p class="text-xs text-slate-500 mt-0.5">Pemilik: {{ $biz->owner_name }}</p>
                                </div>
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-lime-100 text-emerald-800">{{ $biz->is_verified ? 'Aktif' : 'Draft' }}</span>
                            </div>
                            <div class="text-xs text-slate-600 space-y-1">
                                <div>{{ $biz->business_type }}</div>
                                <div class="text-slate-500">{{ $biz->phone ?? '-' }} • {{ $biz->address ?? 'Bengkulu Tengah' }}</div>
                            </div>
                            <div class="pt-2 border-t border-slate-100 flex items-center justify-between">
                                <span class="text-[11px] text-slate-400 font-mono">{{ $biz->code }}</span>
                                <form action="{{ route('master.businesses.destroy', $biz->id) }}" method="POST" onsubmit="return confirm('Hapus mitra ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1 text-slate-400 hover:text-rose-600">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="6" r="1.8"/><circle cx="12" cy="12" r="1.8"/><circle cx="12" cy="18" r="1.8"/></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="bg-white rounded-2xl p-6 border border-slate-200/70 text-center text-slate-400 text-sm">Belum ada mitra.</div>
                    @endforelse
                </div>
            @endif

            @if($activeTab === 'komoditi')
                <div class="hidden md:block bg-white rounded-2xl border border-slate-200/70 card-shadow overflow-hidden">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-100 text-xs font-semibold text-slate-500">
                                <th class="py-3.5 px-4 w-12">No</th>
                                <th class="py-3.5 px-4">Komoditi</th>
                                <th class="py-3.5 px-4">Kategori</th>
                                <th class="py-3.5 px-4">Satuan</th>
                                <th class="py-3.5 px-4 text-right">Harga acuan</th>
                                <th class="py-3.5 px-4">Status</th>
                                <th class="py-3.5 px-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs sm:text-sm">
                            @foreach($commodities as $index => $c)
                                <tr class="hover:bg-slate-50">
                                    <td class="py-3.5 px-4 text-slate-500">{{ $index + 1 }}</td>
                                    <td class="py-3.5 px-4 font-bold text-slate-900">{{ $c->icon }} {{ $c->name }}</td>
                                    <td class="py-3.5 px-4 text-slate-600">{{ $c->category }}</td>
                                    <td class="py-3.5 px-4 text-slate-600">{{ $c->base_unit }}</td>
                                    <td class="py-3.5 px-4 text-right text-slate-700">Rp{{ number_format($c->reference_price, 0, ',', '.') }}</td>
                                    <td class="py-3.5 px-4">
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-lime-100 text-emerald-800">Aktif</span>
                                    </td>
                                    <td class="py-3.5 px-4 text-right">
                                        <form action="{{ route('master.commodities.destroy', $c->id) }}" method="POST" onsubmit="return confirm('Hapus komoditi ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1 text-slate-400 hover:text-slate-700">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="6" r="1.8"/><circle cx="12" cy="12" r="1.8"/><circle cx="12" cy="18" r="1.8"/></svg>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="md:hidden space-y-3">
                    @foreach($commodities as $c)
                        <div class="bg-white rounded-2xl p-4 border border-slate-200/70 card-shadow space-y-2">
                            <div class="flex items-start justify-between">
                                <div>
                                    <div class="font-bold text-slate-900 text-sm">{{ $c->icon }} {{ $c->name }}</div>
                                    <div class="text-xs text-slate-500 mt-0.5">{{ $c->category }} • {{ $c->base_unit }}</div>
                                </div>
                                <span class="px-2 py-0.5 rounded-md text-[11px] font-semibold bg-lime-100 text-emerald-800">Aktif</span>
                            </div>
                            <div class="text-sm font-semibold text-slate-800">Rp{{ number_format($c->reference_price, 0, ',', '.') }}</div>
                        </div>
                    @endforeach
                </div>
            @endif

            @if($activeTab === 'daerah')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-white rounded-2xl p-5 border border-slate-200/70 card-shadow space-y-3">
                        <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3">Wilayah Kecamatan</h3>
                        <div class="divide-y divide-slate-100">
                            @foreach($kecamatans as $kec)
                                <div class="py-2.5 flex items-center justify-between">
                                    <span class="text-sm font-semibold text-slate-800">{{ $kec->name }}</span>
                                    <span class="text-[11px] text-slate-400 font-mono">{{ $kec->code }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="bg-white rounded-2xl p-5 border border-slate-200/70 card-shadow space-y-3">
                        <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3">Standar Satuan</h3>
                        <div class="space-y-2">
                            <div class="p-3 bg-slate-50 rounded-xl flex justify-between items-center">
                                <div>
                                    <div class="text-sm font-bold text-slate-800">Kilogram (kg)</div>
                                    <div class="text-xs text-slate-500">Beras, gula, cabai, telur, daging</div>
                                </div>
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-lime-100 text-emerald-800">Utama</span>
                            </div>
                            <div class="p-3 bg-slate-50 rounded-xl flex justify-between items-center">
                                <div>
                                    <div class="text-sm font-bold text-slate-800">Liter (L)</div>
                                    <div class="text-xs text-slate-500">Minyak goreng curah & kemasan</div>
                                </div>
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-sky-100 text-sky-800">Cair</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        @if($activeTab === 'pasar')
            <div class="lg:col-span-4 bg-white rounded-2xl p-5 border border-slate-200/70 card-shadow space-y-4">
                @if($selectedPoint)
                    @php
                        $assignment = $selectedPoint->assignments->first();
                        $assignedUserId = $assignment?->user_id;
                        $assignedBusinessId = $assignment?->business_id;
                        $assignedSchedule = $assignment?->report_days ?: 'Harian';
                        $selectedPasar = $selectedPoint->market->name ?? 'Di luar pasar';
                    @endphp
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <h3 class="text-base font-bold text-slate-900">Penugasan</h3>
                        <span class="text-[11px] font-semibold text-brand-600 bg-brand-50 px-2 py-0.5 rounded-md">ID #{{ $selectedPoint->id }}</span>
                    </div>
                    <div>
                        <div class="text-base font-bold text-slate-900">{{ $selectedPoint->name }}</div>
                        <div class="text-xs text-slate-500 mt-0.5">{{ $selectedPoint->region->name ?? 'Bengkulu Tengah' }} • {{ $selectedPasar }}</div>
                        @if($assignment)
                            <div class="text-[11px] text-slate-500 mt-1.5">
                                Jadwal: <span class="font-semibold text-slate-700">{{ $assignment->report_days ?: 'Belum diatur' }}</span>
                                @if($assignment->business)
                                    • Mitra: <span class="font-semibold text-slate-700">{{ $assignment->business->name }}</span>
                                @endif
                            </div>
                        @endif
                    </div>
                    <form action="{{ route('master.points.update', $selectedPoint->id) }}" method="POST" class="space-y-3.5 pt-1">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="intent" value="penugasan">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Operator</label>
                            <select name="user_id" class="form-field w-full border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm font-medium text-slate-800 bg-white">
                                <option value="">Pilih operator</option>
                                @foreach($officers as $off)
                                    <option value="{{ $off->id }}" @selected((int) $assignedUserId === (int) $off->id)>{{ $off->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Mitra</label>
                            <select name="business_id" class="form-field w-full border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm font-medium text-slate-800 bg-white">
                                <option value="">Pilih mitra</option>
                                @foreach($businesses as $biz)
                                    <option value="{{ $biz->id }}" @selected((int) $assignedBusinessId === (int) $biz->id)>{{ $biz->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Jadwal Pemantauan</label>
                            <select name="schedule" class="form-field w-full border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm font-medium text-slate-800 bg-white">
                                <option value="Harian" @selected($assignedSchedule === 'Harian')>Harian</option>
                                <option value="Mingguan" @selected($assignedSchedule === 'Mingguan')>Mingguan</option>
                                <option value="2 Hari Sekali" @selected($assignedSchedule === '2 Hari Sekali')>2 Hari Sekali</option>
                            </select>
                        </div>
                        <button type="submit" class="w-full bg-brand-600 hover:bg-brand-700 text-white font-semibold text-sm py-2.5 rounded-xl shadow-xs transition-colors mt-2">
                            Simpan
                        </button>
                    </form>
                @else
                    <div class="text-sm text-slate-500">Pilih titik pantau untuk mengatur penugasan.</div>
                @endif
            </div>
        @endif
    </div>

    <div id="modal-add-market" class="hidden fixed inset-0 bg-slate-900/40 backdrop-blur-xs z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-100 space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-900">Tambah Pasar Baru</h3>
                <button type="button" onclick="closeAddMarketModal()" class="text-slate-400 hover:text-slate-700 text-lg">&times;</button>
            </div>
            <form action="{{ route('master.markets.store') }}" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Nama Pasar</label>
                    <input type="text" name="name" placeholder="Contoh: Pasar Pematang Tiga" required class="form-field w-full border border-slate-200 rounded-xl px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Kecamatan</label>
                    <select name="region_id" required class="form-field w-full border border-slate-200 rounded-xl px-3 py-2 text-sm">
                        @foreach($kecamatans as $kec)
                            <option value="{{ $kec->id }}">{{ $kec->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Hari Pasaran</label>
                    <input type="text" name="market_days" placeholder="Contoh: Selasa, Sabtu" class="form-field w-full border border-slate-200 rounded-xl px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Alamat Lengkap</label>
                    <textarea name="address" rows="2" class="form-field w-full border border-slate-200 rounded-xl px-3 py-2 text-sm"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Latitude</label>
                        <input type="number" step="any" name="latitude" placeholder="-3.73" class="form-field w-full border border-slate-200 rounded-xl px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Longitude</label>
                        <input type="number" step="any" name="longitude" placeholder="102.43" class="form-field w-full border border-slate-200 rounded-xl px-3 py-2 text-sm">
                    </div>
                </div>
                <div class="pt-2 flex justify-end gap-2">
                    <button type="button" onclick="closeAddMarketModal()" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-brand-600 text-white text-xs font-semibold">Tambah</button>
                </div>
            </form>
        </div>
    </div>

    <div id="modal-add-point" class="hidden fixed inset-0 bg-slate-900/40 backdrop-blur-xs z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-100 space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-900">Tambah Titik Pantau Baru</h3>
                <button type="button" onclick="closeAddPointModal()" class="text-slate-400 hover:text-slate-700 text-lg">&times;</button>
            </div>
            <form action="{{ route('master.points.store') }}" method="POST" class="space-y-3">
                @csrf
                <input type="hidden" name="point_type" value="pasar_tradisional">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Nama Titik Pantau</label>
                    <input type="text" name="name" placeholder="Contoh: Kios Pangan Makmur" required class="form-field w-full border border-slate-200 rounded-xl px-3 py-2 text-sm">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Kecamatan</label>
                        <select name="region_id" required class="form-field w-full border border-slate-200 rounded-xl px-3 py-2 text-sm">
                            @foreach($kecamatans as $kec)
                                <option value="{{ $kec->id }}">{{ $kec->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Lokasi Pasar</label>
                        <select name="market_id" class="form-field w-full border border-slate-200 rounded-xl px-3 py-2 text-sm">
                            <option value="">Di luar pasar</option>
                            @foreach($markets as $m)
                                <option value="{{ $m->id }}">{{ $m->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="pt-2 flex justify-end gap-2">
                    <button type="button" onclick="closeAddPointModal()" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-brand-600 text-white text-xs font-semibold">Tambah</button>
                </div>
            </form>
        </div>
    </div>

    <div id="modal-add-business" class="hidden fixed inset-0 bg-slate-900/40 backdrop-blur-xs z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-100 space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-900">Tambah Mitra Pelaksana Baru</h3>
                <button type="button" onclick="closeAddBusinessModal()" class="text-slate-400 hover:text-slate-700 text-lg">&times;</button>
            </div>
            <form action="{{ route('master.businesses.store') }}" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Nama Usaha / Toko</label>
                    <input type="text" name="name" placeholder="Contoh: UD Maju Pangan Jaya" required class="form-field w-full border border-slate-200 rounded-xl px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Nama Pemilik</label>
                    <input type="text" name="owner_name" placeholder="Contoh: H. Syamsul" required class="form-field w-full border border-slate-200 rounded-xl px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Bidang Usaha</label>
                    <input type="text" name="business_type" placeholder="Contoh: Grosir & Eceran Beras" required class="form-field w-full border border-slate-200 rounded-xl px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">No. Telepon / WhatsApp</label>
                    <input type="text" name="phone" placeholder="0812..." class="form-field w-full border border-slate-200 rounded-xl px-3 py-2 text-sm">
                </div>
                <div class="pt-2 flex justify-end gap-2">
                    <button type="button" onclick="closeAddBusinessModal()" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-brand-600 text-white text-xs font-semibold">Tambah</button>
                </div>
            </form>
        </div>
    </div>

    <div id="modal-add-commodity" class="hidden fixed inset-0 bg-slate-900/40 backdrop-blur-xs z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-100 space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-900">Tambah Komoditi Pangan</h3>
                <button type="button" onclick="closeAddCommodityModal()" class="text-slate-400 hover:text-slate-700 text-lg">&times;</button>
            </div>
            <form action="{{ route('master.commodities.store') }}" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Nama Komoditi</label>
                    <input type="text" name="name" placeholder="Contoh: Jagung Pipil" required class="form-field w-full border border-slate-200 rounded-xl px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Kategori</label>
                    <input type="text" name="category" placeholder="Contoh: Pangan Olahan" required class="form-field w-full border border-slate-200 rounded-xl px-3 py-2 text-sm">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Satuan Dasar</label>
                        <input type="text" name="base_unit" value="kg" required class="form-field w-full border border-slate-200 rounded-xl px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Icon Emoji</label>
                        <input type="text" name="icon" value="🌽" class="form-field w-full border border-slate-200 rounded-xl px-3 py-2 text-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Harga Acuan / HET (Rp)</label>
                    <input type="number" name="reference_price" placeholder="Contoh: 8500" required class="form-field w-full border border-slate-200 rounded-xl px-3 py-2 text-sm">
                </div>
                <div class="pt-2 flex justify-end gap-2">
                    <button type="button" onclick="closeAddCommodityModal()" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-brand-600 text-white text-xs font-semibold">Tambah</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function openAddMarketModal() { document.getElementById('modal-add-market').classList.remove('hidden'); }
    function closeAddMarketModal() { document.getElementById('modal-add-market').classList.add('hidden'); }
    function openAddPointModal() { document.getElementById('modal-add-point').classList.remove('hidden'); }
    function closeAddPointModal() { document.getElementById('modal-add-point').classList.add('hidden'); }
    function openAddBusinessModal() { document.getElementById('modal-add-business').classList.remove('hidden'); }
    function closeAddBusinessModal() { document.getElementById('modal-add-business').classList.add('hidden'); }
    function openAddCommodityModal() { document.getElementById('modal-add-commodity').classList.remove('hidden'); }
    function closeAddCommodityModal() { document.getElementById('modal-add-commodity').classList.add('hidden'); }
</script>
@endpush
