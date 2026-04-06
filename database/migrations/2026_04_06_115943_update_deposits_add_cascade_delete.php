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
        Schema::table('deposits', function (Blueprint $table) {
            // Drop the existing foreign key
            $table->dropForeign(['month_id']);
            // Add new foreign key with cascade delete
            $table->foreign('month_id')->references('id')->on('months')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deposits', function (Blueprint $table) {
            // Drop the cascade foreign key
            $table->dropForeign(['month_id']);
            // Restore the original foreign key without cascade
            $table->foreign('month_id')->references('id')->on('months');
        });
    }
};
