<?php

namespace Tests\Feature;

class StaffAccessTest extends StoreTestCase
{
    public function test_guest_is_redirected_to_crew_login(): void
    {
        $this->get('/dashboard')->assertRedirect(route('crew.login'));
    }

    public function test_crew_login_works_for_active_staff(): void
    {
        $this->makeStaff('owner');

        $this->post('/crew-portal', ['username' => 'crew1', 'password' => 'rahasia123'])
            ->assertRedirect(route('dashboard'));
    }

    public function test_inactive_staff_cannot_log_in(): void
    {
        $this->makeStaff('owner', false);

        $this->post('/crew-portal', ['username' => 'crew1', 'password' => 'rahasia123'])
            ->assertSessionHasErrors('login');
    }

    public function test_staff_deactivated_while_logged_in_loses_access_immediately(): void
    {
        $staff = $this->makeStaff('owner');
        $this->actingAsStaff($staff);

        $this->get('/dashboard/orders')->assertOk();

        $staff->update(['is_active' => false]);

        // Dulu role/status dipercaya dari session sampai logout, jadi ini masih 200
        $this->get('/dashboard/orders')->assertRedirect(route('crew.login'));
    }

    public function test_deleted_staff_loses_access(): void
    {
        $staff = $this->makeStaff('owner');
        $this->actingAsStaff($staff);
        $staff->delete();

        $this->get('/dashboard/orders')->assertRedirect(route('crew.login'));
    }

    public function test_every_active_staff_can_open_every_dashboard_page_regardless_of_role(): void
    {
        $pages = [
            '/dashboard',
            '/dashboard/orders',
            '/dashboard/members',
            '/dashboard/produk',
            '/dashboard/visitors',
            '/dashboard/import-export',
        ];

        // Role belum dipakai membatasi akses: semua akun staf aktif punya akses yang sama
        foreach (['owner', 'admin_produk', 'staff_pesanan'] as $role) {
            $this->actingAsStaff($this->makeStaff($role, true, "crew-{$role}"));

            foreach ($pages as $page) {
                $this->get($page)->assertOk();
            }
        }
    }

    public function test_dashboard_pages_stay_closed_to_guests(): void
    {
        foreach (['/dashboard', '/dashboard/orders', '/dashboard/members', '/dashboard/import-export/database/export'] as $page) {
            $this->get($page)->assertRedirect(route('crew.login'));
        }
    }

    public function test_customer_login_does_not_grant_staff_access(): void
    {
        $user = \App\Models\User::factory()->create();

        $this->actingAs($user)->get('/dashboard')->assertRedirect(route('crew.login'));
    }
}