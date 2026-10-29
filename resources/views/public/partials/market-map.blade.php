<div>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
<style>
    #market-map { background: #F6F7F5; }
    #market-map .leaflet-tile-pane { filter: grayscale(0.65) contrast(0.95) brightness(1.04); }
    #market-map .leaflet-container { font-family: inherit; }
    .kecamatan-label {
        background: transparent;
        border: 0;
        box-shadow: none;
        color: #0D3B25;
        font-weight: 700;
        font-size: 11px;
        letter-spacing: 0.01em;
        text-shadow: 0 0 3px #fff, 0 0 3px #fff, 0 0 3px #fff;
        white-space: nowrap;
    }
    .kecamatan-label::before { display: none; }
    .market-pin {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        transform: translate(-50%, -100%);
        white-space: nowrap;
        padding: 3px 8px;
        border-radius: 999px;
        background: #101828;
        color: #fff;
        font-size: 11px;
        font-weight: 700;
        box-shadow: 0 4px 10px -2px rgba(16, 24, 40, 0.35);
        font-variant-numeric: tabular-nums;
    }
    .market-pin::after {
        content: '';
        position: absolute;
        left: 50%;
        bottom: -4px;
        width: 8px;
        height: 8px;
        background: inherit;
        transform: translateX(-50%) rotate(45deg);
        z-index: -1;
    }
    .market-pin.is-cheapest { background: #1F7F4D; }
    .market-pin.is-empty { background: #fff; color: #475467; box-shadow: 0 0 0 1px #D0D5DD, 0 4px 10px -4px rgba(16, 24, 40, 0.25); }
    .market-pin.is-empty::after { box-shadow: 1px 1px 0 0 #D0D5DD; }
    .market-pin-dot { width: 6px; height: 6px; border-radius: 999px; background: #98A2B3; }
</style>

<section class="bg-white rounded-2xl ring-1 ring-slate-200/70 overflow-hidden">
    <div class="px-5 sm:px-6 pt-5 pb-4 flex flex-col sm:flex-row sm:items-end justify-between gap-3">
        <div>
            <h2 class="text-lg font-extrabold tracking-tight text-ink">Peta pasar Bengkulu Tengah</h2>
            <p class="text-sm text-slate-500 mt-0.5">
                Label menunjukkan harga {{ $nearbyCommodity->name ?? 'komoditi' }} di tiap pasar. Klik kecamatan untuk menyaring.
            </p>
        </div>
        @if($selectedKecamatan)
            <a href="{{ $mapData['filterUrl'] }}" class="shrink-0 text-sm font-semibold text-brand-700 hover:text-brand-800">Tampilkan semua kecamatan</a>
        @endif
    </div>

    <div id="market-map" class="h-[380px] sm:h-[480px] border-y border-slate-100" role="region" aria-label="Peta kecamatan dan lokasi pasar di Kabupaten Bengkulu Tengah"></div>

    <div class="px-5 sm:px-6 py-3 flex flex-wrap items-center gap-x-5 gap-y-2 text-xs text-slate-600">
        <span class="inline-flex items-center gap-1.5"><span class="px-1.5 py-0.5 rounded-full bg-ink text-white text-[10px] font-bold">Rp</span> Ada harga</span>
        <span class="inline-flex items-center gap-1.5"><span class="px-1.5 py-0.5 rounded-full bg-brand-600 text-white text-[10px] font-bold">Rp</span> Paling hemat</span>
        <span class="inline-flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-slate-400"></span> Belum ada data</span>
        <span class="inline-flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-sky-500 ring-4 ring-sky-200"></span> {{ $originSource === 'lokasi' ? 'Lokasi Anda' : 'Titik acuan' }}</span>
        <span class="inline-flex items-center gap-1.5"><span class="w-3.5 h-3 rounded-sm bg-brand-500/30 ring-1 ring-brand-700"></span> Kecamatan dipilih</span>
        <span class="sm:ml-auto text-[11px] text-slate-400">Batas kecamatan: BPS Wilkerstat 2021</span>
    </div>
</section>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    (function () {
        const mapData = @json($mapData);
        const rupiah = new Intl.NumberFormat('id-ID');
        const escapeHtml = function (value) {
            return String(value ?? '').replace(/[&<>"']/g, function (character) {
                return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[character];
            });
        };
        const map = L.map('market-map', { scrollWheelZoom: false, zoomSnap: 0.25, zoomControl: true, attributionControl: true })
            .setView([mapData.origin.lat, mapData.origin.lng], 10);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 17,
            attribution: '&copy; OpenStreetMap',
        }).addTo(map);

        const marketCountByRegion = mapData.markets.reduce(function (counts, market) {
            const key = (market.region || '').toUpperCase();
            counts[key] = (counts[key] || 0) + 1;
            return counts;
        }, {});

        function regionStyle(feature) {
            const name = feature.properties.nama.toUpperCase();
            const isSelected = mapData.selectedRegionId && mapData.regions[name] === mapData.selectedRegionId;
            const hasMarkets = (marketCountByRegion[name] || 0) > 0;

            return {
                color: isSelected ? '#125032' : '#17663E',
                weight: isSelected ? 2.5 : 1.4,
                opacity: isSelected ? 1 : 0.7,
                fillColor: isSelected ? '#2E9C61' : (hasMarkets ? '#7FC29A' : '#D7EDDF'),
                fillOpacity: isSelected ? 0.32 : (hasMarkets ? 0.22 : 0.12),
            };
        }

        fetch(mapData.geojsonUrl)
            .then(function (response) { return response.json(); })
            .then(function (geojson) {
                let selectedLayer = null;
                const regionLayer = L.geoJSON(geojson, {
                    style: regionStyle,
                    onEachFeature: function (feature, layer) {
                        const name = escapeHtml(feature.properties.nama);
                        const regionId = mapData.regions[feature.properties.nama.toUpperCase()];
                        const marketCount = marketCountByRegion[feature.properties.nama.toUpperCase()] || 0;

                        layer.bindTooltip(name, { permanent: true, direction: 'center', className: 'kecamatan-label' });
                        layer.bindPopup(
                            '<div style="min-width:160px">'
                            + '<div style="font-weight:700;color:#101828">Kec. ' + name + '</div>'
                            + '<div style="color:#667085;font-size:12px;margin-top:2px">' + marketCount + ' pasar berkoordinat</div>'
                            + (regionId
                                ? '<a href="#" data-region-id="' + regionId + '" style="display:inline-block;margin-top:8px;font-weight:600;color:#17663E">Saring kecamatan ini &rarr;</a>'
                                : '<div style="margin-top:8px;font-size:12px;color:#98A2B3">Belum ada titik pantau</div>')
                            + '</div>'
                        );
                        layer.on('mouseover', function () { layer.setStyle({ weight: 2.5, fillOpacity: 0.35 }); });
                        layer.on('mouseout', function () { regionLayer.resetStyle(layer); });

                        if (mapData.selectedRegionId && regionId === mapData.selectedRegionId) {
                            selectedLayer = layer;
                        }
                    },
                }).addTo(map);

                const outsideMask = [[[-90, -360], [-90, 360], [90, 360], [90, -360]]];
                geojson.features.forEach(function (feature) {
                    const polygons = feature.geometry.type === 'MultiPolygon' ? feature.geometry.coordinates : [feature.geometry.coordinates];
                    polygons.forEach(function (polygon) {
                        outsideMask.push(polygon[0].map(function (point) { return [point[1], point[0]]; }));
                    });
                });
                L.polygon(outsideMask, { stroke: false, fillColor: '#F6F7F5', fillOpacity: 1, interactive: false }).addTo(map).bringToBack();

                regionLayer.bringToFront();
                const regencyBounds = regionLayer.getBounds();
                const regencyZoom = map.getBoundsZoom(regencyBounds, false, L.point(32, 32));
                map.setMinZoom(regencyZoom);
                map.setMaxBounds(regencyBounds.pad(0.08));
                map.options.maxBoundsViscosity = 1;
                map.fitBounds((selectedLayer || regionLayer).getBounds(), { padding: [16, 16] });
            });

        map.on('popupopen', function (event) {
            event.popup.getElement().querySelector('[data-region-id]')?.addEventListener('click', function (clickEvent) {
                clickEvent.preventDefault();
                const url = new URL(mapData.filterUrl);
                url.searchParams.set('kecamatan_id', this.dataset.regionId);
                window.location.href = url.toString();
            });
        });

        mapData.markets.forEach(function (market) {
            const hasPrice = market.price !== null;
            const classes = ['market-pin', hasPrice ? '' : 'is-empty', market.is_cheapest ? 'is-cheapest' : ''].join(' ');
            const label = hasPrice ? 'Rp ' + rupiah.format(market.price) : '<span class="market-pin-dot"></span>' + escapeHtml(market.name);
            const icon = L.divIcon({ className: '', html: '<div class="' + classes + '" style="position:relative;margin-top:-6px">' + label + '</div>', iconSize: null });
            const badges = [
                market.is_closest ? '<span style="background:#F0F9FF;color:#0369A1;padding:1px 6px;border-radius:6px;font-size:11px;font-weight:600">Paling dekat</span>' : '',
                market.is_cheapest ? '<span style="background:#EEF7F1;color:#17663E;padding:1px 6px;border-radius:6px;font-size:11px;font-weight:600">Paling hemat</span>' : '',
            ].join(' ');

            L.marker([market.lat, market.lng], { icon: icon, riseOnHover: true, zIndexOffset: hasPrice ? 500 : 0 })
                .bindPopup(
                    '<div style="min-width:170px">'
                    + (badges.trim() ? '<div style="margin-bottom:6px">' + badges + '</div>' : '')
                    + '<div style="font-weight:700;color:#101828">' + escapeHtml(market.name) + '</div>'
                    + '<div style="color:#667085;font-size:12px">' + (market.region ? 'Kec. ' + escapeHtml(market.region) : '') + '</div>'
                    + '<div style="margin-top:6px;font-size:16px;font-weight:800;color:#101828">'
                    + (hasPrice ? 'Rp ' + rupiah.format(market.price) : '<span style="font-size:12px;font-weight:500;color:#98A2B3">Belum ada harga pada tanggal ini</span>')
                    + '</div></div>'
                )
                .addTo(map);
        });

        L.circleMarker([mapData.origin.lat, mapData.origin.lng], {
            radius: 7, color: '#fff', weight: 3, fillColor: '#0EA5E9', fillOpacity: 1,
        }).bindTooltip(mapData.origin.source === 'lokasi' ? 'Lokasi Anda' : 'Titik acuan jarak').addTo(map);
    })();
</script>
</div>
