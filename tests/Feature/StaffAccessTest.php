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

    public function test_role_downgrade_applies_without_relogin(): void
    {
        $staff = $this->makeStaff('owner');
        $this->actingAsStaff($staff);
        $this->get('/dashboard/visitors')->assertOk();

        $staff->update(['role' => 'staff_pesanan']);

        $this->get('/dashboard/visitors')->assertRedirect(route('dashboard'));
    }

    public function test_product_admin_cannot_open_orders_or_database_export(): void
    {
        $this->actingAsStaff($this->makeStaff('admin_produk'));

        $this->get('/dashboard/orders')->assertRedirect(route('dashboard'));
        $this->get('/dashboard/import-export/database/export')->assertRedirect(route('dashboard'));
        $this->get('/dashboard/import-export/storage/export')->assertRedirect(route('dashboard'));
    }

    public function test_order_staff_cannot_manage_products(): void
    {
        $this->actingAsStaff($this->makeStaff('staff_pesanan'));

        $this->get('/dashboard/produk')->assertRedirect(route('dashboard'));
    }

    public function test_customer_login_does_not_grant_staff_access(): void
    {
        $user = \App\Models\User::factory()->create();

        $this->actingAs($user)->get('/dashboard')->assertRedirect(route('crew.login'));
    }
}