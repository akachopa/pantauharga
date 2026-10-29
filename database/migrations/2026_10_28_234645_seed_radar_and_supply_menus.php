<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $radarId = DB::table('app_menus')->where('code', 'radar')->value('id');
        if (! $radarId) {
            $radarId = 8;
            DB::table('app_menus')->insert([
                'id' => $radarId,
                'code' => 'radar',
                'name' => 'Radar',
                'sort_order' => 8,
            ]);
        }

        $supplyId = DB::table('app_menus')->where('code', 'supply')->value('id');
        if (! $supplyId) {
            $supplyId = 9;
            DB::table('app_menus')->insert([
                'id' => $supplyId,
                'code' => 'supply',
                'name' => 'Rantai Pasok',
                'sort_order' => 9,
            ]);
        }

        $analyticsId = DB::table('permissions')->where('code', 'analytics.read')->value('id');
        $supplyPermId = DB::table('permissions')->where('code', 'supply.manage')->value('id');

        if ($analyticsId) {
            DB::table('menu_permissions')->updateOrInsert(
                ['menu_id' => $radarId, 'permission_id' => $analyticsId],
                []
            );
        }

        if ($supplyPermId) {
            DB::table('menu_permissions')->updateOrInsert(
                ['menu_id' => $supplyId, 'permission_id' => $supplyPermId],
                []
            );
        }
    }

    public function down(): void
    {
        $ids = DB::table('app_menus')->whereIn('code', ['radar', 'supply'])->pluck('id');
        DB::table('menu_permissions')->whereIn('menu_id', $ids)->delete();
        DB::table('app_menus')->whereIn('code', ['radar', 'supply'])->delete();
    }
};
