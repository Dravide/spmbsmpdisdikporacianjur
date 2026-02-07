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
        Schema::table('pendaftarans', function (Blueprint $table) {
            $table->foreignUuid('sekolah_menengah_pertama_id_2')->nullable()->after('sekolah_menengah_pertama_id')->constrained('sekolah_menengah_pertamas', 'sekolah_id')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pendaftarans', function (Blueprint $table) {
            $table->dropForeign(['sekolah_menengah_pertama_id_2']);
            $table->dropColumn('sekolah_menengah_pertama_id_2');
        });
    }
};
