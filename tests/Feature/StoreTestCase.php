<?php

namespace Tests\Feature;

use App\Models\account;
use App\Models\clothes;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\product;
use App\Models\ProductVariant;
use App\Models\accessoris;
use App\Services\RajaOngkirService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Dasar semua test Feature toko: database SQLite in-memory yang di-reset tiap test
 * (lihat phpunit.xml) plus helper pembuat data.
 */
abstract class StoreTestCase extends TestCase
{
    use RefreshDatabase;

    /** Produk clothes dengan varian ukuran. $stocks = ['S' => 5, 'M' => 2] */
    protected function makeClothes(array $stocks = ['M' => 5], int $price = 150000, array $attrs = []): product
    {
        $product = product::create(array_merge([
            'category'     => 'clothes',
            'name'         => 'Kaos Test',
            'slug'         => 'kaos-test-' . uniqid(),
            'price'        => $price,
            'weight'       => 300,
            'description'  => 'Deskripsi',
            'is_active'    => true,
            'published_at' => now()->subDay(),
        ], $attrs));

        clothes::create(['product_id' => $product->id_product, 'color' => 'Black', 'material' => 'Cotton']);

        foreach ($stocks as $size => $stock) {
            ProductVariant::create(['product_id' => $product->id_product, 'label' => $size, 'stock' => $stock]);
        }

        $product->load('variants')->syncActiveStatus();

        return $product->fresh();
    }

    protected function makeAccessory(int $stock = 5, int $price = 25000, array $attrs = []): product
    {
        $product = product::create(array_merge([
            'category'     => 'accessories',
            'name'         => 'Gantungan Test',
            'slug'         => 'gantungan-test-' . uniqid(),
            'price'        => $price,
            'weight'       => 50,
            'stock'        => $stock,
            'is_active'    => $stock > 0,
            'published_at' => now()->subDay(),
        ], $attrs));

        accessoris::create(['product_id' => $product->id_product, 'type' => 'keychain']);

        return $product->fresh();
    }

    protected function makeStaff(string $role = 'owner', bool $active = true, string $username = 'crew1'): account
    {
        // firstOrCreate: aman dipanggil berkali-kali dalam satu test tanpa bentrok username unik
        return account::firstOrCreate(['username' => $username], [
            'name'      => 'Crew ' . $username,
            'password'  => bcrypt('rahasia123'),
            'role'      => $role,
            'is_active' => $active,
        ]);
    }

    /** Set session seolah staf sudah login lewat /crew-portal */
    protected function actingAsStaff(account $staff): static
    {
        return $this->withSession([
            'login' => true,
            'user'  => $staff->username,
            'role'  => $staff->role,
            'name'  => $staff->name,
        ]);
    }

    /** Buat pesanan pending dengan satu item */
    protected function makeOrder(product $product, ?ProductVariant $variant = null, int $qty = 2, array $attrs = []): Order
    {
        $order = Order::create(array_merge([
            'order_number'     => Order::generateOrderNumber(),
            'customer_name'    => 'Budi',
            'customer_phone'   => '081234567890',
            'customer_address' => 'Jl. Test 1',
            'shipping_courier' => 'JNE',
            'shipping_service' => 'REG',
            'shipping_cost'    => 10000,
            'subtotal'         => $product->price * $qty,
            'total'            => $product->price * $qty + 10000,
            'total_weight'     => $product->weight * $qty,
            'status'           => 'pending',
            'payment_status'   => 'unpaid',
        ], $attrs));

        OrderItem::create([
            'order_id'      => $order->id_order,
            'product_id'    => $product->id_product,
            'variant_id'    => $variant?->id_variant,
            'product_name'  => $product->name,
            'variant_label' => $variant?->label,
            'weight'        => $product->weight,
            'price'         => $product->price,
            'quantity'      => $qty,
            'subtotal'      => $product->price * $qty,
        ]);

        return $order;
    }

    /** Ganti RajaOngkir dengan data palsu supaya test tidak memanggil API sungguhan */
    protected function fakeRajaOngkir(): void
    {
        $fake = new class extends RajaOngkirService {
            public function __construct() {}

            public function searchDestination(string $keyword): array
            {
                return [['id' => 1, 'label' => 'Bandung Wetan, Bandung']];
            }

            public function calculateCost(int $destinationId, int $weight, array $couriers = ['jne']): array
            {
                return [
                    ['name' => 'JNE', 'service' => 'REG', 'description' => 'Reguler', 'cost' => 12000, 'etd' => '2-3 hari'],
                ];
            }
        };

        $this->app->instance(RajaOngkirService::class, $fake);
    }
}