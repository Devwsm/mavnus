<?php

namespace Tests\Feature;

use App\Models\Order;
use Carbon\Carbon;

class OrderFilterTest extends StoreTestCase
{
    private function staff(): static
    {
        return $this->actingAsStaff($this->makeStaff('staff_pesanan'));
    }

    private function orderFor(string $name, array $attrs = []): Order
    {
        return $this->makeOrder($this->makeAccessory(5), null, 1, array_merge(['customer_name' => $name], $attrs));
    }

    private function createdAt(Order $order, string $datetime): Order
    {
        $order->forceFill(['created_at' => Carbon::parse($datetime)])->save();

        return $order;
    }

    public function test_search_by_customer_name(): void
    {
        $siti = $this->orderFor('Siti Aminah');
        $budi = $this->orderFor('Budi Santoso');

        $this->staff()->get(route('dashboard.orders', ['q' => 'siti']))
            ->assertOk()
            ->assertSee($siti->order_number)
            ->assertDontSee($budi->order_number)
            ->assertSee('value="siti"', false); // kata kunci tetap terisi di kolom cari
    }

    public function test_search_by_order_number_phone_and_email(): void
    {
        $a = $this->orderFor('Siti', ['customer_phone' => '0811111111', 'customer_email' => 'siti@contoh.test']);
        $b = $this->orderFor('Budi', ['customer_phone' => '0822222222', 'customer_email' => 'budi@contoh.test']);

        $this->staff()->get(route('dashboard.orders', ['q' => substr($a->order_number, -6)]))
            ->assertSee($a->order_number)->assertDontSee($b->order_number);

        $this->staff()->get(route('dashboard.orders', ['q' => '0822222']))
            ->assertSee($b->order_number)->assertDontSee($a->order_number);

        $this->staff()->get(route('dashboard.orders', ['q' => 'siti@contoh']))
            ->assertSee($a->order_number)->assertDontSee($b->order_number);
    }

    public function test_wildcard_characters_are_searched_literally(): void
    {
        $a = $this->orderFor('Siti');
        $b = $this->orderFor('Budi');

        foreach (['%', '_'] as $wildcard) {
            $this->staff()->get(route('dashboard.orders', ['q' => $wildcard]))
                ->assertOk()
                ->assertDontSee($a->order_number)
                ->assertDontSee($b->order_number)
                ->assertSee('Tidak ada pesanan yang cocok dengan filter.');
        }
    }

    public function test_date_range_is_inclusive_of_the_whole_end_day(): void
    {
        $januari = $this->createdAt($this->orderFor('Siti'), '2026-01-10 12:00:00');
        $februari = $this->createdAt($this->orderFor('Budi'), '2026-02-10 23:59:00');

        $this->staff()->get(route('dashboard.orders', ['from' => '2026-02-01']))
            ->assertSee($februari->order_number)->assertDontSee($januari->order_number);

        $this->staff()->get(route('dashboard.orders', ['to' => '2026-01-31']))
            ->assertSee($januari->order_number)->assertDontSee($februari->order_number);

        // 'to' = hari yang sama dengan pesanan pukul 23:59 -> tetap ikut
        $this->staff()->get(route('dashboard.orders', ['from' => '2026-02-10', 'to' => '2026-02-10']))
            ->assertSee($februari->order_number)->assertDontSee($januari->order_number);
    }

    public function test_reversed_date_range_is_swapped(): void
    {
        $januari = $this->createdAt($this->orderFor('Siti'), '2026-01-10 12:00:00');
        $februari = $this->createdAt($this->orderFor('Budi'), '2026-02-10 12:00:00');

        $this->staff()->get(route('dashboard.orders', ['from' => '2026-02-28', 'to' => '2026-01-01']))
            ->assertSee($januari->order_number)->assertSee($februari->order_number);
    }

    public function test_invalid_filter_values_are_ignored_instead_of_failing(): void
    {
        $a = $this->orderFor('Siti');
        $b = $this->orderFor('Budi');

        $this->staff()->get('/dashboard/orders?from=bukan-tanggal&to=2026-13-45&status=ngawur&q[]=x')
            ->assertOk()
            ->assertSee($a->order_number)
            ->assertSee($b->order_number);
    }

    public function test_status_filter_includes_cancelled_orders(): void
    {
        $batal = $this->orderFor('Siti', ['status' => 'cancelled']);
        $menunggu = $this->orderFor('Budi');

        $this->staff()->get(route('dashboard.orders', ['status' => 'cancelled']))
            ->assertSee($batal->order_number)->assertDontSee($menunggu->order_number);
    }

    public function test_normalize_filters_cleans_every_field(): void
    {
        $clean = Order::normalizeFilters([
            'q'      => '  siti  ',
            'status' => 'completed',
            'from'   => '2026-02-31', // tanggal yang tidak ada
            'to'     => ['x'],        // bukan string
        ]);

        $this->assertSame(['q' => 'siti', 'status' => 'completed', 'from' => '', 'to' => ''], $clean);
    }
}