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
        Schema::create('flash_deals', function (Blueprint $table) {
            $table->id();
            $table->string('title', 150);
            $table->string('slug', 150)->unique();
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('status')->default(true)->index();
            $table->boolean('featured')->default(false)->index();
            $table->string('background_color', 20)->nullable();
            $table->string('text_color', 20)->nullable();
            $table->string('banner', 50)->default('def.png');
            $table->string('banner_storage_type', 10)->default('public');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flash_deals');
    }
};
