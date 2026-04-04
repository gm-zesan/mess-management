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
        Schema::table('mess_user', function (Blueprint $table) {
            // Add unique constraint on user_id to ensure one user can only join one mess
            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mess_user', function (Blueprint $table) {
            $table->dropUnique(['user_id']);
        });
    }
};
