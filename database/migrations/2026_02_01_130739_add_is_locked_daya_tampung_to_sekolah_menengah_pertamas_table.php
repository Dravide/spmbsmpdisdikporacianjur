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
        Schema::table('sekolah_menengah_pertamas', function (Blueprint $table) {
            $table->boolean('is_locked_daya_tampung')->default(false)->after('mode_spmb');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sekolah_menengah_pertamas', function (Blueprint $table) {
            $table->dropColumn('is_locked_daya_tampung');
        });
    }
};
