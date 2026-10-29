<?php

namespace Database\Factories;

use App\Models\Commodity;
use App\Models\CommunityPriceReport;
use App\Models\Market;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CommunityPriceReport>
 */
class CommunityPriceReportFactory extends Factory
{
    protected $model = CommunityPriceReport::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'commodity_id' => Commodity::query()->value('id') ?? 1,
            'market_id' => Market::query()->value('id') ?? 1,
            'reported_price' => 18500,
            'official_price' => 18000,
            'reporter_name' => fake()->name(),
            'phone' => null,
            'note' => 'Harga di kios berbeda dari sistem.',
            'status' => 'pending',
            'reviewed_at' => null,
            'created_at' => now(),
        ];
    }
}
