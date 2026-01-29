<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sekolah_menengah_pertamas', function (Blueprint $table) {
            $table->string('sekolah_id')->primary();
            $table->string('npsn')->unique();
            $table->string('nama');
            $table->string('kode_wilayah')->nullable();
            $table->string('bentuk_pendidikan_id')->nullable();
            $table->string('status_sekolah')->nullable();
            $table->text('alamat_jalan')->nullable();
            $table->string('desa_kelurahan')->nullable();
            $table->string('rt')->nullable();
            $table->string('rw')->nullable();
            $table->decimal('lintang', 10, 7)->nullable();
            $table->decimal('bujur', 10, 7)->nullable();
            $table->timestamps();

            $table->index('nama');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sekolah_menengah_pertamas');
    }
};
