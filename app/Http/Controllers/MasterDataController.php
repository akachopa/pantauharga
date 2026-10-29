<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Commodity;
use App\Models\Market;
use App\Models\MonitoringPoint;
use App\Models\PointAssignment;
use App\Models\Region;
use App\Models\User;
use Illuminate\Http\Request;

class MasterDataController extends Controller
{
    public function index(Request $request)
    {
        $activeTab = $request->get('tab', 'pasar');
        $filterKecamatan = $request->get('kecamatan_id');
        $filterType = $request->get('point_type');
        $filterStatus = $request->get('status');
        $search = $request->get('search');

        // Lists
        $kecamatans = Region::where('parent_id', 1)->orderBy('name')->get();
        $markets = Market::with('region')->orderBy('name')->get();

        // Points Query
        $pointQuery = MonitoringPoint::with(['market', 'region', 'assignments.user', 'assignments.business']);

        if ($filterKecamatan) {
            $pointQuery->where('region_id', $filterKecamatan);
        }
        if ($filterType) {
            $pointQuery->where('point_type', $filterType);
        }
        if ($filterStatus !== null && $filterStatus !== '') {
            $pointQuery->where('is_active', $filterStatus == '1');
        }
        if ($search) {
            $pointQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhereHas('market', function ($marketQuery) use ($search) {
                        $marketQuery->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('assignments.user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $points = $pointQuery->orderBy('id')->get();
        $selectedPointId = (int) $request->get('point');
        $selectedPoint = $points->firstWhere('id', $selectedPointId) ?? $points->first();

        // Businesses (Mitra)
        $businesses = Business::with('region')->orderBy('id')->get();

        // Commodities
        $commodities = Commodity::orderBy('id')->get();

        // Users for assignment dropdown
        $officers = User::all();

        // Mini stat counts
        $totalPoints = MonitoringPoint::count();
        $activePoints = MonitoringPoint::where('is_active', true)->count();
        $totalBusinesses = Business::count();
        $verifiedBusinesses = Business::where('is_verified', true)->count();
        $totalCommodities = Commodity::count();

        return view('master.index', compact(
            'activeTab',
            'kecamatans',
            'markets',
            'points',
            'selectedPoint',
            'businesses',
            'commodities',
            'officers',
            'filterKecamatan',
            'filterType',
            'filterStatus',
            'search',
            'totalPoints',
            'activePoints',
            'totalBusinesses',
            'verifiedBusinesses',
            'totalCommodities'
        ));
    }

    // Market CRUD
    public function storeMarket(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:150',
            'region_id' => 'required|exists:regions,id',
            'address' => 'nullable|string',
            'market_days' => 'nullable|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        do {
            $code = 'MKT-'.strtoupper(substr(str_replace(' ', '', $request->name), 0, 4)).'-'.rand(100, 9999);
        } while (Market::where('code', $code)->exists());

        Market::create([
            'region_id' => $request->region_id,
            'code' => $code,
            'name' => $request->name,
            'address' => $request->address,
            'market_days' => $request->market_days ?? 'Setiap Hari',
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'is_active' => true,
        ]);

        return redirect()->route('master.index', ['tab' => 'pasar'])->with('success', 'Pasar baru berhasil ditambahkan!');
    }

    public function updateMarket(Request $request, $id)
    {
        $market = Market::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:150',
            'region_id' => 'required|exists:regions,id',
            'address' => 'nullable|string',
            'market_days' => 'nullable|string',
        ]);

        $market->update([
            'name' => $request->name,
            'region_id' => $request->region_id,
            'address' => $request->address,
            'market_days' => $request->market_days,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('master.index', ['tab' => 'pasar'])->with('success', 'Data pasar berhasil diperbarui!');
    }

    public function destroyMarket($id)
    {
        Market::destroy($id);

        return redirect()->route('master.index', ['tab' => 'pasar'])->with('success', 'Pasar berhasil dihapus!');
    }

    // Point CRUD
    public function storePoint(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:150',
            'region_id' => 'required|exists:regions,id',
            'point_type' => 'required|in:pasar_tradisional,toko_kelontong,agen_distributor',
        ]);

        do {
            $code = 'PT-'.rand(100, 9999);
        } while (MonitoringPoint::where('code', $code)->exists());

        $point = MonitoringPoint::create([
            'market_id' => $request->market_id,
            'region_id' => $request->region_id,
            'code' => $code,
            'name' => $request->name,
            'point_type' => $request->point_type,
            'is_demo' => false,
            'is_active' => true,
        ]);

        if ($request->user_id) {
            PointAssignment::create([
                'user_id' => $request->user_id,
                'point_id' => $point->id,
                'starts_on' => now()->toDateString(),
            ]);
        }

        return redirect()->route('master.index', ['tab' => 'pasar'])->with('success', 'Titik pantau baru berhasil ditambahkan!');
    }

    public function updatePoint(Request $request, $id)
    {
        $point = MonitoringPoint::findOrFail($id);

        if ($request->input('intent') === 'penugasan') {
            $request->validate([
                'user_id' => 'nullable|exists:users,id',
                'business_id' => 'nullable|exists:businesses,id',
                'schedule' => 'nullable|string|max:60',
            ]);

            if ($request->filled('user_id')) {
                PointAssignment::updateOrCreate(
                    ['point_id' => $point->id],
                    [
                        'user_id' => $request->user_id,
                        'business_id' => $request->business_id,
                        'report_days' => $request->schedule,
                        'starts_on' => now()->toDateString(),
                    ]
                );
            }

            return redirect()->route('master.index', ['tab' => 'pasar', 'point' => $point->id])
                ->with('success', "Penugasan {$point->name} berhasil disimpan");
        }

        $point->update([
            'name' => $request->name,
            'market_id' => $request->market_id,
            'region_id' => $request->region_id,
            'point_type' => $request->point_type,
            'is_active' => $request->has('is_active'),
        ]);

        if ($request->user_id) {
            PointAssignment::updateOrCreate(
                ['point_id' => $point->id],
                ['user_id' => $request->user_id, 'starts_on' => now()->toDateString()]
            );
        }

        return redirect()->route('master.index', ['tab' => 'pasar'])->with('success', 'Titik pantau berhasil diperbarui!');
    }

    public function destroyPoint($id)
    {
        MonitoringPoint::destroy($id);

        return redirect()->route('master.index', ['tab' => 'pasar'])->with('success', 'Titik pantau berhasil dihapus!');
    }

    // Business (Mitra) CRUD
    public function storeBusiness(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:150',
            'owner_name' => 'required|string|max:150',
            'business_type' => 'required|string',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
        ]);

        do {
            $code = 'BIZ-'.rand(100, 9999);
        } while (Business::where('code', $code)->exists());

        Business::create([
            'region_id' => $request->region_id,
            'code' => $code,
            'name' => $request->name,
            'owner_name' => $request->owner_name,
            'business_type' => $request->business_type,
            'phone' => $request->phone,
            'address' => $request->address,
            'is_verified' => true,
        ]);

        return redirect()->route('master.index', ['tab' => 'mitra'])->with('success', 'Mitra pelaksana baru berhasil ditambahkan!');
    }

    public function updateBusiness(Request $request, $id)
    {
        $biz = Business::findOrFail($id);
        $biz->update($request->all());

        return redirect()->route('master.index', ['tab' => 'mitra'])->with('success', 'Data mitra berhasil diperbarui!');
    }

    public function destroyBusiness($id)
    {
        Business::destroy($id);

        return redirect()->route('master.index', ['tab' => 'mitra'])->with('success', 'Mitra berhasil dihapus!');
    }

    // Commodity CRUD
    public function storeCommodity(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'category' => 'required|string',
            'base_unit' => 'required|string|max:20',
            'reference_price' => 'required|numeric',
        ]);

        do {
            $code = strtoupper(str_replace(' ', '_', $request->name)).'_'.rand(100, 9999);
        } while (Commodity::where('code', $code)->exists());

        Commodity::create([
            'code' => $code,
            'name' => $request->name,
            'category' => $request->category,
            'base_unit' => $request->base_unit,
            'icon' => $request->icon ?? '🌾',
            'reference_price' => $request->reference_price,
            'is_active' => true,
        ]);

        return redirect()->route('master.index', ['tab' => 'komoditi'])->with('success', 'Komoditi baru berhasil ditambahkan!');
    }

    public function updateCommodity(Request $request, $id)
    {
        $commodity = Commodity::findOrFail($id);
        $commodity->update($request->all());

        return redirect()->route('master.index', ['tab' => 'komoditi'])->with('success', 'Data komoditi berhasil diperbarui!');
    }

    public function destroyCommodity($id)
    {
        Commodity::destroy($id);

        return redirect()->route('master.index', ['tab' => 'komoditi'])->with('success', 'Komoditi berhasil dihapus!');
    }
}
