<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Order;
use App\Models\User;
use App\Support\OrderCleanup;

class OrderStatusTest extends StoreTestCase
{
    private function setStatus(Order $order, string $status)
    {
        return $this->actingAsStaff($this->makeStaff('staff_pesanan'))
            ->patch(route('dashboard.orders.updateStatus', $order), ['status' => $status]);
    }

    public function test_normal_flow_pending_to_completed(): void
    {
        $p = $this->makeAccessory(5);
        $order = $this->makeOrder($p);

        foreach (['processing', 'shipped', 'completed'] as $status) {
            $this->setStatus($order, $status)->assertSessionHas('success');
            $this->assertSame($status, $order->fresh()->status);
        }
    }

    public function test_cancelling_restocks_clothes_variant(): void
    {
        $p = $this->makeClothes(['M' => 5]);
        $variant = $p->variants->first();
        $variant->decrement('stock', 2); // stok sudah berkurang saat pesanan dibuat
        $order = $this->makeOrder($p, $variant, 2);

        $this->setStatus($order, 'cancelled')->assertSessionHas('success');

        $this->assertSame('cancelled', $order->fresh()->status);
        $this->assertSame(5, $variant->fresh()->stock);
    }

    public function test_cancelling_restocks_accessory_and_reactivates_sold_out_product(): void
    {
        $p = $this->makeAccessory(2);
        $p->update(['stock' => 0, 'is_active' => false]);
        $order = $this->makeOrder($p, null, 2);

        $this->setStatus($order, 'cancelled');

        $this->assertSame(2, (int) $p->fresh()->stock);
        $this->assertTrue($p->fresh()->is_active);
    }

    public function test_cancelled_order_cannot_be_reopened(): void
    {
        $p = $this->makeAccessory(5);
        $order = $this->makeOrder($p, null, 1, ['status' => 'cancelled']);

        $this->setStatus($order, 'processing')->assertSessionHas('error');

        $this->assertSame('cancelled', $order->fresh()->status);
    }

    public function test_cancelling_twice_does_not_restock_twice(): void
    {
        $p = $this->makeAccessory(5);
        $p->decrement('stock', 2);
        $order = $this->makeOrder($p, null, 2);

        $this->setStatus($order, 'cancelled');
        $this->setStatus($order, 'cancelled');

        $this->assertSame(5, (int) $p->fresh()->stock);
    }

    public function test_cannot_go_back_to_pending(): void
    {
        $order = $this->makeOrder($this->makeAccessory(5), null, 1, ['status' => 'processing']);

        $this->setStatus($order, 'pending')->assertSessionHas('error');

        $this->assertSame('processing', $order->fresh()->status);
    }

    public function test_invalid_status_value_is_rejected(): void
    {
        $order = $this->makeOrder($this->makeAccessory(5));

        $this->setStatus($order, 'hacked')->assertSessionHasErrors('status');
    }

    public function test_status_dropdown_only_offers_valid_next_statuses(): void
    {
        $order = $this->makeOrder($this->makeAccessory(5), null, 1, ['status' => 'cancelled']);

        $html = $this->actingAsStaff($this->makeStaff('owner'))
            ->get(route('dashboard.orders.show', $order))->assertOk()->getContent();

        $this->assertStringNotContainsString('value="processing"', $html);
    }

    // ---------- pembersihan pesanan kedaluwarsa ----------

    public function test_expired_unpaid_order_is_cancelled_not_deleted_and_stock_returned(): void
    {
        $p = $this->makeClothes(['M' => 5]);
        $variant = $p->variants->first();
        $variant->decrement('stock', 2);
        $order = $this->makeOrder($p, $variant, 2);
        $order->forceFill(['created_at' => now()->subMinutes(61)])->save();

        $this->assertSame(1, OrderCleanup::run());

        // Riwayat tetap ada: statusnya batal + kedaluwarsa, item pesanan tidak ikut hilang
        $fresh = $order->fresh();
        $this->assertNotNull($fresh);
        $this->assertSame('cancelled', $fresh->status);
        $this->assertSame('expired', $fresh->payment_status);
        $this->assertTrue($fresh->isExpired());
        $this->assertSame('Kedaluwarsa', $fresh->statusLabel());
        $this->assertCount(1, $fresh->items);
        $this->assertSame(5, $variant->fresh()->stock);
    }

    public function test_expired_order_snapshot_photo_is_removed_but_order_is_kept(): void
    {
        Storage::fake('public');
        $p = $this->makeAccessory(5);
        $order = $this->makeOrder($p, null, 1);
        $path = 'orders/' . $order->order_number . '/foto.webp';
        Storage::disk('public')->put($path, 'x');
        $order->items()->update(['product_image' => $path]);
        $order->forceFill(['created_at' => now()->subMinutes(61)])->save();

        OrderCleanup::run();

        $this->assertFalse(Storage::disk('public')->exists($path));
        $this->assertNull($order->fresh()->items->first()->product_image);
    }

    public function test_expiry_does_not_restock_twice(): void
    {
        $p = $this->makeAccessory(5);
        $p->decrement('stock', 2); // stok sudah berkurang saat pesanan dibuat
        $order = $this->makeOrder($p, null, 2);
        $order->forceFill(['created_at' => now()->subMinutes(61)])->save();

        $this->assertSame(1, OrderCleanup::run());
        $this->assertSame(0, OrderCleanup::run());

        $this->assertSame(5, (int) $p->fresh()->stock);
    }

    public function test_manually_cancelled_order_is_not_labelled_expired(): void
    {
        $order = $this->makeOrder($this->makeAccessory(5));

        $this->setStatus($order, 'cancelled');

        $this->assertFalse($order->fresh()->isExpired());
        $this->assertSame('Cancelled', $order->fresh()->statusLabel());
    }

    public function test_customer_and_staff_still_see_expired_order(): void
    {
        $user = User::factory()->create();
        $order = $this->makeOrder($this->makeAccessory(5), null, 1, ['user_id' => $user->id]);
        $order->forceFill(['created_at' => now()->subMinutes(61)])->save();
        OrderCleanup::run();

        $this->actingAs($user)->get(route('account.orders'))
            ->assertOk()->assertSee($order->order_number)->assertSee('Kedaluwarsa');

        $this->actingAsStaff($this->makeStaff('owner'))
            ->get(route('dashboard.orders', ['status' => 'cancelled']))
            ->assertOk()->assertSee($order->order_number)->assertSee('Kedaluwarsa');
    }

    public function test_fresh_order_is_not_expired(): void
    {
        $order = $this->makeOrder($this->makeAccessory(5));

        $this->assertSame(0, OrderCleanup::run());
        $this->assertDatabaseHas('orders', ['id_order' => $order->id_order]);
    }

    public function test_order_already_processed_by_staff_is_not_deleted(): void
    {
        $order = $this->makeOrder($this->makeAccessory(5), null, 1, ['status' => 'processing']);
        $order->forceFill(['created_at' => now()->subDay()])->save();

        $this->assertSame(0, OrderCleanup::run());
        $this->assertDatabaseHas('orders', ['id_order' => $order->id_order]);
    }

    public function test_expiry_uses_config_value_not_raw_env(): void
    {
        config(['mavnus.order_expire_minutes' => 5]);
        $order = $this->makeOrder($this->makeAccessory(5));
        $order->forceFill(['created_at' => now()->subMinutes(10)])->save();

        $this->assertSame(1, OrderCleanup::run());
    }

    public function test_old_cart_rows_are_pruned(): void
    {
        $p = $this->makeAccessory(5);
        DB::table('cart_items')->insert([
            ['session_id' => 'lama', 'product_id' => $p->id_product, 'quantity' => 1, 'created_at' => now()->subDays(40), 'updated_at' => now()->subDays(40)],
            ['session_id' => 'baru', 'product_id' => $p->id_product, 'quantity' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);

        OrderCleanup::run();

        $this->assertDatabaseMissing('cart_items', ['session_id' => 'lama']);
        $this->assertDatabaseHas('cart_items', ['session_id' => 'baru']);
    }
}