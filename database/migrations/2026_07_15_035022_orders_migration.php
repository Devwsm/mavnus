<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //
        Schema::create('orders', function (Blueprint $table) {
            $table->id('id_order');
            $table->string('order_number')->unique();

            // Data pembeli (guest checkout, belum ada sistem akun customer)
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->text('customer_address');

            // Placeholder buat RajaOngkir nanti
            $table->string('shipping_courier')->nullable();
            $table->string('shipping_service')->nullable();
            $table->unsignedBigInteger('shipping_cost')->default(0);
            $table->unsignedInteger('total_weight')->default(0);

            // Ringkasan biaya
            $table->unsignedBigInteger('subtotal');
            $table->unsignedBigInteger('total');

            // Status pesanan & pembayaran, terpisah karena beda siklus
            $table->enum('status', ['pending', 'processing', 'shipped', 'completed', 'cancelled'])->default('pending');
            $table->enum('payment_status', ['unpaid', 'paid', 'failed', 'expired'])->default('unpaid');

            // Placeholder buat Midtrans nanti
            $table->string('payment_method')->nullable();
            $table->string('midtrans_order_id')->nullable();
            $table->string('midtrans_transaction_id')->nullable();
            $table->timestamp('paid_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('orders');
    }
};