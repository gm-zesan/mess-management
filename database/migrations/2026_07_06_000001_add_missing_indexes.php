<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Add missing indexes on foreign keys for improved query performance.
     * This addresses N+1 query issues by optimizing database lookups.
     */
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            // Index on user_id for filtering expenses by user
            $table->index('user_id');
        });

        Schema::table('deposits', function (Blueprint $table) {
            // Index on user_id for filtering deposits by user
            $table->index('user_id');
        });

        Schema::table('meals', function (Blueprint $table) {
            // Index on user_id for filtering meals by user (already has unique, but index still helps)
            // The unique constraint already provides indexing, but explicit index for clarity
        });

        Schema::table('login_attempts', function (Blueprint $table) {
            // Index on user_id for quick user login history lookup
            if (Schema::hasColumn('login_attempts', 'user_id')) {
                $table->index('user_id');
            }
        });

        Schema::table('audit_logs', function (Blueprint $table) {
            // Index on user_id for quick audit trail lookup
            if (Schema::hasColumn('audit_logs', 'user_id')) {
                $table->index('user_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
        });

        Schema::table('deposits', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
        });

        Schema::table('login_attempts', function (Blueprint $table) {
            if (Schema::hasColumn('login_attempts', 'user_id')) {
                $table->dropIndex(['user_id']);
            }
        });

        Schema::table('audit_logs', function (Blueprint $table) {
            if (Schema::hasColumn('audit_logs', 'user_id')) {
                $table->dropIndex(['user_id']);
            }
        });
    }
};
