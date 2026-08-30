<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * The original `photo` column was capped at 50 characters, but
     * `UploadedFile::store('team-members', 'public')` always generates a
     * path like `team-members/{40-char-random}.{ext}` (57+ characters),
     * so every photo upload failed with a truncation error at the DB layer.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE team_members MODIFY photo VARCHAR(255) NOT NULL DEFAULT \'def.png\'');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE team_members MODIFY photo VARCHAR(50) NOT NULL DEFAULT \'def.png\'');
        }
    }
};
