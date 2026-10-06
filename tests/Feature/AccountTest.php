<?php

namespace Tests\Feature;

use App\Models\User;

class AccountTest extends StoreTestCase
{
    public function test_account_pages_require_login(): void
    {
        $this->get('/account')->assertRedirect(route('login'));
        $this->get('/account/orders')->assertRedirect(route('login'));
    }

    public function test_register_logs_in_and_redirects(): void
    {
        $this->post('/register', [
            'name' => 'Ani',
            'email' => 'ani@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect();

        $this->assertAuthenticated();
    }

    public function test_login_message_is_same_for_unknown_email_and_wrong_password(): void
    {
        User::factory()->create(['email' => 'ada@example.com', 'password' => bcrypt('benar-benar')]);

        $a = $this->post('/login', ['email' => 'ada@example.com', 'password' => 'salah-salah']);
        $b = $this->post('/login', ['email' => 'tidakada@example.com', 'password' => 'salah-salah']);

        $a->assertSessionHasErrors(['login' => 'Email atau password salah, coba lagi.']);
        $b->assertSessionHasErrors(['login' => 'Email atau password salah, coba lagi.']);
    }

    public function test_cannot_view_another_users_order_detail(): void
    {
        $mine = User::factory()->create();
        $other = User::factory()->create();
        $order = $this->makeOrder($this->makeAccessory(5), null, 1, ['user_id' => $other->id]);

        $this->actingAs($mine)->get(route('account.orders.show', $order))->assertForbidden();
        $this->actingAs($other)->get(route('account.orders.show', $order))->assertOk();
    }

    public function test_orders_list_only_shows_own_orders(): void
    {
        $mine = User::factory()->create();
        $other = User::factory()->create();
        $p = $this->makeAccessory(5);
        $this->makeOrder($p, null, 1, ['user_id' => $mine->id, 'customer_name' => 'PunyaSaya']);
        $this->makeOrder($p, null, 1, ['user_id' => $other->id, 'customer_name' => 'PunyaOrangLain']);

        $this->actingAs($mine)->get('/account/orders')->assertOk();
        $this->assertSame(1, \App\Models\Order::where('user_id', $mine->id)->count());
    }

    public function test_changing_profile_requires_current_password(): void
    {
        $user = User::factory()->create(['password' => bcrypt('password-lama')]);

        $this->actingAs($user)->post('/account', ['name' => 'Baru', 'email' => $user->email, 'current_password' => 'salah'])
            ->assertSessionHasErrors('current_password');

        $this->assertNotSame('Baru', $user->fresh()->name);
    }

    public function test_delete_account_keeps_orders_but_detaches_them(): void
    {
        $user = User::factory()->create(['password' => bcrypt('password-lama')]);
        $order = $this->makeOrder($this->makeAccessory(5), null, 1, ['user_id' => $user->id]);

        $this->actingAs($user)->delete('/account', ['password' => 'password-lama'])->assertRedirect(route('home'));

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
        $this->assertNull($order->fresh()->user_id);
    }

    public function test_staff_session_survives_customer_logout(): void
    {
        $user = User::factory()->create();
        $staff = $this->makeStaff('owner');

        $this->actingAs($user)->actingAsStaff($staff)->post('/logout');

        $this->get('/dashboard')->assertOk();
    }
}