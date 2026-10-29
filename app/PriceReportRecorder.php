<?php

namespace App;

use App\Models\Commodity;
use App\Models\Report;
use App\Models\ReportItem;
use App\Models\ReportSeries;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PriceReportRecorder
{
    public const AUTO_VERIFY_NOTE = 'Terverifikasi otomatis: input tim operator.';

    /**
     * Record one observation at a monitoring point with one or more commodity prices.
     *
     * Submissions from the operator input team are approved on entry; everyone else waits for a verifier.
     *
     * @param  list<array{commodity_id: int, price: float|int|string, stock?: float|int|string|null, notes?: string|null}>  $items
     */
    public function record(
        User $author,
        int $pointId,
        string $observedAt,
        int $sourceId,
        string $status,
        array $items,
        ?string $notes = null,
        ?string $evidencePath = null,
    ): Report {
        return DB::transaction(function () use ($author, $pointId, $observedAt, $sourceId, $status, $items, $notes, $evidencePath): Report {
            $series = ReportSeries::create([
                'point_id' => $pointId,
                'observed_at' => $observedAt,
            ]);

            $report = Report::create([
                'series_id' => $series->id,
                'source_id' => $sourceId,
                'status' => $status,
                'submitted_by' => $author->id,
                'version_no' => 1,
                'is_demo' => false,
                'notes' => $notes,
                'evidence_path' => $evidencePath,
            ]);

            $commodities = Commodity::query()->whereIn('id', array_column($items, 'commodity_id'))->get()->keyBy('id');

            foreach ($items as $item) {
                $previousPrice = $this->previousPrice($pointId, (int) $item['commodity_id'], $observedAt);
                $changeAmount = $previousPrice ? (float) $item['price'] - $previousPrice : 0;

                ReportItem::create([
                    'report_id' => $report->id,
                    'commodity_id' => $item['commodity_id'],
                    'price' => $item['price'],
                    'stock' => $item['stock'] ?? 0,
                    'unit' => $commodities->get((int) $item['commodity_id'])?->base_unit ?? 'kg',
                    'price_change_pct' => $previousPrice ? round(($changeAmount / $previousPrice) * 100, 2) : 0,
                    'price_change_amount' => $changeAmount,
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            $this->approveOnEntry($report, $author);

            return $report;
        });
    }

    /**
     * Approve a submitted report straight away when its author belongs to the operator input team.
     */
    public function approveOnEntry(Report $report, User $author): bool
    {
        if ($report->status !== 'submitted' || ! $author->canSelfVerifyPrices()) {
            return false;
        }

        $report->update([
            'status' => 'approved',
            'verified_by' => $author->id,
            'verified_at' => now(),
        ]);

        DB::table('verification_events')->insert([
            'report_id' => $report->id,
            'actor_id' => $author->id,
            'decision' => 'approved',
            'note' => self::AUTO_VERIFY_NOTE,
            'created_at' => now(),
        ]);

        return true;
    }

    private function previousPrice(int $pointId, int $commodityId, string $observedAt): ?float
    {
        $price = DB::table('report_items as ri')
            ->join('reports as r', 'r.id', '=', 'ri.report_id')
            ->join('report_series as rs', 'rs.id', '=', 'r.series_id')
            ->where('ri.commodity_id', $commodityId)
            ->where('rs.point_id', $pointId)
            ->where('rs.observed_at', '<', $observedAt)
            ->orderByDesc('rs.observed_at')
            ->value('ri.price');

        return $price > 0 ? (float) $price : null;
    }
}
