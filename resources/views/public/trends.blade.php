@extends('layouts.public')

@section('title', 'Tren & Insight Harga - PANTAU HARGA Bengkulu Tengah')

@section('content')
    @include('public.partials.page-header', [
        'title' => 'Tren & Insight',
        'description' => 'Pergerakan harga harian dan ringkasan otomatis kondisi harga dari laporan yang telah diverifikasi.',
    ])

    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-8 sm:py-12">
        <section id="tren" class="scroll-mt-20 grid grid-cols-1 lg:grid-cols-3 gap-4">
            <div class="lg:col-span-2 bg-white rounded-2xl ring-1 ring-slate-200/70 p-5 sm:p-6">
                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3 mb-5">
                    <div>
                        <h2 class="text-lg font-extrabold tracking-tight text-ink">Grafik harga</h2>
                        <p class="text-sm text-slate-500 mt-0.5">Rata-rata harga harian dari laporan yang telah diverifikasi.</p>
                    </div>
                    <div class="inline-flex shrink-0 bg-slate-100 p-1 rounded-lg self-start">
                        @foreach([7 => '7 Hari', 30 => '30 Hari'] as $periodValue => $periodLabel)
                            <a href="{{ route('public.trends', array_merge($publicFilterQuery, ['period' => $periodValue])) }}"
                               class="px-3 py-1 rounded-md text-xs font-semibold transition-colors {{ $chartPeriod === $periodValue ? 'bg-white text-ink shadow-sm' : 'text-slate-500 hover:text-ink' }}">{{ $periodLabel }}</a>
                        @endforeach
                    </div>
                </div>

                @if($hasChartData)
                    <div class="w-full overflow-x-auto custom-scrollbar -mx-1 px-1">
                        <svg viewBox="0 0 660 225" class="w-full h-56 min-w-[560px] sm:min-w-0" role="img" aria-label="Grafik tren harga komoditi">
                            <g stroke="#EEF0F2" stroke-width="1">
                                @foreach([25, 60, 95, 130, 165, 195] as $y)
                                    <line x1="55" y1="{{ $y }}" x2="640" y2="{{ $y }}"/>
                                @endforeach
                            </g>
                            <g fill="#98A2B3" font-size="11" text-anchor="end" font-weight="500">
                                @foreach($priceChart['yLabels'] as $yLabel)
                                    <text x="48" y="{{ $yLabel['y'] }}">{{ $yLabel['text'] }}</text>
                                @endforeach
                            </g>
                            <g fill="#98A2B3" font-size="11" text-anchor="middle" font-weight="500">
                                @foreach($priceChart['labels'] as $label)
                                    <text x="{{ $label['x'] }}" y="215">{{ $label['text'] }}</text>
                                @endforeach
                            </g>
                            @foreach($priceChart['series'] as $series)
                                <polyline fill="none" stroke="{{ $series['color'] }}" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" points="{{ $series['points'] }}"/>
                            @endforeach
                        </svg>
                    </div>
                    <p class="sm:hidden text-[11px] text-slate-400 mt-1">Geser grafik untuk melihat seluruh periode.</p>
                    <div class="flex flex-wrap gap-x-4 gap-y-2 pt-4 mt-3 border-t border-slate-100">
                        @foreach($priceChart['series'] as $series)
                            <span class="inline-flex items-center gap-2 text-xs font-medium text-slate-600">
                                <span class="w-2.5 h-2.5 rounded-full" style="background: {{ $series['color'] }}"></span>
                                {{ $series['name'] }}
                            </span>
                        @endforeach
                    </div>
                @else
                    <div class="py-12 text-center">
                        <p class="text-sm font-semibold text-slate-700">Belum ada deret harga</p>
                        <p class="text-sm text-slate-400 mt-1">Tidak ada laporan terverifikasi pada rentang ini.</p>
                    </div>
                @endif
            </div>

            <aside class="bg-white rounded-2xl ring-1 ring-slate-200/70 p-5 sm:p-6">
                <h2 class="text-lg font-extrabold tracking-tight text-ink">Sorotan</h2>
                <p class="text-sm text-slate-500 mt-0.5">Dibanding hari pantau sebelumnya.</p>
                <dl class="mt-4 space-y-2">
                    @foreach([
                        ['key' => 'up', 'label' => 'Kenaikan tertinggi', 'empty' => 'Tidak ada kenaikan'],
                        ['key' => 'down', 'label' => 'Penurunan tertinggi', 'empty' => 'Tidak ada penurunan'],
                        ['key' => 'stable', 'label' => 'Paling stabil', 'empty' => 'Belum ada pembanding'],
                    ] as $highlightRow)
                        @php $highlight = $highlights[$highlightRow['key']]; @endphp
                        <div class="rounded-xl bg-slate-50 px-4 py-3">
                            <dt class="text-xs text-slate-500">{{ $highlightRow['label'] }}</dt>
                            <dd class="mt-1 flex items-center justify-between gap-3">
                                <span class="font-display font-bold text-ink truncate">{{ $highlight['name'] ?? $highlightRow['empty'] }}</span>
                                @if($highlight)
                                    @include('public.partials.change-badge', ['changePct' => $highlight['change_pct'], 'compact' => true])
                                @endif
                            </dd>
                        </div>
                    @endforeach
                </dl>
            </aside>
        </section>

        <section id="insight" class="scroll-mt-20 mt-8 sm:mt-10">
            <div class="mb-5">
                <h2 class="text-xl sm:text-2xl font-extrabold tracking-tight text-ink">Insight Harga</h2>
                <p class="text-sm text-slate-500 mt-1">Dirangkum otomatis: lonjakan, penurunan, selisih antar pasar, harga acuan, dan laporan warga.</p>
            </div>
            @if($insights->isNotEmpty())
                @include('public.partials.insight-list', ['insights' => $insights])
            @else
                @include('public.partials.empty-state', [
                    'title' => 'Belum ada insight',
                    'message' => 'Tidak ada harga terverifikasi untuk filter ini.',
                ])
            @endif
        </section>
    </div>
@endsection
