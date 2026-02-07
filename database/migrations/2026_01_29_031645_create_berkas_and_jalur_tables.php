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
        // Table: berkas
        Schema::create('berkas', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->text('deskripsi')->nullable();
            $table->boolean('is_required')->default(true);
            $table->timestamps();
        });

        // Table: jalur_pendaftarans
        Schema::create('jalur_pendaftarans', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->text('deskripsi')->nullable();
            $table->boolean('aktif')->default(true);
            $table->integer('kuota')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->timestamps();
        });

        // Pivot Table: berkas_jalur_pendaftaran
        Schema::create('berkas_jalur_pendaftaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jalur_pendaftaran_id')->constrained('jalur_pendaftarans')->onDelete('cascade');
            $table->foreignId('berkas_id')->constrained('berkas')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('berkas_jalur_pendaftaran');
        Schema::dropIfExists('jalur_pendaftarans');
        Schema::dropIfExists('berkas');
    }
};
