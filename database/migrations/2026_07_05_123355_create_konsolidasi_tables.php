<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('krs_konsolidasi', function (Blueprint $table) {
            $table->id();
            $table->smallInteger('id_regional');
            $table->unsignedBigInteger('source_id_krs');
            $table->string('nim', 20);
            $table->unsignedBigInteger('id_kelas');
            $table->string('status', 20);
            $table->timestampTz('source_created_at');
            $table->timestampTz('synced_at')->useCurrent();

            $table->unique(['id_regional', 'source_id_krs']);
        });

        Schema::create('nilai_konsolidasi', function (Blueprint $table) {
            $table->id();
            $table->smallInteger('id_regional');
            $table->unsignedBigInteger('source_id_nilai');
            $table->string('nim', 20);
            $table->unsignedBigInteger('id_kelas');
            $table->decimal('nilai_akhir', 5, 2)->nullable();
            $table->boolean('is_finalisasi')->default(false);
            $table->timestampTz('synced_at')->useCurrent();

            $table->unique(['id_regional', 'source_id_nilai']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('krs_konsolidasi');
        Schema::dropIfExists('nilai_konsolidasi');
    }
};
