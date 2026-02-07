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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->string('log_type')->default('activity'); // activity, login, system
            $table->string('action'); // created, updated, deleted, login, logout, etc.
            $table->string('description');
            $table->string('subject_type')->nullable(); // Model class name
            $table->string('subject_id')->nullable(); // Model ID
            $table->nullableMorphs('causer'); // Who performed the action
            $table->json('properties')->nullable(); // Additional data (old values, new values, etc.)
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();

            $table->index(['subject_type', 'subject_id']);
            $table->index('log_type');
            $table->index('action');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
