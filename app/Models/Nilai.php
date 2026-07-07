<?php

namespace App\Models;
use App\Traits\HasRegionalConnection;
use Illuminate\Database\Eloquent\Model;

class Nilai extends Model
{
    use HasRegionalConnection;
    protected $table = 'nilai';
    protected $primaryKey = 'id_nilai';

    protected $fillable = ['id_regional', 'nim', 'id_kelas', 'nilai_akhir', 'is_finalisasi'];

    protected $casts = [
        'is_finalisasi' => 'boolean',
        'nilai_akhir' => 'float',
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'nim', 'nim');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas', 'id_kelas');
    }
}
