<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $connection = 'pgsql_pusat';
    protected $table = 'audit_log';
    protected $primaryKey = 'id_log';
    public $timestamps = false;

    protected $fillable = ['user_id', 'role_user', 'aktivitas', 'ip_address', 'old_value'];

    protected $casts = [
        'old_value' => 'array',
        'created_at' => 'datetime',
    ];
}
