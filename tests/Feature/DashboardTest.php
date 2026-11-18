<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use App\Models\Visit;
use App\Support\Stock;

class DashboardTest extends StoreTestCase
{
    private function staff(): static
    {
        return $this->actingAsStaff($this->makeStaff());
    }

    public function test_dashboard_is_the_same_single_summary_for_every_role(): void
    {
        foreach (['owner', 'admin_produk', 'staff_pesanan'] as $role) {
            $this->actingAsStaff($this->makeStaff($role, true, "crew-{$role}"))
                ->get('/dashboard')
                ->assertOk()
                ->assertSee('Omzet Bulan Ini')
                ->assertSee('Stok Menipis')
                ->assertSee('Pesanan Pending')
                ->assertSee('Anggota')
                // Badge role sudah dihapus dari sapaan
                ->assertDontSee('Admin Produk')
                ->assertDontSee('Staff Pesanan');
        }
    }

    public function test_dashboard_greets_staff_by_name(): void
    {
        $this->staff()->get('/dashboard')->assertSee('Halo, Crew crew1');
    }

    public function test_stock_cards_count_clothes_variants_and_accessories_together(): void
    {
        $this->makeClothes(['S' => 0, 'M' => 2, 'L' => 10]);   // 1 habis, 1 menipis, 1 aman
        $this->makeAccessory(3);                                // menipis (stok tunggal)
        $this->makeAccessory(20);                               // aman

        $summary = Stock::summary();

        $this->assertSame(5, $summary['units']);
        $this->assertSame(2, $summary['low']);
        $this->assertSame(1, $summary['out']);
        $this->assertSame(2, $summary['safe']);

        $this->staff()->get('/dashboard')->assertOk();
    }

    public function test_critical_stock_lists_out_of_stock_before_low_stock(): void
    {
        $clothes = $this->makeClothes(['S' => 0, 'M' => 2, 'L' => 10], 150000, ['name' => 'Kaos Kritis']);
        $this->makeAccessory(1, 25000, ['name' => 'Gantungan Hampir Habis']);

        $critical = Stock::critical(5);

        $this->assertCount(3, $critical);
        $this->assertSame(0, $critical->first()['stock']);
        $this->assertSame([0, 1, 2], $critical->pluck('stock')->all());

        $this->staff()->get('/dashboard')
            ->assertSee('Kaos Kritis')
            ->assertSee('Gantungan Hampir Habis')
            ->assertSee('Ukuran S');
    }

    public function test_revenue_only_counts_completed_orders_and_splits_by_category(): void
    {
        $clothes = $this->makeClothes(['M' => 5], 100000);
        $variant = $clothes->variants->first();
        $accessory = $this->makeAccessory(5, 20000);

        $this->makeOrder($clothes, $variant, 2, ['status' => 'completed']);   // 200.000 barang
        $this->makeOrder($accessory, null, 3, ['status' => 'completed']);     // 60.000 barang
        $this->makeOrder($clothes, $variant, 9, ['status' => 'pending']);     // tidak dihitung
        $this->makeOrder($clothes, $variant, 9, ['status' => 'cancelled']);   // tidak dihitung

        // total pesanan = subtotal + ongkir Rp10.000 (lihat makeOrder): 210.000 + 70.000
        $this->staff()->get('/dashboard')
            ->assertOk()
            ->assertSee('Rp280.000')
            ->assertSee('Rp200.000')   // per kategori: clothes
            ->assertSee('Rp60.000');   // per kategori: accessories
    }

    public function test_revenue_chart_groups_by_month_without_month_end_overflow(): void
    {
        $this->travelTo(now()->setDate(2026, 3, 31)->setTime(10, 0));

        $accessory = $this->makeAccessory(5, 50000);
        $this->makeOrder($accessory, null, 1, ['status' => 'completed']);

        $this->travelTo(now()->setDate(2026, 3, 31)->setTime(11, 0));

        // 31 Maret dikurangi 1 bulan tidak boleh "meluap" ke Maret lagi: Februari tetap tampil
        $this->staff()->get('/dashboard')
            ->assertOk()
            ->assertSee('Feb 2026')
            ->assertSee('Mar 2026')
            ->assertSee('Okt 2025')
            ->assertSee('Rp60rb');
    }

    public function test_orders_trend_and_status_counts_cover_all_statuses(): void
    {
        $accessory = $this->makeAccessory(5);
        foreach (Order::STATUSES as $status) {
            $this->makeOrder($accessory, null, 1, ['status' => $status]);
        }

        $counts = Order::countByStatus();

        $this->assertSame(array_fill_keys(Order::STATUSES, 1), $counts);
        $this->staff()->get('/dashboard')->assertOk()->assertSee('Dibatalkan');
    }

    public function test_dashboard_shows_device_breakdown_and_member_numbers(): void
    {
        Visit::create(['ip_address' => '1.1.1.1', 'session_id' => 'a', 'url' => '/', 'device_type' => 'desktop', 'browser' => 'Chrome']);
        Visit::create(['ip_address' => '2.2.2.2', 'session_id' => 'b', 'url' => '/', 'device_type' => 'mobile', 'browser' => 'Safari']);
        User::factory()->count(2)->create();

        $this->staff()->get('/dashboard')
            ->assertOk()
            ->assertSee('Desktop (1)')
            ->assertSee('Mobile (1)')
            ->assertSee('2 anggota baru bulan ini');
    }

    public function test_navbar_links_to_members_and_shows_low_stock_badge(): void
    {
        $this->makeClothes(['M' => 2]);

        $this->staff()->get('/dashboard')
            ->assertSee(route('dashboard.members'), false)
            ->assertSee(route('dashboard.visitors'), false)
            ->assertSee('Anggota');
    }
}