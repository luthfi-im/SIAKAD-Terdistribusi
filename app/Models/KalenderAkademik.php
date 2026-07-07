<?php

namespace App\Models;
use App\Traits\HasRegionalConnection;
use Illuminate\Database\Eloquent\Model;

class KalenderAkademik extends Model
{
    use HasRegionalConnection;
    protected $table = 'kalender_akademik';

    protected $fillable = ['semester', 'tahun_ajaran', 'status_aktif'];

    protected $casts = [
        'status_aktif' => 'boolean',
    ];
}
