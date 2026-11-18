<?php

namespace Tests\Feature;

use App\Models\User;

class MemberTest extends StoreTestCase
{
    private function staff(): static
    {
        return $this->actingAsStaff($this->makeStaff());
    }

    private function member(string $name, string $email, array $attrs = []): User
    {
        return User::factory()->create(array_merge(['name' => $name, 'email' => $email], $attrs));
    }

    public function test_guest_and_customers_cannot_open_member_pages(): void
    {
        $member = $this->member('Siti', 'siti@example.com');

        $this->get('/dashboard/members')->assertRedirect(route('crew.login'));
        $this->get("/dashboard/members/{$member->id}")->assertRedirect(route('crew.login'));
        $this->actingAs($member)->get('/dashboard/members')->assertRedirect(route('crew.login'));
    }

    public function test_member_list_shows_profile_order_count_and_completed_spending(): void
    {
        $siti = $this->member('Siti Aminah', 'siti@example.com', ['phone' => '081234567890']);
        $this->member('Budi Santoso', 'budi@example.com');

        $accessory = $this->makeAccessory(10, 100000);
        $this->makeOrder($accessory, null, 1, ['user_id' => $siti->id, 'status' => 'completed']); // 110.000
        $this->makeOrder($accessory, null, 1, ['user_id' => $siti->id, 'status' => 'pending']);   // tidak dihitung

        $this->staff()->get('/dashboard/members')
            ->assertOk()
            ->assertSee('Siti Aminah')
            ->assertSee('siti@example.com')
            ->assertSee('081234567890')
            ->assertSee('Budi Santoso')
            ->assertSee('2 pesanan')
            ->assertSee('Rp110.000')
            ->assertSee('Daftar Anggota (2)');
    }

    public function test_member_list_search_by_name_email_and_phone(): void
    {
        $this->member('Siti Aminah', 'siti@example.com', ['phone' => '081234567890']);
        $this->member('Budi Santoso', 'budi@example.com', ['phone' => '089900001111']);

        $this->staff();

        $this->get(route('dashboard.members', ['q' => 'siti']))
            ->assertSee('Siti Aminah')->assertDontSee('Budi Santoso')
            ->assertSee('value="siti"', false);

        $this->get(route('dashboard.members', ['q' => 'budi@example']))
            ->assertSee('Budi Santoso')->assertDontSee('Siti Aminah');

        $this->get(route('dashboard.members', ['q' => '0899']))
            ->assertSee('Budi Santoso')->assertDontSee('Siti Aminah');

        $this->get(route('dashboard.members', ['q' => 'tidak-ada']))
            ->assertOk()
            ->assertSee('Tidak ada anggota yang cocok');
    }

    public function test_search_treats_percent_and_underscore_literally(): void
    {
        $this->member('Siti Aminah', 'siti@example.com');

        // "%" bukan wildcard: tidak boleh cocok ke semua anggota
        $this->staff()->get(route('dashboard.members', ['q' => '%']))
            ->assertOk()
            ->assertDontSee('Siti Aminah');
    }

    public function test_search_ignores_non_string_query(): void
    {
        $this->member('Siti Aminah', 'siti@example.com');

        $this->staff()->get('/dashboard/members?q[]=x')->assertOk()->assertSee('Siti Aminah');
    }

    public function test_member_list_is_paginated(): void
    {
        User::factory()->count(17)->create();

        $this->staff()->get('/dashboard/members')->assertOk()->assertSee('Daftar Anggota (17)');
        $this->get('/dashboard/members?page=2')->assertOk();
    }

    public function test_member_detail_shows_profile_status_counts_and_order_history(): void
    {
        $siti = $this->member('Siti Aminah', 'siti@example.com', [
            'phone'   => '081234567890',
            'address' => 'Jl. Melati No. 5, Bandung',
        ]);
        $other = $this->member('Budi Santoso', 'budi@example.com');
        $accessory = $this->makeAccessory(10, 50000);

        $done = $this->makeOrder($accessory, null, 2, ['user_id' => $siti->id, 'status' => 'completed']); // 110.000
        $pending = $this->makeOrder($accessory, null, 1, ['user_id' => $siti->id]);
        $foreign = $this->makeOrder($accessory, null, 1, ['user_id' => $other->id]);

        $this->staff()->get("/dashboard/members/{$siti->id}")
            ->assertOk()
            ->assertSee('Siti Aminah')
            ->assertSee('siti@example.com')
            ->assertSee('081234567890')
            ->assertSee('Jl. Melati No. 5, Bandung')
            ->assertSee('Rp110.000')                         // total belanja = pesanan selesai saja
            ->assertSee($done->order_number)
            ->assertSee($pending->order_number)
            ->assertDontSee($foreign->order_number)          // pesanan anggota lain tidak bocor
            ->assertSee(route('dashboard.orders.show', $done), false);
    }

    public function test_member_detail_without_orders_and_missing_phone(): void
    {
        $member = $this->member('Siti Aminah', 'siti@example.com');

        $this->staff()->get("/dashboard/members/{$member->id}")
            ->assertOk()
            ->assertSee('Anggota ini belum pernah memesan');
    }

    public function test_unknown_member_is_404(): void
    {
        $this->staff()->get('/dashboard/members/999999')->assertNotFound();
    }

    public function test_member_pages_never_expose_password_hash(): void
    {
        $member = $this->member('Siti Aminah', 'siti@example.com');

        $this->staff()->get('/dashboard/members')->assertDontSee($member->password, false);
        $this->get("/dashboard/members/{$member->id}")->assertDontSee($member->password, false);
    }

    public function test_deleting_account_removes_member_but_keeps_orders(): void
    {
        $member = $this->member('Siti Aminah', 'siti@example.com');
        $accessory = $this->makeAccessory(10);
        $order = $this->makeOrder($accessory, null, 1, ['user_id' => $member->id]);

        $member->delete();

        $this->staff()->get('/dashboard/members')->assertDontSee('Siti Aminah');
        $this->assertNull($order->fresh()->user_id);
    }
}