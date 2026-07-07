<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('kelas', function (Blueprint $table) {
            $table->id('id_kelas');
            $table->smallInteger('id_regional');
            $table->string('kode_mk', 20);
            $table->string('nip_dosen', 20);
            $table->string('id_ruangan', 20);
            $table->smallInteger('semester');
            $table->smallInteger('tahun_akademik');
            $table->smallInteger('kuota');
            $table->smallInteger('sisa_kuota');
            $table->timestamps();

            $table->foreign('id_regional')->references('id_regional')->on('regional');
            $table->foreign('kode_mk')->references('kode_mk')->on('mata_kuliah');
            $table->foreign('nip_dosen')->references('nip')->on('dosen');
            $table->foreign('id_ruangan')->references('id_ruangan')->on('ruangan');
        });
        DB::statement('ALTER TABLE kelas ADD CONSTRAINT chk_kuota CHECK (kuota > 0)');
        DB::statement('ALTER TABLE kelas ADD CONSTRAINT chk_sisa_kuota CHECK (sisa_kuota >= 0)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kelas');
    }
};
