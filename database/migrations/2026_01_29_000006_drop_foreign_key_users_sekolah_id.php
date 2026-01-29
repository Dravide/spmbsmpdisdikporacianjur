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
        Schema::table('users', function (Blueprint $table) {
            // Drop the foreign key constraint
            // The default naming convention is table_column_foreign
            $table->dropForeign(['sekolah_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('sekolah_id')
                ->references('sekolah_id')
                ->on('sekolah_dasar')
                ->onDelete('set null');
        });
    }
};
