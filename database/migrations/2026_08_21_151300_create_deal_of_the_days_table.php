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
        Schema::create('deal_of_the_days', function (Blueprint $table) {
            $table->id();
            $table->string('title', 150);
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->decimal('discount', 8, 2)->default(0.00);
            $table->string('discount_type', 12)->default('amount');
            $table->boolean('status')->default(false)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deal_of_the_days');
    }
};
