<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Commodity;
use App\Models\MonitoringPoint;
use App\Models\SupplyLink;
use App\Models\SupplyShipment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupplyChainController extends Controller
{
    public function index(Request $request): View
    {
        $activeTab = $request->get('tab', 'relasi');
        $statusFilter = $request->get('status');

        $links = SupplyLink::with(['business', 'point.market', 'commodity'])
            ->orderByDesc('id')
            ->get();

        $shipmentsQuery = SupplyShipment::with(['link.business', 'link.point', 'link.commodity'])
            ->orderByDesc('scheduled_on')
            ->orderByDesc('id');

        if ($statusFilter) {
            $shipmentsQuery->where('status', $statusFilter);
        }

        $shipments = $shipmentsQuery->limit(100)->get();
        $businesses = Business::query()->orderBy('name')->get();
        $points = MonitoringPoint::query()->where('is_active', true)->orderBy('name')->get();
        $commodities = Commodity::query()->where('is_active', true)->orderBy('name')->get();

        return view('supply.index', compact(
            'activeTab',
            'statusFilter',
            'links',
            'shipments',
            'businesses',
            'points',
            'commodities',
        ));
    }

    public function storeLink(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'business_id' => 'required|exists:businesses,id',
            'point_id' => 'required|exists:monitoring_points,id',
            'commodity_id' => 'nullable|exists:commodities,id',
            'notes' => 'nullable|string|max:500',
        ]);

        SupplyLink::create([
            ...$validated,
            'is_active' => true,
            'created_at' => now(),
        ]);

        return redirect()->route('supply.index', ['tab' => 'relasi'])
            ->with('success', 'Relasi pasokan berhasil ditambahkan.');
    }

    public function updateLink(Request $request, int $id): RedirectResponse
    {
        $link = SupplyLink::findOrFail($id);
        $validated = $request->validate([
            'business_id' => 'required|exists:businesses,id',
            'point_id' => 'required|exists:monitoring_points,id',
            'commodity_id' => 'nullable|exists:commodities,id',
            'notes' => 'nullable|string|max:500',
            'is_active' => 'nullable|boolean',
        ]);

        $link->update([
            ...$validated,
            'is_active' => $request->boolean('is_active', $link->is_active),
        ]);

        return redirect()->route('supply.index', ['tab' => 'relasi'])
            ->with('success', 'Relasi pasokan berhasil diperbarui.');
    }

    public function destroyLink(int $id): RedirectResponse
    {
        SupplyLink::destroy($id);

        return redirect()->route('supply.index', ['tab' => 'relasi'])
            ->with('success', 'Relasi pasokan dihapus.');
    }

    public function storeShipment(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'supply_link_id' => 'required|exists:supply_links,id',
            'scheduled_on' => 'required|date',
            'quantity' => 'required|numeric|min:0.01',
            'unit' => 'nullable|string|max:32',
            'status' => 'required|in:planned,in_transit,delivered,cancelled',
            'notes' => 'nullable|string|max:500',
        ]);

        SupplyShipment::create([
            ...$validated,
            'unit' => $validated['unit'] ?? 'kg',
            'created_at' => now(),
        ]);

        return redirect()->route('supply.index', ['tab' => 'jadwal'])
            ->with('success', 'Jadwal kiriman berhasil ditambahkan.');
    }

    public function updateShipment(Request $request, int $id): RedirectResponse
    {
        $shipment = SupplyShipment::findOrFail($id);
        $validated = $request->validate([
            'scheduled_on' => 'required|date',
            'quantity' => 'required|numeric|min:0.01',
            'unit' => 'nullable|string|max:32',
            'status' => 'required|in:planned,in_transit,delivered,cancelled',
            'notes' => 'nullable|string|max:500',
        ]);

        $shipment->update([
            ...$validated,
            'unit' => $validated['unit'] ?? $shipment->unit,
        ]);

        return redirect()->route('supply.index', ['tab' => 'jadwal'])
            ->with('success', 'Jadwal kiriman berhasil diperbarui.');
    }

    public function destroyShipment(int $id): RedirectResponse
    {
        SupplyShipment::destroy($id);

        return redirect()->route('supply.index', ['tab' => 'jadwal'])
            ->with('success', 'Jadwal kiriman dihapus.');
    }
}
