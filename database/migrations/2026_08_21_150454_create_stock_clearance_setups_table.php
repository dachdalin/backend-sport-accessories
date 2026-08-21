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
        Schema::create('stock_clearance_setups', function (Blueprint $table) {
            $table->id();
            $table->string('discount_type', 10)->default('percent');
            $table->decimal('discount_amount', 10, 2);
            $table->string('offer_active_time', 20)->default('always');
            $table->time('offer_active_range_start')->nullable();
            $table->time('offer_active_range_end')->nullable();
            $table->boolean('show_in_homepage')->default(false);
            $table->boolean('show_in_homepage_once')->default(false);
            $table->boolean('show_in_shop')->default(true);
            $table->boolean('is_active')->default(true)->index();
            $table->date('duration_start_date');
            $table->date('duration_end_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_clearance_setups');
    }
};
