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
        Schema::create('mata_kuliah', function (Blueprint $table) {
            $table->string('kode_mk', 20)->primary();
            $table->string('kode_mk_prasyarat', 20)->nullable();
            $table->string('id_prodi', 20);
            $table->string('nama_mk', 200);
            $table->smallInteger('sks');
            $table->timestamps();
        });

        Schema::table('mata_kuliah', function (Blueprint $table) {
            $table->foreign('kode_mk_prasyarat')->references('kode_mk')->on('mata_kuliah')->nullOnDelete();
        });

        DB::statement('ALTER TABLE mata_kuliah ADD CONSTRAINT chk_sks CHECK (sks BETWEEN 1 AND 6)');
    }

    public function down(): void
    {
        Schema::dropIfExists('mata_kuliah');
    }
};