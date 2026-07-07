<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_log', function (Blueprint $table) {
            $table->id('id_log');
            $table->string('user_id', 50);
            $table->string('role_user', 30);
            $table->string('aktivitas', 200);
            $table->ipAddress('ip_address');
            $table->jsonb('old_value')->nullable();
            $table->timestampTz('created_at')->useCurrent();
        });

        // REQ-C.6: append-only, blokir UPDATE & DELETE di level database
        DB::statement('
            CREATE RULE audit_log_no_update AS ON UPDATE TO audit_log DO INSTEAD NOTHING;
        ');
        DB::statement('
            CREATE RULE audit_log_no_delete AS ON DELETE TO audit_log DO INSTEAD NOTHING;
        ');
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_log');
    }
};
