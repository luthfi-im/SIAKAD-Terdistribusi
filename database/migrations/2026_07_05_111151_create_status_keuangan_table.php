<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('status_keuangan', function (Blueprint $table) {
            $table->string('nim', 20)->primary();
            $table->string('status', 20)->default('LUNAS'); // LUNAS / BELUM LUNAS
            $table->timestampTz('updated_at')->useCurrent();

            $table->foreign('nim')->references('nim')->on('mahasiswa');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('status_keuangan');
    }
};
