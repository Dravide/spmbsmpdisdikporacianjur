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
        Schema::table('pendaftaran_berkases', function (Blueprint $table) {
            $table->enum('status_berkas', ['pending', 'approved', 'revision', 'rejected'])->default('pending')->after('nama_file_asli');
            $table->text('catatan_verifikasi')->nullable()->after('status_berkas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pendaftaran_berkases', function (Blueprint $table) {
            $table->dropColumn(['status_berkas', 'catatan_verifikasi']);
        });
    }
};
