<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Storage;
use App\Models\CartItem;
use App\Models\product;

class ProductAdminTest extends StoreTestCase
{
    private function clothesPayload(array $override = []): array
    {
        return array_merge([
            'name'         => 'Kaos Baru',
            'price'        => 175000,
            'weight'       => 300,
            'description'  => 'Deskripsi',
            'release_mode' => 'now',
            'color'        => 'Black',
            'material'     => 'Cotton',
            'variants'     => [['size' => 'S', 'stock' => 4], ['size' => 'M', 'stock' => 6]],
        ], $override);
    }

    // UploadedFile::fake()->image() & konversi webp butuh ekstensi GD (atau Imagick)
    private function requireGd(): void
    {
        if (! extension_loaded('gd')) {
            $this->markTestSkipped('Ekstensi PHP GD tidak terpasang di mesin ini.');
        }
    }

    private function asAdmin()
    {
        return $this->actingAsStaff($this->makeStaff('admin_produk'));
    }

    public function test_create_clothes(): void
    {
        $this->asAdmin()->post('/dashboard/produk/store', $this->clothesPayload(['category' => 'clothes']))
            ->assertRedirect(route('dashboard.produk'));

        $p = product::first();
        $this->assertSame('Kaos Baru', $p->name);
        $this->assertCount(2, $p->variants);
        $this->assertTrue($p->is_active);
    }

    public function test_duplicate_sizes_are_rejected(): void
    {
        $this->asAdmin()->post('/dashboard/produk/store', $this->clothesPayload([
            'category' => 'clothes',
            'variants' => [['size' => 'M', 'stock' => 1], ['size' => 'M', 'stock' => 2]],
        ]))->assertSessionHasErrors('variants.1.size');

        $this->assertDatabaseCount('products', 0);
    }

    public function test_color_cannot_contain_css_injection(): void
    {
        $this->asAdmin()->post('/dashboard/produk/store', $this->clothesPayload([
            'category' => 'clothes',
            'color'    => 'red"; background:url(//evil.example/x)',
        ]))->assertSessionHasErrors('color');
    }

    public function test_color_accepts_normal_values(): void
    {
        foreach (['Hitam', 'Off White', '#1a1a1a', 'rgb(10, 20, 30)'] as $color) {
            $this->asAdmin()->post('/dashboard/produk/store', $this->clothesPayload(['category' => 'clothes', 'color' => $color]))
                ->assertSessionHasNoErrors();
        }
    }

    public function test_too_many_images_rejected(): void
    {
        $this->requireGd();
        $files = array_map(fn($i) => \Illuminate\Http\UploadedFile::fake()->image("f{$i}.jpg"), range(1, 9));

        $this->asAdmin()->post('/dashboard/produk/store', $this->clothesPayload(['category' => 'clothes', 'images' => $files]))
            ->assertSessionHasErrors('images');
    }

    public function test_uploaded_photo_is_resized_to_webp_max_1600px(): void
    {
        $this->requireGd();
        Storage::fake('public');
        $file = \Illuminate\Http\UploadedFile::fake()->image('besar.jpg', 3200, 2400);

        $this->asAdmin()->post('/dashboard/produk/store', $this->clothesPayload(['category' => 'clothes', 'images' => [$file]]))
            ->assertSessionHasNoErrors();

        $path = product::first()->images->first()->image_path;
        $this->assertStringEndsWith('.webp', $path);
        [$w, $h] = getimagesizefromstring(Storage::disk('public')->get($path));
        $this->assertLessThanOrEqual(1600, max($w, $h));
    }

    public function test_editing_keeps_variant_ids_and_cart_items(): void
    {
        $p = $this->makeClothes(['S' => 4, 'M' => 6]);
        $m = $p->variants->firstWhere('label', 'M');
        $cart = CartItem::create(['session_id' => 'x', 'product_id' => $p->id_product, 'variant_id' => $m->id_variant, 'quantity' => 1]);

        $this->asAdmin()->put("/dashboard/produk/{$p->id_product}", $this->clothesPayload([
            'price'    => 200000,
            'variants' => [['size' => 'S', 'stock' => 4], ['size' => 'M', 'stock' => 9]],
        ]))->assertRedirect(route('dashboard.produk'));

        // Dulu semua varian dihapus lalu dibuat ulang: ID berganti & keranjang pembeli ikut terhapus
        $this->assertSame(9, $m->fresh()->stock);
        $this->assertDatabaseHas('cart_items', ['id_cart_item' => $cart->id_cart_item, 'variant_id' => $m->id_variant]);
        $this->assertSame(200000, (int) $p->fresh()->price);
    }

    public function test_removing_a_size_removes_only_that_variant_and_its_cart_rows(): void
    {
        $p = $this->makeClothes(['S' => 4, 'M' => 6]);
        $s = $p->variants->firstWhere('label', 'S');
        $m = $p->variants->firstWhere('label', 'M');
        CartItem::create(['session_id' => 'x', 'product_id' => $p->id_product, 'variant_id' => $s->id_variant, 'quantity' => 1]);

        $this->asAdmin()->put("/dashboard/produk/{$p->id_product}", $this->clothesPayload(['variants' => [['size' => 'M', 'stock' => 6]]]));

        $this->assertDatabaseMissing('product_variants', ['id_variant' => $s->id_variant]);
        $this->assertDatabaseHas('product_variants', ['id_variant' => $m->id_variant]);
        $this->assertDatabaseCount('cart_items', 0);
    }

    public function test_editing_does_not_reset_original_release_date(): void
    {
        $released = now()->subDays(10)->startOfSecond();
        $p = $this->makeClothes(['M' => 5], 100000, ['published_at' => $released]);

        $this->asAdmin()->put("/dashboard/produk/{$p->id_product}", $this->clothesPayload(['variants' => [['size' => 'M', 'stock' => 5]]]));

        $this->assertTrue($p->fresh()->published_at->equalTo($released));
    }

    public function test_sizes_are_listed_in_s_m_l_xl_order(): void
    {
        $p = $this->makeClothes(['XL' => 1, 'S' => 1, 'L' => 1, 'M' => 1]);

        $this->assertSame(['S', 'M', 'L', 'XL'], $p->variants()->pluck('label')->all());
    }

    public function test_delete_product_clears_it_from_carts(): void
    {
        $p = $this->makeAccessory(5);
        CartItem::create(['session_id' => 'x', 'product_id' => $p->id_product, 'quantity' => 1]);

        $this->asAdmin()->delete("/dashboard/produk/{$p->id_product}")->assertRedirect();

        $this->assertDatabaseCount('products', 0);
        $this->assertDatabaseCount('cart_items', 0);
    }
}