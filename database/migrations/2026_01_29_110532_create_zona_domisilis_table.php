<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('zona_domisilis', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sekolah_menengah_pertama_id');
            $table->string('kecamatan');
            $table->string('desa')->nullable();
            $table->string('rw')->nullable();
            $table->string('rt')->nullable();
            $table->timestamps();

            $table->index('sekolah_menengah_pertama_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('zona_domisilis');
    }
};
