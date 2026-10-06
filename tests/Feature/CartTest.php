<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use App\Models\CartItem;
use App\Models\User;

class CartTest extends StoreTestCase
{
    public function test_add_clothes_to_cart(): void
    {
        $p = $this->makeClothes(['M' => 5]);
        $variant = $p->variants->first();

        $this->postJson('/cart/add', [
            'product_id' => $p->id_product,
            'variant_id' => $variant->id_variant,
            'quantity'   => 2,
        ])->assertOk()->assertJsonPath('count', 2);
    }

    public function test_quantity_is_capped_to_stock(): void
    {
        $p = $this->makeClothes(['M' => 3]);

        $this->postJson('/cart/add', [
            'product_id' => $p->id_product,
            'variant_id' => $p->variants->first()->id_variant,
            'quantity'   => 50,
        ])->assertOk()->assertJsonPath('count', 3);
    }

    public function test_quantity_above_99_is_rejected(): void
    {
        $p = $this->makeAccessory(500);

        $this->postJson('/cart/add', ['product_id' => $p->id_product, 'quantity' => 100])
            ->assertStatus(422)->assertJsonValidationErrors('quantity');
    }

    public function test_clothes_requires_a_size(): void
    {
        $p = $this->makeClothes(['M' => 3]);

        $this->postJson('/cart/add', ['product_id' => $p->id_product, 'quantity' => 1])->assertStatus(422);
    }

    public function test_variant_from_another_product_is_rejected(): void
    {
        $a = $this->makeClothes(['M' => 3]);
        $b = $this->makeClothes(['M' => 3], 99000);

        $this->postJson('/cart/add', [
            'product_id' => $a->id_product,
            'variant_id' => $b->variants->first()->id_variant,
            'quantity'   => 1,
        ])->assertStatus(422);
    }

    public function test_scheduled_product_cannot_be_added(): void
    {
        $p = $this->makeAccessory(5, 25000, ['published_at' => now()->addDays(2)]);

        $this->postJson('/cart/add', ['product_id' => $p->id_product, 'quantity' => 1])->assertStatus(422);
    }

    public function test_cart_survives_login_session_regeneration(): void
    {
        $p = $this->makeAccessory(5);
        $this->postJson('/cart/add', ['product_id' => $p->id_product, 'quantity' => 2])->assertOk();

        User::factory()->create(['email' => 'a@example.com', 'password' => bcrypt('password123')]);

        $this->post('/login', ['email' => 'a@example.com', 'password' => 'password123'])->assertRedirect();

        // Dulu keranjang diikat ke ID session yang berganti saat login, jadi kosong di sini
        $this->getJson('/cart')->assertOk()->assertJsonPath('count', 2);
    }

    public function test_cannot_change_or_delete_someone_elses_cart_item(): void
    {
        $p = $this->makeAccessory(5);
        $foreign = CartItem::create(['session_id' => 'orang-lain', 'product_id' => $p->id_product, 'quantity' => 1]);

        $this->patchJson("/cart/{$foreign->id_cart_item}", ['quantity' => 3])->assertNotFound();
        $this->deleteJson("/cart/{$foreign->id_cart_item}")->assertNotFound();

        $this->assertDatabaseHas('cart_items', ['id_cart_item' => $foreign->id_cart_item, 'quantity' => 1]);
    }

    public function test_updating_item_that_sold_out_removes_it_instead_of_saving_zero(): void
    {
        $p = $this->makeAccessory(5);
        $this->postJson('/cart/add', ['product_id' => $p->id_product, 'quantity' => 1])->assertOk();
        $item = CartItem::first();

        $p->update(['stock' => 0]);

        $this->patchJson("/cart/{$item->id_cart_item}", ['quantity' => 2])->assertOk()->assertJsonPath('count', 0);
        $this->assertDatabaseMissing('cart_items', ['id_cart_item' => $item->id_cart_item]);
    }

    public function test_orphaned_cart_row_does_not_break_the_cart_endpoint(): void
    {
        $p = $this->makeAccessory(5);
        $this->postJson('/cart/add', ['product_id' => $p->id_product, 'quantity' => 1])->assertOk();

        // baris yatim: produknya terhapus tanpa cascade (mis. data lama)
        DB::statement('PRAGMA foreign_keys = OFF');
        DB::table('products')->where('id_product', $p->id_product)->delete();
        DB::statement('PRAGMA foreign_keys = ON');

        $this->getJson('/cart')->assertOk()->assertJsonPath('count', 0);
    }

    public function test_badge_count_is_rendered_in_html_without_extra_request(): void
    {
        $p = $this->makeAccessory(5);
        $this->postJson('/cart/add', ['product_id' => $p->id_product, 'quantity' => 3])->assertOk();

        $html = $this->get('/')->assertOk()->getContent();

        $this->assertMatchesRegularExpression('/cart-badge flex[^>]*>3<\/span>/', $html);
    }
}