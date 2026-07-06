<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * DEPRECATED: This migration was removed because it added a problematic unique constraint.
     * Adding unique('user_id') prevents users from joining multiple messes.
     * Only the composite unique(['mess_id', 'user_id']) constraint should exist.
     * See 2026_04_04_155101_create_mess_user_table.php for the correct constraints.
     */
    public function up(): void
    {
        // Do nothing - the unique constraint on user_id was breaking multi-mess feature
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op since up() does nothing
    }
};
