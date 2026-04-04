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
        // Add mess_id to meals table
        Schema::table('meals', function (Blueprint $table) {
            $table->foreignId('mess_id')->nullable()->constrained('messes')->cascadeOnDelete();
            $table->index('mess_id');
        });

        // Add mess_id to expenses table
        Schema::table('expenses', function (Blueprint $table) {
            $table->foreignId('mess_id')->nullable()->constrained('messes')->cascadeOnDelete();
            $table->index('mess_id');
        });

        // Add mess_id to deposits table
        Schema::table('deposits', function (Blueprint $table) {
            $table->foreignId('mess_id')->nullable()->constrained('messes')->cascadeOnDelete();
            $table->index('mess_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('meals', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\Mess::class);
            $table->dropIndex('meals_mess_id_index');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\Mess::class);
            $table->dropIndex('expenses_mess_id_index');
        });

        Schema::table('deposits', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\Mess::class);
            $table->dropIndex('deposits_mess_id_index');
        });
    }
};
