<?php

namespace Tests\Feature;

use Tests\Feature\Concerns\ManagesRoleUsers;
use Tests\TestCase;

class AccountLoginTest extends TestCase
{
    use ManagesRoleUsers;

    protected function setUp(): void
    {
        parent::setUp();

        config(['auth.demo_auto_login' => false]);
    }

    public function test_guest_is_sent_to_login_page_for_internal_pages(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
        $this->get(route('mitra.dashboard'))->assertRedirect(route('login'));
    }

    public function test_login_page_renders(): void
    {
        $this->get(route('login'))->assertOk()->assertSee('Masuk');
    }

    public function test_operator_logs_in_to_internal_dashboard(): void
    {
        $operator = $this->userWithRole('operator');

        $response = $this->post(route('login.store'), ['email' => $operator->email, 'password' => 'password']);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($operator);
    }

    public function test_partner_logs_in_to_partner_dashboard(): void
    {
        $partner = $this->userWithRole('mitra');

        $response = $this->post(route('login.store'), ['email' => $partner->email, 'password' => 'password']);

        $response->assertRedirect(route('mitra.dashboard'));
        $this->assertAuthenticatedAs($partner);
    }

    public function test_wrong_password_is_rejected(): void
    {
        $operator = $this->userWithRole('operator');

        $response = $this->from(route('login'))->post(route('login.store'), ['email' => $operator->email, 'password' => 'salah-sandi']);

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors(['email' => 'Email atau kata sandi salah, atau akun tidak aktif.']);
        $this->assertGuest();
    }

    public function test_inactive_account_cannot_log_in(): void
    {
        $operator = $this->userWithRole('operator', attributes: ['is_active' => false]);

        $this->post(route('login.store'), ['email' => $operator->email, 'password' => 'password'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_user_can_log_out(): void
    {
        $operator = $this->userWithRole('operator');

        $this->actingAs($operator)->post(route('logout'))->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_role_switcher_is_unavailable_without_demo_mode(): void
    {
        $admin = $this->userWithRole('admin');
        $operator = $this->userWithRole('operator');

        $this->actingAs($operator)->get(route('switch.user', $admin->id))->assertNotFound();
        $this->assertAuthenticatedAs($operator);
    }

    public function test_settings_are_restricted_to_administrators(): void
    {
        $operator = $this->userWithRole('operator');
        $admin = $this->userWithRole('admin');

        $this->actingAs($operator)->get(route('settings.index'))->assertForbidden();
        $this->actingAs($admin)->get(route('settings.index'))->assertOk();
    }
}
