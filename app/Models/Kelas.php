<?php

namespace App\Models;
use App\Traits\HasRegionalConnection;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasRegionalConnection;
    protected $table = 'kelas';
    protected $primaryKey = 'id_kelas';

    protected $fillable = [
        'id_regional',
        'kode_mk',
        'nip_dosen',
        'id_ruangan',
        'semester',
        'tahun_akademik',
        'kuota',
        'sisa_kuota',
    ];

    public function regional()
    {
        return $this->belongsTo(Regional::class, 'id_regional', 'id_regional');
    }

    public function mataKuliah()
    {
        return $this->belongsTo(MataKuliah::class, 'kode_mk', 'kode_mk');
    }

    public function dosen()
    {
        return $this->belongsTo(Dosen::class, 'nip_dosen', 'nip');
    }

    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class, 'id_ruangan', 'id_ruangan');
    }

    public function krs()
    {
        return $this->hasMany(Krs::class, 'id_kelas', 'id_kelas');
    }

    public function nilai()
    {
        return $this->hasMany(Nilai::class, 'id_kelas', 'id_kelas');
    }
}
