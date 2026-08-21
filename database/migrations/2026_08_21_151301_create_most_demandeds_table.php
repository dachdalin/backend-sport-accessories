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
        Schema::create('most_demandeds', function (Blueprint $table) {
            $table->id();
            $table->string('banner', 50)->default('def.png');
            $table->string('banner_storage_type', 10)->default('public');
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->boolean('status')->default(false)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('most_demandeds');
    }
};
