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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignId('mess_id')->nullable()->constrained('messes')->cascadeOnDelete();
            $table->string('action'); // 'login', 'failed_login', 'logout', 'crud', 'permission_change', etc.
            $table->string('model_type')->nullable(); // The model affected (Meal, Expense, etc.)
            $table->bigInteger('model_id')->nullable(); // ID of the affected model
            $table->json('before_values')->nullable(); // Previous values for updates
            $table->json('after_values')->nullable(); // New values for create/update
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->text('description')->nullable(); // Human readable description
            $table->string('status')->default('success'); // success, failed, warning
            $table->timestamps();

            // Indexes for efficient querying
            $table->index(['user_id', 'created_at']);
            $table->index(['mess_id', 'created_at']);
            $table->index(['action', 'created_at']);
            $table->index('created_at');
            $table->index(['model_type', 'model_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
