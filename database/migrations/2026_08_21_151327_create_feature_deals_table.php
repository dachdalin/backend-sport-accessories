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
        Schema::create('feature_deals', function (Blueprint $table) {
            $table->id();
            $table->string('photo', 50)->default('def.png');
            $table->string('photo_storage_type', 10)->default('public');
            $table->string('url')->nullable();
            $table->boolean('status')->default(true)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feature_deals');
    }
};
