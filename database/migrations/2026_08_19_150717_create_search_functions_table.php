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
        Schema::create('search_functions', function (Blueprint $table) {
            $table->id();
            $table->string('key', 150);
            $table->string('url', 250);
            $table->string('visible_for', 20)->default('admin');
            $table->timestamps();

            $table->unique(['key', 'visible_for']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('search_functions');
    }
};
