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
        if (!Schema::hasColumn('expenses', 'mess_id')) {
            Schema::table('expenses', function (Blueprint $table) {
                $table->foreignId('mess_id')->constrained('messes')->cascadeOnDelete()->after('id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration is a backup - 155119 handles the column.
        // Do nothing on rollback to avoid conflicts.
    }
};
