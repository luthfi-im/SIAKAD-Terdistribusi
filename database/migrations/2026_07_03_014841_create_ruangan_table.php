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
        Schema::create('ruangan', function (Blueprint $table) {
            $table->string('id_ruangan', 20)->primary();
            $table->smallInteger('id_regional');
            $table->string('nama_ruangan', 100);
            $table->smallInteger('kapasitas');
            $table->timestamps();

            $table->foreign('id_regional')->references('id_regional')->on('regional');
        });
        DB::statement('ALTER TABLE ruangan ADD CONSTRAINT chk_kapasitas CHECK (kapasitas > 0)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ruangan');
    }
};