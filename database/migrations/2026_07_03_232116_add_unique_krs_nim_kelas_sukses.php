<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            CREATE UNIQUE INDEX uq_krs_nim_kelas_sukses
            ON krs (nim, id_kelas)
            WHERE status = 'Sukses'
        ");
    }

    public function down(): void
    {
        DB::statement("DROP INDEX IF EXISTS uq_krs_nim_kelas_sukses");
    }
};
