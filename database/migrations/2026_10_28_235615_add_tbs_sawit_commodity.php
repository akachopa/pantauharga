<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::table('commodities')->where('code', 'TBS_SAWIT')->exists()) {
            return;
        }

        DB::table('commodities')->insert([
            'code' => 'TBS_SAWIT',
            'name' => 'TBS Kelapa Sawit',
            'category' => 'Perkebunan',
            'base_unit' => 'kg',
            'icon' => '🌴',
            'reference_price' => null,
            'is_active' => true,
            'created_at' => now(),
        ]);
    }

    public function down(): void
    {
        $commodityId = DB::table('commodities')->where('code', 'TBS_SAWIT')->value('id');
        if (! $commodityId || DB::table('report_items')->where('commodity_id', $commodityId)->exists()) {
            return;
        }

        DB::table('commodities')->where('id', $commodityId)->delete();
    }
};
