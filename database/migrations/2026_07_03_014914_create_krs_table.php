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
        Schema::create('krs', function (Blueprint $table) {
            $table->id('id_krs');
            $table->smallInteger('id_regional');
            $table->string('nim', 20);
            $table->foreignId('id_kelas')->constrained('kelas', 'id_kelas');
            $table->string('status', 20)->default('Antre'); // Antre/Sukses/Gagal
            $table->timestamps();

            $table->foreign('id_regional')->references('id_regional')->on('regional');
            $table->foreign('nim')->references('nim')->on('mahasiswa');

            $table->index(['nim', 'id_regional']); // idx_krs_nim_regional
            $table->index(['id_kelas', 'status']);  // idx_krs_kelas_status
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('krs');
    }
};