@extends('layouts.public')

@section('title', 'Tabel Harga - PANTAU HARGA Bengkulu Tengah')

@section('content')
    @include('public.partials.page-header', [
        'title' => 'Tabel',
        'description' => 'Rincian harga terverifikasi di setiap pasar dan titik pantau.',
    ])

    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-8 sm:py-12">
        <section id="tabel" class="scroll-mt-20 bg-white rounded-2xl ring-1 ring-slate-200/70 overflow-hidden">
            <div class="px-5 sm:px-6 py-5 border-b border-slate-100">
                <h2 class="text-lg font-extrabold tracking-tight text-ink">Harga per pasar</h2>
                <p class="text-sm text-slate-500 mt-0.5">Harga per pasar, {{ $shortDateLabel }}.</p>
            </div>

            <div class="hidden md:block overflow-x-auto custom-scrollbar">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50 text-xs font-semibold text-slate-500">
                            <th class="py-3 px-6">Komoditi</th>
                            <th class="py-3 px-4">Pasar</th>
                            <th class="py-3 px-4 text-right">Harga</th>
                            <th class="py-3 px-4">Perubahan</th>
                            <th class="py-3 px-4 text-right">Stok</th>
                            <th class="py-3 px-6">Waktu pantau</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-slate-700 divide-y divide-slate-100">
                        @forelse($priceItems as $row)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="py-3.5 px-6">
                                    <div class="flex items-center gap-3">
                                        @include('prices.partials.commodity-icon', ['name' => $row->commodity_name])
                                        <div>
                                            <div class="font-semibold text-ink">{{ $row->commodity_name }}</div>
                                            <div class="text-xs text-slate-400">per {{ $row->base_unit }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4">{{ $row->market_name ?: '—' }}</td>
                                <td class="py-3.5 px-4 text-right font-semibold text-ink tabular-nums">Rp {{ number_format($row->price, 0, ',', '.') }}</td>
                                <td class="py-3.5 px-4">@include('public.partials.change-badge', ['changePct' => (float) $row->price_change_pct, 'compact' => true])</td>
                                <td class="py-3.5 px-4 text-right tabular-nums">{{ number_format($row->stock, 0, ',', '.') }}</td>
                                <td class="py-3.5 px-6 text-slate-500 whitespace-nowrap">{{ \Carbon\Carbon::parse($row->observed_at)->translatedFormat('d M Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-14 text-center">
                                    <p class="text-sm font-semibold text-slate-700">Tidak ada harga terverifikasi</p>
                                    <p class="text-sm text-slate-400 mt-1">Tidak ada baris untuk filter ini.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <ul class="md:hidden divide-y divide-slate-100">
                @forelse($priceItems as $row)
                    <li class="px-4 py-3.5 flex items-center gap-3">
                        @include('prices.partials.commodity-icon', ['name' => $row->commodity_name])
                        <div class="min-w-0 flex-1">
                            <div class="font-semibold text-ink text-sm truncate">{{ $row->commodity_name }}</div>
                            <div class="text-xs text-slate-500 truncate">{{ $row->market_name ?: '—' }} · stok {{ number_format($row->stock, 0, ',', '.') }}</div>
                        </div>
                        <div class="text-right shrink-0">
                            <div class="font-bold text-ink text-sm tabular-nums">Rp {{ number_format($row->price, 0, ',', '.') }}</div>
                            <div class="mt-0.5">@include('public.partials.change-badge', ['changePct' => (float) $row->price_change_pct, 'compact' => true])</div>
                        </div>
                    </li>
                @empty
                    <li class="py-14 text-center">
                        <p class="text-sm font-semibold text-slate-700">Tidak ada harga terverifikasi</p>
                        <p class="text-sm text-slate-400 mt-1">Tidak ada baris untuk filter ini.</p>
                    </li>
                @endforelse
            </ul>

            @if($priceItems->hasPages())
                <div class="px-5 py-3 border-t border-slate-100">
                    {{ $priceItems->links() }}
                </div>
            @endif
        </section>
    </div>
@endsection
