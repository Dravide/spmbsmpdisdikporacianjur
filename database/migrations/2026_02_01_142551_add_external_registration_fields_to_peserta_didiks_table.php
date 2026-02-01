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
        Schema::table('peserta_didiks', function (Blueprint $table) {
            $table->boolean('is_external')->default(false)->after('flag_pip')->comment('Penanda pendaftaran akun mandiri/luar wilayah');
            $table->enum('verification_status', ['verified', 'pending', 'rejected'])->default('verified')->after('is_external');
            $table->text('verification_note')->nullable()->after('verification_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('peserta_didiks', function (Blueprint $table) {
            $table->dropColumn(['is_external', 'verification_status', 'verification_note']);
        });
    }
};
