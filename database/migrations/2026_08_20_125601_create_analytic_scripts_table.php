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
        Schema::create('analytic_scripts', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('type', 30)->default('custom');
            $table->string('script_id', 255)->nullable();
            $table->longText('script');
            $table->boolean('status')->default(false)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analytic_scripts');
    }
};
