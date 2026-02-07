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
        Schema::table('daftar_ulangs', function (Blueprint $table) {
            $table->json('checklist_dokumen')->nullable()->after('status');
        });

        Schema::table('sekolah_menengah_pertamas', function (Blueprint $table) {
            $table->text('syarat_daftar_ulang')->nullable()->after('nama');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daftar_ulangs', function (Blueprint $table) {
            $table->dropColumn('checklist_dokumen');
        });

        Schema::table('sekolah_menengah_pertamas', function (Blueprint $table) {
            $table->dropColumn('syarat_daftar_ulang');
        });
    }
};
