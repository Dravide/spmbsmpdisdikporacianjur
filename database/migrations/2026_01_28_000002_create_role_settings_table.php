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
        Schema::create('role_settings', function (Blueprint $table) {
            $table->id();
            $table->string('role')->unique();
            $table->string('role_label');
            $table->integer('max_login_locations')->default(5);
            $table->integer('session_timeout_minutes')->default(120);
            $table->boolean('allow_multiple_sessions')->default(true);
            $table->timestamps();
        });

        // Insert default role settings
        DB::table('role_settings')->insert([
            [
                'role' => 'admin',
                'role_label' => 'Administrator',
                'max_login_locations' => 10,
                'session_timeout_minutes' => 480,
                'allow_multiple_sessions' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'role' => 'opsd',
                'role_label' => 'Operator SD',
                'max_login_locations' => 5,
                'session_timeout_minutes' => 240,
                'allow_multiple_sessions' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'role' => 'opsmp',
                'role_label' => 'Operator SMP',
                'max_login_locations' => 5,
                'session_timeout_minutes' => 240,
                'allow_multiple_sessions' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'role' => 'cmb',
                'role_label' => 'Calon Murid Baru',
                'max_login_locations' => 2,
                'session_timeout_minutes' => 120,
                'allow_multiple_sessions' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_settings');
    }
};
