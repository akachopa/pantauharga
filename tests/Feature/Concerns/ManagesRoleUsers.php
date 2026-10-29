<?php

namespace Tests\Feature\Concerns;

use App\Models\PointAssignment;
use App\Models\Report;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;

trait ManagesRoleUsers
{
    /** @var list<int> */
    private array $createdUserIds = [];

    /**
     * @param  list<int>  $pointIds
     * @param  array<string, mixed>  $attributes
     */
    private function userWithRole(string $roleCode, array $pointIds = [], array $attributes = []): User
    {
        $user = User::factory()->create($attributes + [
            'organization_id' => 1,
            'is_active' => true,
            'is_internal' => $roleCode !== 'mitra',
            'is_demo' => false,
        ]);
        $user->roles()->attach(Role::query()->where('code', $roleCode)->value('id'));

        foreach ($pointIds as $pointId) {
            PointAssignment::create(['user_id' => $user->id, 'point_id' => $pointId, 'starts_on' => '2026-01-01']);
        }

        $this->createdUserIds[] = $user->id;

        return $user->fresh();
    }

    protected function tearDown(): void
    {
        if ($this->createdUserIds !== []) {
            $reportIds = Report::query()->whereIn('submitted_by', $this->createdUserIds)->pluck('id');
            $seriesIds = Report::query()->whereIn('id', $reportIds)->pluck('series_id');

            DB::table('verification_events')->whereIn('report_id', $reportIds)->delete();
            DB::table('report_items')->whereIn('report_id', $reportIds)->delete();
            Report::query()->whereIn('id', $reportIds)->delete();
            DB::table('report_series')->whereIn('id', $seriesIds)->delete();
            PointAssignment::query()->whereIn('user_id', $this->createdUserIds)->delete();
            DB::table('user_roles')->whereIn('user_id', $this->createdUserIds)->delete();
            User::query()->whereIn('id', $this->createdUserIds)->delete();
        }

        parent::tearDown();
    }
}
