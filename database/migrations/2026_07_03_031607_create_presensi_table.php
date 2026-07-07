<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('presensi', function (Blueprint $table) {
            $table->id('id_presensi');
            $table->smallInteger('id_regional');
            $table->foreignId('id_kelas')->constrained('kelas', 'id_kelas');
            $table->string('nim', 20);
            $table->date('tanggal_pertemuan');
            $table->smallInteger('pertemuan_ke');
            $table->string('status', 10)->default('Alpa'); // Hadir/Izin/Sakit/Alpa
            $table->timestamps();

            $table->foreign('id_regional')->references('id_regional')->on('regional');
            $table->foreign('nim')->references('nim')->on('mahasiswa');

            $table->unique(['id_kelas', 'nim', 'pertemuan_ke']); // 1 mahasiswa 1 entri per pertemuan
            $table->index(['id_kelas', 'tanggal_pertemuan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('presensi');
    }
};
