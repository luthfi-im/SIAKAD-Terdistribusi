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
        Schema::create('regional', function (Blueprint $table) {
            $table->smallInteger('id_regional')->primary();
            $table->string('nama_regional', 100);
            $table->string('lokasi', 200);
            $table->string('fakultas', 150);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('regional');
    }
};