<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pendaftaran_berkases', function (Blueprint $table) {
            $table->json('form_data')->nullable()->after('file_path');
        });
    }

    public function down(): void
    {
        Schema::table('pendaftaran_berkases', function (Blueprint $table) {
            $table->dropColumn('form_data');
        });
    }
};
