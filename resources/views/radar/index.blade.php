@extends('layouts.app')

@section('title', 'Radar Peta - PANTAU HARGA')
@section('breadcrumb', 'Radar')

@push('head')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
    <style>
        #radar-map { background: #F6F7F5; }
        #radar-map .leaflet-tile-pane { filter: grayscale(0.6) contrast(0.95) brightness(1.04); }
        .radar-kecamatan-label {
            background: transparent;
            border: 0;
            box-shadow: none;
            color: #0D3B25;
            font-weight: 700;
            font-size: 11px;
            text-shadow: 0 0 3px #fff, 0 0 3px #fff, 0 0 3px #fff;
            white-space: nowrap;
        }
        .radar-kecamatan-label::before { display: none; }
    </style>
@endpush

@section('content')
    <div class="space-y-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">Radar</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Sebaran harga dan stok di pasar berkoordinat.</p>
        </div>

        <form method="GET" action="{{ route('radar.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
            <div class="bg-white border border-slate-200 rounded-xl px-3.5 py-2 card-shadow">
                <div class="text-[10px] font-medium text-slate-400 uppercase tracking-wider">Tanggal</div>
                <input type="date" name="date" value="{{ $selectedDate }}" onchange="this.form.submit()" class="form-plain text-xs sm:text-sm font-semibold text-slate-800 mt-0.5 w-full">
            </div>
            <div class="bg-white border border-slate-200 rounded-xl px-3.5 py-2 card-shadow">
                <div class="text-[10px] font-medium text-slate-400 uppercase tracking-wider">Kecamatan</div>
                <select name="kecamatan_id" onchange="this.form.submit()" class="form-plain text-xs sm:text-sm font-semibold text-slate-800 mt-0.5 w-full">
                    <option value="">Semua kecamatan</option>
                    @foreach($kecamatans as $kec)
                        <option value="{{ $kec->id }}" @selected($selectedKecamatan == $kec->id)>{{ $kec->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl px-3.5 py-2 card-shadow">
                <div class="text-[10px] font-medium text-slate-400 uppercase tracking-wider">Komoditi</div>
                <select name="commodity_id" onchange="this.form.submit()" class="form-plain text-xs sm:text-sm font-semibold text-slate-800 mt-0.5 w-full">
                    <option value="">Semua komoditi</option>
                    @foreach($commodities as $c)
                        <option value="{{ $c->id }}" @selected($selectedCommodity == $c->id)>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
        </form>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
            <div class="xl:col-span-2 bg-white rounded-2xl border border-slate-200/70 card-shadow overflow-hidden">
                <div id="radar-map" class="w-full h-[420px] sm:h-[520px]"></div>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200/70 card-shadow p-4 sm:p-5 space-y-3">
                <div class="flex items-center justify-between gap-2">
                    <h2 class="text-sm font-bold text-slate-900">Pasar di peta</h2>
                    <span class="text-[11px] font-semibold text-slate-500">{{ $markers->count() }} titik</span>
                </div>
                <div class="flex flex-wrap gap-2 text-[11px]">
                    <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-full bg-rose-50 text-rose-700 border border-rose-100"><span class="w-2 h-2 rounded-full bg-rose-500"></span>Lonjakan</span>
                    <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-full bg-amber-50 text-amber-800 border border-amber-100"><span class="w-2 h-2 rounded-full bg-amber-500"></span>Stok menipis</span>
                    <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-100"><span class="w-2 h-2 rounded-full bg-emerald-500"></span>Stabil</span>
                </div>
                <div class="divide-y divide-slate-100 max-h-[420px] overflow-y-auto custom-scrollbar">
                    @forelse($markers as $marker)
                        <div class="py-3 space-y-1">
                            <div class="font-bold text-sm text-slate-900">{{ $marker['name'] }}</div>
                            <div class="text-[11px] text-slate-500">{{ $marker['region'] ?: 'Bengkulu Tengah' }}</div>
                            <div class="text-xs text-slate-700">
                                @if($marker['avg_price'] !== null)
                                    Rata-rata Rp {{ number_format($marker['avg_price'], 0, ',', '.') }}
                                    · Stok {{ number_format($marker['total_stock'] ?? 0, 0, ',', '.') }}
                                @else
                                    Belum ada observasi pada filter ini
                                @endif
                            </div>
                            <div class="flex gap-2 pt-1">
                                <a href="{{ route('prices.index', ['tab' => 'tabulasi', 'market_id' => $marker['market_id'], 'date' => $selectedDate]) }}" class="text-[11px] font-semibold text-brand-700 hover:text-brand-800">Input harga</a>
                                <a href="{{ route('actions.index') }}" class="text-[11px] font-semibold text-slate-600 hover:text-slate-800">Tindak lanjut</a>
                            </div>
                        </div>
                    @empty
                        <p class="py-8 text-center text-sm text-slate-400">Tidak ada pasar berkoordinat untuk filter ini.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        (function () {
            const markers = @json($markers);
            const selectedRegionName = @json(optional($kecamatans->firstWhere('id', $selectedKecamatan))->name);
            const regionIds = @json($kecamatans->mapWithKeys(fn ($kec) => [mb_strtoupper($kec->name) => $kec->id]));
            const radarUrl = (regionId) => {
                const url = new URL(@json(route('radar.index', array_filter(['date' => $selectedDate, 'commodity_id' => $selectedCommodity]))));
                if (regionId) {
                    url.searchParams.set('kecamatan_id', regionId);
                }
                return url.toString();
            };
            const escapeHtml = (value) => String(value ?? '').replace(/[&<>"']/g, (character) => (
                { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[character]
            ));
            const map = L.map('radar-map', { zoomSnap: 0.25 }).setView([{{ $centerLat }}, {{ $centerLng }}], 10);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 18,
                attribution: '&copy; OpenStreetMap'
            }).addTo(map);

            fetch(@json(asset('geo/bengkulu-tengah-kecamatan.geojson')))
                .then((response) => response.json())
                .then((geojson) => {
                    let selectedLayer = null;
                    const isSelected = (feature) => selectedRegionName
                        && feature.properties.nama.toUpperCase() === selectedRegionName.toUpperCase();

                    const regionLayer = L.geoJSON(geojson, {
                        style: (feature) => ({
                            color: isSelected(feature) ? '#125032' : '#17663E',
                            weight: isSelected(feature) ? 2.5 : 1.4,
                            opacity: isSelected(feature) ? 1 : 0.7,
                            fillColor: isSelected(feature) ? '#2E9C61' : '#D7EDDF',
                            fillOpacity: isSelected(feature) ? 0.3 : 0.15,
                        }),
                        onEachFeature: (feature, layer) => {
                            const regionKey = feature.properties.nama.toUpperCase();
                            const regionId = regionIds[regionKey];
                            const regionMarkers = markers.filter((m) => (m.region || '').toUpperCase() === regionKey);
                            const pricedCount = regionMarkers.filter((m) => m.avg_price !== null).length;
                            const alertCount = regionMarkers.filter((m) => m.tone === 'spike' || m.tone === 'stock').length;

                            layer.bindTooltip(escapeHtml(feature.properties.nama), { permanent: true, direction: 'center', className: 'radar-kecamatan-label' });
                            layer.bindPopup(
                                `<div style="min-width:170px"><strong>Kec. ${escapeHtml(feature.properties.nama)}</strong>`
                                + `<div style="color:#64748b;font-size:12px;margin-top:2px">${regionMarkers.length} pasar · ${pricedCount} ada observasi`
                                + (alertCount ? ` · <span style="color:#e11d48;font-weight:600">${alertCount} perlu perhatian</span>` : '')
                                + '</div>'
                                + (regionId
                                    ? (isSelected(feature)
                                        ? `<a href="${radarUrl(null)}" style="display:inline-block;margin-top:8px;font-weight:600;color:#17663E">Tampilkan semua kecamatan &rarr;</a>`
                                        : `<a href="${radarUrl(regionId)}" style="display:inline-block;margin-top:8px;font-weight:600;color:#17663E">Saring kecamatan ini &rarr;</a>`)
                                    : '<div style="margin-top:8px;font-size:12px;color:#94a3b8">Belum terdaftar sebagai wilayah pantau</div>')
                                + '</div>'
                            );
                            layer.on('mouseover', () => layer.setStyle({ weight: 2.5, fillOpacity: 0.35, opacity: 1 }));
                            layer.on('mouseout', () => regionLayer.resetStyle(layer));
                            if (isSelected(feature)) {
                                selectedLayer = layer;
                            }
                        },
                    }).addTo(map);

                    const outsideMask = [[[-90, -360], [-90, 360], [90, 360], [90, -360]]];
                    geojson.features.forEach((feature) => {
                        const polygons = feature.geometry.type === 'MultiPolygon' ? feature.geometry.coordinates : [feature.geometry.coordinates];
                        polygons.forEach((polygon) => outsideMask.push(polygon[0].map((point) => [point[1], point[0]])));
                    });
                    regionLayer.bringToBack();
                    L.polygon(outsideMask, { stroke: false, fillColor: '#F6F7F5', fillOpacity: 1, interactive: false }).addTo(map).bringToBack();

                    const regencyBounds = regionLayer.getBounds();
                    map.setMinZoom(map.getBoundsZoom(regencyBounds, false, L.point(32, 32)));
                    map.setMaxBounds(regencyBounds.pad(0.08));
                    map.options.maxBoundsViscosity = 1;
                    map.fitBounds((selectedLayer || regionLayer).getBounds(), { padding: [16, 16] });
                });

            const colors = { spike: '#e11d48', stock: '#d97706', stable: '#059669' };

            markers.forEach((m) => {
                const color = colors[m.tone] || colors.stable;
                const circle = L.circleMarker([m.lat, m.lng], {
                    radius: 9,
                    color: '#fff',
                    weight: 2,
                    fillColor: color,
                    fillOpacity: 0.9
                }).addTo(map);

                const priceText = m.avg_price !== null
                    ? `Rata-rata Rp ${Number(m.avg_price).toLocaleString('id-ID')}<br>Stok ${Number(m.total_stock || 0).toLocaleString('id-ID')}`
                    : 'Belum ada observasi';

                circle.bindPopup(
                    `<strong>${escapeHtml(m.name)}</strong><br>${escapeHtml(m.region)}<br>${priceText}<br>` +
                    `<a href="{{ url('/prices') }}?tab=tabulasi&market_id=${m.market_id}&date={{ $selectedDate }}">Buka tabulasi</a>`
                );
            });
        })();
    </script>
@endpush
