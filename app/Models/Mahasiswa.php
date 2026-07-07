<?php

namespace App\Models;
use App\Traits\HasRegionalConnection;
use Illuminate\Database\Eloquent\Model;

class Mahasiswa extends Model
{

    use HasRegionalConnection;
    protected $table = 'mahasiswa';
    protected $primaryKey = 'nim';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'nim',
        'id_regional',
        'id_prodi',
        'nama_mahasiswa',
        'angkatan',
        'ips_terakhir',
        'is_deleted',
    ];

    protected $casts = [
        'is_deleted' => 'boolean',
        'ips_terakhir' => 'float',
    ];

    public function regional()
    {
        return $this->belongsTo(Regional::class, 'id_regional', 'id_regional');
    }

    public function krs()
    {
        return $this->hasMany(Krs::class, 'nim', 'nim');
    }

    public function nilai()
    {
        return $this->hasMany(Nilai::class, 'nim', 'nim');
    }
}
