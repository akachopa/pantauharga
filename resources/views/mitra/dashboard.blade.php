@extends('layouts.portal')

@section('title', 'Portal Mitra - PANTAU HARGA Bengkulu Tengah')
@section('portal-label', 'Portal Mitra Usaha')

@php
    $statusStyles = [
        'submitted' => ['label' => 'Menunggu verifikasi', 'class' => 'bg-amber-50 text-amber-800 ring-amber-200'],
        'approved' => ['label' => 'Terverifikasi', 'class' => 'bg-brand-50 text-brand-800 ring-brand-200'],
        'returned' => ['label' => 'Perlu perbaikan', 'class' => 'bg-orange-50 text-orange-800 ring-orange-200'],
        'rejected' => ['label' => 'Ditolak', 'class' => 'bg-rose-50 text-rose-700 ring-rose-200'],
        'draft' => ['label' => 'Draf', 'class' => 'bg-slate-100 text-slate-600 ring-slate-200'],
        'superseded' => ['label' => 'Diganti revisi', 'class' => 'bg-slate-100 text-slate-500 ring-slate-200'],
    ];
    $oldItems = old('items', [['commodity_id' => '', 'price' => '', 'stock' => '']]);
@endphp

@section('content')
    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-8 sm:py-10 space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-3">
            <div>
                <p class="text-sm font-semibold text-brand-700">{{ $businessName ?? 'Mitra usaha' }}</p>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-ink">Halo, {{ $user->name }}</h1>
                <p class="text-sm text-slate-500 mt-1">Harga yang Anda kirim tampil di informasi publik setelah diverifikasi petugas dinas.</p>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-3">
            @foreach([
                ['label' => 'Menunggu verifikasi', 'value' => $stats['pending'], 'class' => 'text-amber-700'],
                ['label' => 'Terverifikasi', 'value' => $stats['approved'], 'class' => 'text-brand-700'],
                ['label' => 'Perlu perbaikan / ditolak', 'value' => $stats['needs_fix'], 'class' => 'text-rose-600'],
            ] as $stat)
                <div class="bg-white rounded-2xl ring-1 ring-slate-200/70 p-4">
                    <p class="text-xs text-slate-500">{{ $stat['label'] }}</p>
                    <p class="mt-1 font-display text-2xl sm:text-3xl font-extrabold tabular-nums {{ $stat['class'] }}">{{ $stat['value'] }}</p>
                </div>
            @endforeach
        </div>

        <section class="bg-white rounded-2xl ring-1 ring-slate-200/70 p-5 sm:p-6">
            <h2 class="text-lg font-extrabold tracking-tight text-ink">Kirim harga hari ini</h2>
            <p class="text-sm text-slate-500 mt-0.5">Isi satu baris untuk setiap komoditi yang Anda jual.</p>

            @if($points->isEmpty())
                <div class="mt-5 rounded-xl bg-amber-50 ring-1 ring-amber-200 p-4 text-sm text-amber-900">
                    Akun Anda belum ditugaskan ke lokasi pantau. Hubungi admin dinas agar lokasi usaha Anda didaftarkan.
                </div>
            @else
                @if($errors->any())
                    <div class="mt-5 rounded-xl bg-rose-50 ring-1 ring-rose-200 p-4 text-sm text-rose-900">
                        <p class="font-semibold">Periksa kembali isian Anda:</p>
                        <ul class="mt-1 list-disc pl-5 space-y-0.5">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('mitra.reports.store') }}" enctype="multipart/form-data" class="mt-5 space-y-5">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="point_id" class="block text-sm font-medium text-slate-700">Lokasi usaha</label>
                            <select id="point_id" name="point_id" required class="mt-1 w-full rounded-lg border-slate-300 focus:border-brand-500 focus:ring-brand-500 text-sm">
                                @foreach($points as $point)
                                    <option value="{{ $point->id }}" @selected(old('point_id') == $point->id)>{{ $point->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="observed_at" class="block text-sm font-medium text-slate-700">Tanggal pantau</label>
                            <input id="observed_at" type="date" name="observed_at" value="{{ old('observed_at', now()->toDateString()) }}" max="{{ now()->toDateString() }}" required
                                   class="mt-1 w-full rounded-lg border-slate-300 focus:border-brand-500 focus:ring-brand-500 text-sm">
                        </div>
                    </div>

                    <div>
                        <div class="hidden sm:grid grid-cols-[1fr_180px_140px_40px] gap-3 px-1 pb-1 text-xs font-semibold text-slate-500">
                            <span>Komoditi</span><span>Harga (Rp)</span><span>Stok (opsional)</span><span></span>
                        </div>
                        <div id="item-rows" class="space-y-3">
                            @foreach($oldItems as $index => $item)
                                <div data-item-row class="grid grid-cols-2 sm:grid-cols-[1fr_180px_140px_40px] gap-2 sm:gap-3 rounded-xl sm:rounded-none bg-slate-50 sm:bg-transparent p-3 sm:p-0">
                                    <select name="items[{{ $index }}][commodity_id]" required aria-label="Komoditi" class="col-span-2 sm:col-span-1 w-full rounded-lg border-slate-300 focus:border-brand-500 focus:ring-brand-500 text-sm">
                                        <option value="">Pilih komoditi</option>
                                        @foreach($commodities as $commodity)
                                            <option value="{{ $commodity->id }}" @selected(($item['commodity_id'] ?? '') == $commodity->id)>{{ $commodity->name }} / {{ $commodity->base_unit }}</option>
                                        @endforeach
                                    </select>
                                    <input type="number" name="items[{{ $index }}][price]" value="{{ $item['price'] ?? '' }}" min="1" step="1" required placeholder="Harga" aria-label="Harga"
                                           class="w-full rounded-lg border-slate-300 focus:border-brand-500 focus:ring-brand-500 text-sm tabular-nums">
                                    <input type="number" name="items[{{ $index }}][stock]" value="{{ $item['stock'] ?? '' }}" min="0" step="any" placeholder="Stok" aria-label="Stok"
                                           class="w-full rounded-lg border-slate-300 focus:border-brand-500 focus:ring-brand-500 text-sm tabular-nums">
                                    <button type="button" data-remove-row class="col-span-2 sm:col-span-1 h-10 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 text-sm font-semibold" aria-label="Hapus baris">
                                        <span class="sm:hidden">Hapus baris</span><span class="hidden sm:inline">&times;</span>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                        <button type="button" id="add-item-row" class="mt-3 text-sm font-semibold text-brand-700 hover:text-brand-800">+ Tambah komoditi</button>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="notes" class="block text-sm font-medium text-slate-700">Catatan <span class="text-slate-400 font-normal">(opsional)</span></label>
                            <input id="notes" type="text" name="notes" value="{{ old('notes') }}" maxlength="255" placeholder="Misal: pasokan cabai dari Kepahiang terlambat"
                                   class="mt-1 w-full rounded-lg border-slate-300 focus:border-brand-500 focus:ring-brand-500 text-sm">
                        </div>
                        <div>
                            <label for="evidence" class="block text-sm font-medium text-slate-700">Foto nota / papan harga <span class="text-slate-400 font-normal">(opsional)</span></label>
                            <input id="evidence" type="file" name="evidence" accept=".jpg,.jpeg,.png,.pdf"
                                   class="mt-1 w-full text-sm text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-slate-100 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-slate-700">
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-2 border-t border-slate-100">
                        <p class="text-xs text-slate-500">Data dikirim sebagai laporan mitra dan ditinjau petugas sebelum dipublikasikan.</p>
                        <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white font-semibold text-sm px-6 py-2.5 rounded-lg transition-colors">Kirim untuk verifikasi</button>
                    </div>
                </form>
            @endif
        </section>

        <section class="bg-white rounded-2xl ring-1 ring-slate-200/70 overflow-hidden">
            <div class="px-5 sm:px-6 pt-5 pb-3">
                <h2 class="text-lg font-extrabold tracking-tight text-ink">Riwayat kiriman</h2>
            </div>
            @if($reports->isEmpty())
                <p class="px-5 sm:px-6 pb-8 text-sm text-slate-400">Belum ada harga yang Anda kirim.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-slate-50 border-y border-slate-100">
                            <tr>
                                <th class="py-2.5 px-5 sm:px-6">Tanggal pantau</th>
                                <th class="py-2.5 px-3">Lokasi</th>
                                <th class="py-2.5 px-3">Komoditi</th>
                                <th class="py-2.5 px-3">Status</th>
                                <th class="py-2.5 px-5 sm:px-6">Catatan petugas</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($reports as $report)
                                @php
                                    $style = $statusStyles[$report->status] ?? $statusStyles['draft'];
                                    $verificationNote = $latestNotes->get($report->id);
                                @endphp
                                <tr class="align-top">
                                    <td class="py-3 px-5 sm:px-6 whitespace-nowrap tabular-nums">{{ optional($report->series?->observed_at)->translatedFormat('d M Y') }}</td>
                                    <td class="py-3 px-3 min-w-[180px]">{{ $report->series?->point?->name }}</td>
                                    <td class="py-3 px-3 min-w-[220px]">
                                        @foreach($report->items as $item)
                                            <div class="flex justify-between gap-3">
                                                <span class="text-slate-700">{{ $item->commodity?->name }}</span>
                                                <span class="tabular-nums font-semibold text-ink whitespace-nowrap">Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                                            </div>
                                        @endforeach
                                    </td>
                                    <td class="py-3 px-3 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold ring-1 {{ $style['class'] }}">{{ $style['label'] }}</span>
                                    </td>
                                    <td class="py-3 px-5 sm:px-6 min-w-[200px] text-slate-500">{{ $verificationNote->note ?? '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($reports->hasPages())
                    <div class="px-5 sm:px-6 py-4 border-t border-slate-100">{{ $reports->links() }}</div>
                @endif
            @endif
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            const rows = document.getElementById('item-rows');
            const addButton = document.getElementById('add-item-row');
            if (! rows || ! addButton) {
                return;
            }
            let nextIndex = rows.querySelectorAll('[data-item-row]').length;

            addButton.addEventListener('click', function () {
                const template = rows.querySelector('[data-item-row]');
                const row = template.cloneNode(true);
                row.querySelectorAll('select, input').forEach(function (field) {
                    field.name = field.name.replace(/items\[\d+\]/, 'items[' + nextIndex + ']');
                    field.value = '';
                });
                rows.appendChild(row);
                nextIndex++;
                row.querySelector('select').focus();
            });

            rows.addEventListener('click', function (event) {
                const removeButton = event.target.closest('[data-remove-row]');
                if (! removeButton || rows.querySelectorAll('[data-item-row]').length === 1) {
                    return;
                }
                removeButton.closest('[data-item-row]').remove();
            });
        })();
    </script>
@endpush
