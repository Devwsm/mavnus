<?php

namespace Tests\Feature;

use App\Models\Order;

class CheckoutTest extends StoreTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->fakeRajaOngkir();
    }

    private function payload(array $override = []): array
    {
        return array_merge([
            'customer_name'     => 'Budi',
            'customer_phone'    => '081234567890',
            'customer_address'  => 'Jl. Merdeka 1',
            'destination_id'    => 1,
            'destination_label' => 'Bandung Wetan, Bandung',
            'shipping_courier'  => 'JNE',
            'shipping_service'  => 'REG',
        ], $override);
    }

    public function test_checkout_creates_order_decrements_stock_and_clears_cart(): void
    {
        $p = $this->makeClothes(['M' => 5], 150000);
        $this->postJson('/cart/add', ['product_id' => $p->id_product, 'variant_id' => $p->variants->first()->id_variant, 'quantity' => 2]);

        $res = $this->post('/order', $this->payload());

        $order = Order::first();
        $res->assertRedirect(route('order.success', $order));
        // ongkir diambil dari server (12000), bukan dari input browser
        $this->assertSame(300000, (int) $order->subtotal);
        $this->assertSame(12000, (int) $order->shipping_cost);
        $this->assertSame(312000, (int) $order->total);
        $this->assertSame(3, $p->variants()->first()->stock);
        $this->assertDatabaseCount('cart_items', 0);
    }

    public function test_browser_supplied_shipping_cost_is_ignored(): void
    {
        $p = $this->makeAccessory(5, 10000);
        $this->postJson('/cart/add', ['product_id' => $p->id_product, 'quantity' => 1]);

        $this->post('/order', $this->payload(['shipping_cost' => 1]));

        $this->assertSame(12000, (int) Order::first()->shipping_cost);
    }

    public function test_cannot_buy_more_than_stock_even_if_stock_dropped_after_adding(): void
    {
        $p = $this->makeAccessory(5);
        $this->postJson('/cart/add', ['product_id' => $p->id_product, 'quantity' => 4]);
        $p->update(['stock' => 2]);

        $this->post('/order', $this->payload())->assertRedirect(route('order.checkout'));

        $this->assertDatabaseCount('orders', 0);
        $this->assertSame(2, (int) $p->fresh()->stock);
    }

    public function test_phone_must_be_digits_only(): void
    {
        $p = $this->makeAccessory(5);
        $this->postJson('/cart/add', ['product_id' => $p->id_product, 'quantity' => 1]);

        $this->post('/order', $this->payload(['customer_phone' => '+62 812-345']))->assertSessionHasErrors('customer_phone');
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_address_length_is_limited(): void
    {
        $p = $this->makeAccessory(5);
        $this->postJson('/cart/add', ['product_id' => $p->id_product, 'quantity' => 1]);

        $this->post('/order', $this->payload(['customer_address' => str_repeat('a', 501)]))
            ->assertSessionHasErrors('customer_address');
    }

    public function test_empty_cart_cannot_checkout(): void
    {
        $this->get('/order/checkout')->assertRedirect(route('home'));
        $this->post('/order', $this->payload())->assertRedirect(route('home'));
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_unknown_courier_service_is_rejected(): void
    {
        $p = $this->makeAccessory(5);
        $this->postJson('/cart/add', ['product_id' => $p->id_product, 'quantity' => 1]);

        $this->post('/order', $this->payload(['shipping_service' => 'GRATIS']))->assertSessionHas('error');
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_success_page_only_for_the_browser_that_made_the_order(): void
    {
        $p = $this->makeAccessory(5);
        $this->postJson('/cart/add', ['product_id' => $p->id_product, 'quantity' => 1]);
        $this->post('/order', $this->payload());
        $order = Order::first();

        $this->get(route('order.success', $order))->assertOk();

        // "browser lain" = session baru
        $this->flushSession();
        $this->get(route('order.success', $order))->assertNotFound();
    }

    public function test_shipping_search_ignores_array_keyword_instead_of_500(): void
    {
        $this->getJson('/shipping/search?keyword[]=x')->assertOk()->assertJson(['data' => []]);
    }
}