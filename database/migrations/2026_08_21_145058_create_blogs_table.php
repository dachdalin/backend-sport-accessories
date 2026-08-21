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
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blog_category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title', 150);
            $table->string('slug', 150)->unique();
            $table->string('writer', 100)->nullable();
            $table->text('description');
            $table->string('image')->default('def.png');
            $table->string('image_storage_type', 15)->default('public');
            $table->string('image_alt_text')->nullable();
            $table->boolean('is_published')->default(false)->index();
            $table->dateTime('published_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blogs');
    }
};
