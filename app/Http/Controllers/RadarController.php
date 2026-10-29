<?php

namespace App\Http\Controllers;

use App\Models\Commodity;
use App\Models\Region;
use App\RadarMapData;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RadarController extends Controller
{
    public function __construct(private RadarMapData $radarMapData) {}

    public function index(Request $request): View
    {
        $selectedDate = $request->get('date', '2026-10-27');
        $selectedKecamatan = $request->filled('kecamatan_id') ? (int) $request->get('kecamatan_id') : null;
        $selectedCommodity = $request->filled('commodity_id') ? (int) $request->get('commodity_id') : null;

        $kecamatans = Region::where('parent_id', 1)->orderBy('name')->get();
        $commodities = Commodity::where('is_active', true)->orderBy('name')->get();

        $markers = $this->radarMapData->markers(
            Carbon::parse($selectedDate),
            $selectedKecamatan,
            $selectedCommodity,
        );

        $centerLat = $markers->avg('lat') ?: -3.75;
        $centerLng = $markers->avg('lng') ?: 102.45;

        return view('radar.index', compact(
            'selectedDate',
            'selectedKecamatan',
            'selectedCommodity',
            'kecamatans',
            'commodities',
            'markers',
            'centerLat',
            'centerLng',
        ));
    }
}
