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
        Schema::create('pengumumans', function (Blueprint $table) {
            $table->id();
            $table->string('sekolah_menengah_pertama_id');
            $table->foreign('sekolah_menengah_pertama_id')->references('sekolah_id')->on('sekolah_menengah_pertamas')->cascadeOnDelete();

            $table->foreignId('jalur_pendaftaran_id')->constrained()->cascadeOnDelete();

            $table->foreignUuid('pendaftaran_id')->constrained()->cascadeOnDelete();

            $table->foreignId('peserta_didik_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['lulus', 'tidak_lulus'])->default('tidak_lulus');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengumuman');
    }
};
