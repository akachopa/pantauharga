<?php

namespace App;

use App\Models\Report;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class HeaderAlertBuilder
{
    public function __construct(private PolicyRuleEvaluator $policyRuleEvaluator) {}

    /**
     * @return Collection<int, array{title: string, detail: string, tone: string, url: string}>
     */
    public function build(int $limit = 5): Collection
    {
        $alerts = collect();

        foreach ($this->policyRuleEvaluator->evaluate()['cases'] as $case) {
            $alerts->push([
                'title' => $case['commodity'],
                'detail' => ($case['observed_value'] ?? 'Ambang terlampaui').' di '.$case['point'],
                'tone' => 'rose',
                'url' => route('policies.index', ['tab' => 'terpicu']),
            ]);
        }

        $pending = Report::query()->whereIn('status', ['submitted', 'draft'])->count();
        if ($pending > 0) {
            $alerts->push([
                'title' => 'Perlu verifikasi',
                'detail' => "{$pending} laporan menunggu keputusan verifikator.",
                'tone' => 'amber',
                'url' => route('prices.index', ['tab' => 'verifikasi']),
            ]);
        }

        $lowStock = DB::table('report_items as ri')
            ->join('reports as r', 'r.id', '=', 'ri.report_id')
            ->join('commodities as c', 'c.id', '=', 'ri.commodity_id')
            ->where('r.status', 'approved')
            ->where('ri.stock', '<', 30)
            ->select('c.name', DB::raw('MIN(ri.stock) as min_stock'))
            ->groupBy('c.id', 'c.name')
            ->orderBy('min_stock')
            ->limit(2)
            ->get();

        foreach ($lowStock as $row) {
            $alerts->push([
                'title' => 'Stok '.$row->name.' menipis',
                'detail' => 'Stok terpantau '.number_format((float) $row->min_stock, 0, ',', '.').' satuan.',
                'tone' => 'amber',
                'url' => route('actions.index'),
            ]);
        }

        return $alerts->unique(fn (array $item) => $item['title'].'|'.$item['detail'])->take($limit)->values();
    }
}
