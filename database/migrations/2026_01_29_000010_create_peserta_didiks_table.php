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
        Schema::create('peserta_didiks', function (Blueprint $table) {
            $table->id();
            $table->uuid('peserta_didik_id')->unique(); // ID from Dapodik/Source
            $table->string('sekolah_id')->nullable()->index();
            $table->string('kode_wilayah')->nullable();
            $table->string('nama');
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('jenis_kelamin')->nullable();
            $table->string('nik')->nullable(); // NIK might not be unique if dirty data
            $table->string('no_kk')->nullable();
            $table->string('nisn')->nullable()->index();
            $table->text('alamat_jalan')->nullable();
            $table->string('desa_kelurahan')->nullable();
            $table->string('rt')->nullable();
            $table->string('rw')->nullable();
            $table->string('nama_dusun')->nullable();
            $table->string('nama_ibu_kandung')->nullable();
            $table->string('pekerjaan_ibu')->nullable();
            $table->string('penghasilan_ibu')->nullable();
            $table->string('nama_ayah')->nullable();
            $table->string('pekerjaan_ayah')->nullable();
            $table->string('penghasilan_ayah')->nullable();
            $table->string('nama_wali')->nullable();
            $table->string('pekerjaan_wali')->nullable();
            $table->string('penghasilan_wali')->nullable();
            $table->string('kebutuhan_khusus')->nullable();
            $table->string('no_KIP')->nullable();
            $table->string('no_pkh')->nullable();
            $table->double('lintang')->nullable();
            $table->double('bujur')->nullable();
            $table->string('flag_pip')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peserta_didiks');
    }
};
