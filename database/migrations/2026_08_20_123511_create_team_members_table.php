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
        Schema::create('team_members', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('role', 100);
            $table->text('bio')->nullable();
            $table->string('photo', 50)->default('def.png');
            $table->string('photo_storage_type', 10)->default('public');
            $table->string('photo_alt_text')->nullable();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->boolean('status')->default(true)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('team_members');
    }
};
