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
        Schema::create('nilai', function (Blueprint $table) {
            $table->id('id_nilai');
            $table->smallInteger('id_regional');
            $table->string('nim', 20);
            $table->foreignId('id_kelas')->constrained('kelas', 'id_kelas');
            $table->decimal('nilai_akhir', 5, 2)->nullable();
            $table->boolean('is_finalisasi')->default(false);
            $table->timestamps();

            $table->foreign('id_regional')->references('id_regional')->on('regional');
            $table->foreign('nim')->references('nim')->on('mahasiswa');

            $table->index(['nim', 'id_regional']); // idx_nilai_nim_regional
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nilai');
    }
};