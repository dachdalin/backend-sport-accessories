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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name', 80)->unique();
            $table->string('slug', 120)->unique();
            $table->string('code', 50)->nullable();
            $table->text('description')->nullable();
            $table->string('thumbnail')->default('def.png');
            $table->string('thumbnail_storage_type', 10)->default('public');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('purchase_price', 10, 2)->nullable();
            $table->integer('current_stock')->default(0);
            $table->integer('minimum_order_qty')->default(1);
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('brand_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('tax', 10, 2)->default(0.00);
            $table->string('tax_type', 20)->nullable();
            $table->decimal('discount', 10, 2)->default(0.00);
            $table->string('discount_type', 20)->nullable();
            $table->boolean('free_shipping')->default(false);
            $table->boolean('refundable')->default(true);
            $table->boolean('featured')->default(false);
            $table->string('meta_title', 191)->nullable();
            $table->string('meta_description', 191)->nullable();
            $table->boolean('status')->default(true)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
