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
        Schema::table('messes', function (Blueprint $table) {
            // Add manager_id as a foreign key to ensure each mess has one manager
            $table->foreignId('manager_id')->nullable()->constrained('users')->cascadeOnDelete()->after('creator_id');
            
            // Add unique constraint so each user can only be manager of one mess
            $table->unique('manager_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messes', function (Blueprint $table) {
            $table->dropUnique(['manager_id']);
            $table->dropForeign(['manager_id']);
            $table->dropColumn('manager_id');
        });
    }
};
