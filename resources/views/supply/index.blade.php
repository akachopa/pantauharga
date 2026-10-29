@extends('layouts.app')

@section('title', 'Rantai Pasok - PANTAU HARGA')
@section('breadcrumb', 'Rantai Pasok')

@section('content')
    @php
        $tabs = [
            'relasi' => 'Relasi Pasokan',
            'jadwal' => 'Jadwal Kiriman',
        ];
        $statusLabels = [
            'planned' => 'Direncanakan',
            'in_transit' => 'Dalam perjalanan',
            'delivered' => 'Terkirim',
            'cancelled' => 'Dibatalkan',
        ];
    @endphp

    <div class="space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">Rantai Pasok</h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">Relasi mitra ke titik pantau dan jadwal kiriman.</p>
            </div>
            @if($activeTab === 'relasi')
                <button type="button" onclick="document.getElementById('modal-add-link').classList.remove('hidden')" class="bg-brand-600 hover:bg-brand-700 text-white font-semibold text-xs sm:text-sm px-4 py-2.5 rounded-xl inline-flex items-center gap-2 shadow-xs">
                    Tambah relasi
                </button>
            @else
                <button type="button" onclick="document.getElementById('modal-add-shipment').classList.remove('hidden')" class="bg-brand-600 hover:bg-brand-700 text-white font-semibold text-xs sm:text-sm px-4 py-2.5 rounded-xl inline-flex items-center gap-2 shadow-xs">
                    Tambah jadwal
                </button>
            @endif
        </div>

        <div class="border-b border-slate-200 flex items-center gap-6 overflow-x-auto custom-scrollbar">
            @foreach($tabs as $tabKey => $tabLabel)
                <a href="{{ route('supply.index', ['tab' => $tabKey]) }}"
                   class="pb-3 text-xs sm:text-sm font-semibold whitespace-nowrap border-b-2 transition-colors {{ $activeTab === $tabKey ? 'border-brand-600 text-brand-700' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
                    {{ $tabLabel }}
                </a>
            @endforeach
        </div>

        @if($activeTab === 'relasi')
            <div class="bg-white rounded-2xl border border-slate-200/70 card-shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-100 text-xs font-semibold text-slate-500">
                                <th class="py-3 px-4">Mitra</th>
                                <th class="py-3 px-4">Titik pantau</th>
                                <th class="py-3 px-4">Komoditi</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="py-3 px-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @forelse($links as $link)
                                <tr class="hover:bg-slate-50/70">
                                    <td class="py-3.5 px-4 font-semibold text-slate-900">{{ $link->business?->name }}</td>
                                    <td class="py-3.5 px-4 text-slate-700">{{ $link->point?->name }}</td>
                                    <td class="py-3.5 px-4 text-slate-700">{{ $link->commodity?->name ?: 'Semua' }}</td>
                                    <td class="py-3.5 px-4">
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $link->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600' }}">
                                            {{ $link->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-right">
                                        <form action="{{ route('supply.links.destroy', $link->id) }}" method="POST" onsubmit="return confirm('Hapus relasi ini?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs font-semibold text-rose-600 hover:text-rose-700">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-slate-400">Belum ada relasi pasokan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <form method="GET" action="{{ route('supply.index') }}" class="flex items-center gap-2">
                <input type="hidden" name="tab" value="jadwal">
                <select name="status" onchange="this.form.submit()" class="form-field bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs font-medium text-slate-800">
                    <option value="">Semua status</option>
                    @foreach($statusLabels as $value => $label)
                        <option value="{{ $value }}" @selected($statusFilter === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </form>

            <div class="bg-white rounded-2xl border border-slate-200/70 card-shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-100 text-xs font-semibold text-slate-500">
                                <th class="py-3 px-4">Tanggal</th>
                                <th class="py-3 px-4">Mitra / Titik</th>
                                <th class="py-3 px-4">Jumlah</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="py-3 px-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @forelse($shipments as $ship)
                                <tr class="hover:bg-slate-50/70">
                                    <td class="py-3.5 px-4 text-slate-700">{{ $ship->scheduled_on?->translatedFormat('d M Y') }}</td>
                                    <td class="py-3.5 px-4">
                                        <div class="font-semibold text-slate-900">{{ $ship->link?->business?->name }}</div>
                                        <div class="text-xs text-slate-500">{{ $ship->link?->point?->name }} · {{ $ship->link?->commodity?->name ?: 'Umum' }}</div>
                                    </td>
                                    <td class="py-3.5 px-4 text-slate-700">{{ number_format((float) $ship->quantity, 0, ',', '.') }} {{ $ship->unit }}</td>
                                    <td class="py-3.5 px-4">
                                        <form action="{{ route('supply.shipments.update', $ship->id) }}" method="POST" class="inline-flex items-center gap-2">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="scheduled_on" value="{{ $ship->scheduled_on?->toDateString() }}">
                                            <input type="hidden" name="quantity" value="{{ $ship->quantity }}">
                                            <input type="hidden" name="unit" value="{{ $ship->unit }}">
                                            <input type="hidden" name="notes" value="{{ $ship->notes }}">
                                            <select name="status" onchange="this.form.submit()" class="form-field border border-slate-200 rounded-lg px-2 py-1 text-xs">
                                                @foreach($statusLabels as $value => $label)
                                                    <option value="{{ $value }}" @selected($ship->status === $value)>{{ $label }}</option>
                                                @endforeach
                                            </select>
                                        </form>
                                    </td>
                                    <td class="py-3.5 px-4 text-right">
                                        <form action="{{ route('supply.shipments.destroy', $ship->id) }}" method="POST" onsubmit="return confirm('Hapus jadwal ini?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs font-semibold text-rose-600 hover:text-rose-700">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-slate-400">Belum ada jadwal kiriman.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>

    <div id="modal-add-link" class="hidden fixed inset-0 bg-slate-900/40 backdrop-blur-xs z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-100 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-sm font-bold text-slate-900">Tambah Relasi Pasokan</h3>
                <button type="button" onclick="document.getElementById('modal-add-link').classList.add('hidden')" class="text-slate-400 hover:text-slate-700 text-lg">&times;</button>
            </div>
            <form action="{{ route('supply.links.store') }}" method="POST" class="space-y-3 text-xs">
                @csrf
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Mitra</label>
                    <select name="business_id" required class="form-field w-full border border-slate-200 rounded-xl px-3 py-2">
                        <option value="">Pilih mitra</option>
                        @foreach($businesses as $biz)
                            <option value="{{ $biz->id }}">{{ $biz->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Titik pantau</label>
                    <select name="point_id" required class="form-field w-full border border-slate-200 rounded-xl px-3 py-2">
                        <option value="">Pilih titik</option>
                        @foreach($points as $point)
                            <option value="{{ $point->id }}">{{ $point->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Komoditi (opsional)</label>
                    <select name="commodity_id" class="form-field w-full border border-slate-200 rounded-xl px-3 py-2">
                        <option value="">Semua / umum</option>
                        @foreach($commodities as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Catatan</label>
                    <textarea name="notes" rows="2" class="form-field w-full border border-slate-200 rounded-xl px-3 py-2"></textarea>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('modal-add-link').classList.add('hidden')" class="px-4 py-2 border border-slate-200 rounded-xl font-semibold">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-brand-600 text-white rounded-xl font-semibold">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <div id="modal-add-shipment" class="hidden fixed inset-0 bg-slate-900/40 backdrop-blur-xs z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-100 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-sm font-bold text-slate-900">Tambah Jadwal Kiriman</h3>
                <button type="button" onclick="document.getElementById('modal-add-shipment').classList.add('hidden')" class="text-slate-400 hover:text-slate-700 text-lg">&times;</button>
            </div>
            <form action="{{ route('supply.shipments.store') }}" method="POST" class="space-y-3 text-xs">
                @csrf
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Relasi pasokan</label>
                    <select name="supply_link_id" required class="form-field w-full border border-slate-200 rounded-xl px-3 py-2">
                        <option value="">Pilih relasi</option>
                        @foreach($links as $link)
                            <option value="{{ $link->id }}">{{ $link->business?->name }} → {{ $link->point?->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Tanggal</label>
                        <input type="date" name="scheduled_on" required value="{{ now()->toDateString() }}" class="form-field w-full border border-slate-200 rounded-xl px-3 py-2">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Status</label>
                        <select name="status" class="form-field w-full border border-slate-200 rounded-xl px-3 py-2">
                            @foreach($statusLabels as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Jumlah</label>
                        <input type="number" step="0.01" min="0.01" name="quantity" required class="form-field w-full border border-slate-200 rounded-xl px-3 py-2">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Satuan</label>
                        <input type="text" name="unit" value="kg" class="form-field w-full border border-slate-200 rounded-xl px-3 py-2">
                    </div>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Catatan</label>
                    <textarea name="notes" rows="2" class="form-field w-full border border-slate-200 rounded-xl px-3 py-2"></textarea>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('modal-add-shipment').classList.add('hidden')" class="px-4 py-2 border border-slate-200 rounded-xl font-semibold">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-brand-600 text-white rounded-xl font-semibold">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection
