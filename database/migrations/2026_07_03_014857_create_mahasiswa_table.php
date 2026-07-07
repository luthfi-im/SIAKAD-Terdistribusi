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
        Schema::create('mahasiswa', function (Blueprint $table) {
            $table->string('nim', 20)->primary();
            $table->smallInteger('id_regional');
            $table->string('id_prodi', 20);
            $table->string('nama_mahasiswa', 200);
            $table->smallInteger('angkatan');
            $table->decimal('ips_terakhir', 3, 2)->default(0.00);
            $table->boolean('is_deleted')->default(false); // REQ-5.3.3
            $table->timestamps();

            $table->foreign('id_regional')->references('id_regional')->on('regional');
        });
        DB::statement('ALTER TABLE mahasiswa ADD CONSTRAINT chk_ips CHECK (ips_terakhir BETWEEN 0 AND 4)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mahasiswa');
    }
};
