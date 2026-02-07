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
        Schema::create('pendaftarans', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Foreign Keys
            $table->foreignId('peserta_didik_id')->constrained('peserta_didiks')->cascadeOnDelete();
            $table->foreignUuid('sekolah_menengah_pertama_id')->nullable()->constrained('sekolah_menengah_pertamas', 'sekolah_id')->nullOnDelete();
            $table->foreignId('jalur_pendaftaran_id')->nullable()->constrained('jalur_pendaftarans')->nullOnDelete();

            // Registration Details
            $table->date('tanggal_daftar')->default(now());
            $table->double('koordinat_lintang')->nullable();
            $table->double('koordinat_bujur')->nullable();
            $table->float('jarak_meter')->nullable(); // Distance to school in meters
            $table->string('status')->default('draft'); // draft, submitted, verified, accepted, rejected
            $table->string('nomor_pendaftaran')->nullable()->unique();

            $table->text('catatan')->nullable(); // For rejection reasons etc.

            $table->timestamps();
        });

        Schema::create('pendaftaran_berkases', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('pendaftaran_id')->constrained('pendaftarans')->cascadeOnDelete();
            $table->foreignId('berkas_id')->constrained('berkas')->cascadeOnDelete();
            $table->string('file_path');
            $table->string('nama_file_asli')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pendaftaran_berkases');
        Schema::dropIfExists('pendaftarans');
    }
};
