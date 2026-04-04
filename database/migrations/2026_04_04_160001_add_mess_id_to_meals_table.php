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
        if (!Schema::hasColumn('meals', 'mess_id')) {
            Schema::table('meals', function (Blueprint $table) {
                $table->foreignId('mess_id')->constrained('messes')->cascadeOnDelete()->after('id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('meals', function (Blueprint $table) {
            $table->dropForeignIdFor('messes');
        });
    }
};
