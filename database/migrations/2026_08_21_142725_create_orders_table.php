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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 30)->unique();
            $table->string('customer_name', 100);
            $table->string('customer_email', 191)->nullable();
            $table->string('customer_phone', 30)->nullable();
            $table->text('shipping_address');
            $table->string('order_status', 20)->default('pending')->index();
            $table->string('payment_status', 20)->default('unpaid')->index();
            $table->string('payment_method', 50)->nullable();
            $table->decimal('discount_amount', 10, 2)->default(0.00);
            $table->string('discount_type', 20)->nullable();
            $table->decimal('shipping_cost', 10, 2)->default(0.00);
            $table->decimal('order_amount', 12, 2)->default(0.00);
            $table->text('order_note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
