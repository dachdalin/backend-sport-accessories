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
        Schema::create('shipping_methods', function (Blueprint $table) {
            $table->id();
            $table->string('title', 100);
            $table->decimal('cost', 8, 2)->default(0);
            $table->string('duration', 20)->nullable();
            $table->boolean('status')->default(true)->index();
            $table->unsignedBigInteger('creator_id');
            $table->string('creator_type', 191)->default('admin');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_methods');
    }
};
