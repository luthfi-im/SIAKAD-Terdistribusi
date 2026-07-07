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
        Schema::create('dosen', function (Blueprint $table) {
            $table->string('nip', 20)->primary();
            $table->smallInteger('id_regional');
            $table->string('id_prodi', 20);
            $table->string('nama_dosen', 200);
            $table->timestamps();

            $table->foreign('id_regional')->references('id_regional')->on('regional');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dosen');
    }
};