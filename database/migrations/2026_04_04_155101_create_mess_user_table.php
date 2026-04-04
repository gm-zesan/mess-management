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
        Schema::create('mess_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mess_id')->constrained('messes')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('invited_by_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            
            // Ensure unique combination of mess and user
            $table->unique(['mess_id', 'user_id']);
            
            // Indexes
            $table->index('mess_id');
            $table->index('user_id');
            $table->index(['mess_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mess_user');
    }
};
