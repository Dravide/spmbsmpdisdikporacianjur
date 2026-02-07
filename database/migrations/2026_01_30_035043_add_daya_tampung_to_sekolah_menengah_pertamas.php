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
        Schema::table('sekolah_menengah_pertamas', function (Blueprint $table) {
            $table->integer('daya_tampung')->default(0)->after('status_sekolah');
            $table->integer('jumlah_rombel')->default(0)->after('daya_tampung');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sekolah_menengah_pertamas', function (Blueprint $table) {
            $table->dropColumn(['daya_tampung', 'jumlah_rombel']);
        });
    }
};
