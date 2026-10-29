<?php

namespace Tests\Feature;

use App\Models\Commodity;
use App\Models\DataSource;
use App\Models\MonitoringPoint;
use App\Models\Report;
use Tests\Feature\Concerns\ManagesRoleUsers;
use Tests\TestCase;

class MitraPortalTest extends TestCase
{
    use ManagesRoleUsers;

    public function test_partner_sees_dashboard_with_assigned_location(): void
    {
        $point = MonitoringPoint::query()->where('is_active', true)->firstOrFail();
        $partner = $this->userWithRole('mitra', [$point->id]);

        $response = $this->actingAs($partner)->get(route('mitra.dashboard'));

        $response->assertOk();
        $response->assertSee('Kirim harga hari ini');
        $response->assertViewHas('points', fn ($points): bool => $points->pluck('id')->all() === [$point->id]);
    }

    public function test_partner_without_assignment_is_told_to_contact_admin(): void
    {
        $partner = $this->userWithRole('mitra');

        $this->actingAs($partner)->get(route('mitra.dashboard'))
            ->assertOk()
            ->assertSee('Akun Anda belum ditugaskan ke lokasi pantau.');
    }

    public function test_partner_submission_waits_for_verification(): void
    {
        $point = MonitoringPoint::query()->where('is_active', true)->firstOrFail();
        $partner = $this->userWithRole('mitra', [$point->id]);
        [$rice, $sugar] = Commodity::query()->where('is_active', true)->orderBy('id')->take(2)->get();

        $response = $this->actingAs($partner)->post(route('mitra.reports.store'), [
            'point_id' => $point->id,
            'observed_at' => now()->toDateString(),
            'items' => [
                ['commodity_id' => $rice->id, 'price' => 14200, 'stock' => 30],
                ['commodity_id' => $sugar->id, 'price' => 17800],
            ],
        ]);

        $response->assertRedirect(route('mitra.dashboard'));
        $response->assertSessionHas('success', 'Harga terkirim dan menunggu verifikasi petugas dinas.');

        $report = Report::query()->with('items')->where('submitted_by', $partner->id)->sole();
        $this->assertSame('submitted', $report->status);
        $this->assertNull($report->verified_by);
        $this->assertSame((int) DataSource::query()->where('code', 'SRC_MITRA')->value('id'), (int) $report->source_id);
        $this->assertEqualsCanonicalizing([14200.0, 17800.0], $report->items->map(fn ($item): float => (float) $item->price)->all());
    }

    public function test_partner_cannot_submit_for_unassigned_location(): void
    {
        [$assigned, $other] = MonitoringPoint::query()->where('is_active', true)->orderBy('id')->take(2)->get();
        $partner = $this->userWithRole('mitra', [$assigned->id]);

        $response = $this->actingAs($partner)->post(route('mitra.reports.store'), [
            'point_id' => $other->id,
            'observed_at' => now()->toDateString(),
            'items' => [['commodity_id' => Commodity::query()->value('id'), 'price' => 15000]],
        ]);

        $response->assertSessionHasErrors(['point_id' => 'Lokasi ini bukan titik pantau yang ditugaskan untuk akun Anda.']);
        $this->assertFalse(Report::query()->where('submitted_by', $partner->id)->exists());
    }

    public function test_partner_cannot_repeat_a_commodity_or_send_future_dates(): void
    {
        $point = MonitoringPoint::query()->where('is_active', true)->firstOrFail();
        $partner = $this->userWithRole('mitra', [$point->id]);
        $commodityId = Commodity::query()->where('is_active', true)->value('id');

        $response = $this->actingAs($partner)->post(route('mitra.reports.store'), [
            'point_id' => $point->id,
            'observed_at' => now()->addDay()->toDateString(),
            'items' => [
                ['commodity_id' => $commodityId, 'price' => 15000],
                ['commodity_id' => $commodityId, 'price' => 16000],
            ],
        ]);

        $response->assertSessionHasErrors(['observed_at', 'items.0.commodity_id']);
        $this->assertFalse(Report::query()->where('submitted_by', $partner->id)->exists());
    }

    public function test_partner_is_redirected_away_from_internal_pages(): void
    {
        $partner = $this->userWithRole('mitra');

        $this->actingAs($partner)->get(route('dashboard'))->assertRedirect(route('mitra.dashboard'));
        $this->actingAs($partner)->post(route('prices.store'), [])->assertRedirect(route('mitra.dashboard'));
    }

    public function test_internal_staff_cannot_open_partner_portal(): void
    {
        $operator = $this->userWithRole('operator');

        $this->actingAs($operator)->get(route('mitra.dashboard'))->assertForbidden();
    }

    public function test_admin_assigns_and_removes_partner_locations(): void
    {
        [$first, $second] = MonitoringPoint::query()->where('is_active', true)->orderBy('id')->take(2)->get();
        $admin = $this->userWithRole('admin');
        $partner = $this->userWithRole('mitra', [$first->id]);
        $mitraRoleId = $partner->roles->first()->id;

        $this->actingAs($admin)->put(route('settings.users.update', $partner->id), [
            'name' => $partner->name,
            'email' => $partner->email,
            'role_id' => $mitraRoleId,
            'is_active' => 1,
            'assign_points' => 1,
            'point_ids' => [$second->id],
        ])->assertRedirect(route('settings.index', ['tab' => 'pengguna', 'user' => $partner->id]));

        $this->actingAs($partner)->get(route('mitra.dashboard'))
            ->assertViewHas('points', fn ($points): bool => $points->pluck('id')->all() === [$second->id]);
    }
}
