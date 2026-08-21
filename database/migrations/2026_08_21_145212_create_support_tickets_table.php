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
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->string('subject', 150);
            $table->string('type', 50)->nullable();
            $table->string('priority', 10)->default('low');
            $table->text('description');
            $table->string('attachment')->nullable();
            $table->string('attachment_storage_type', 10)->default('public');
            $table->text('reply')->nullable();
            $table->string('status', 15)->default('open')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_tickets');
    }
};
