<?php

namespace App\Models;
use App\Traits\HasRegionalConnection;
use Illuminate\Database\Eloquent\Model;

class Krs extends Model
{
    use HasRegionalConnection;
    protected $table = 'krs';
    protected $primaryKey = 'id_krs';

    protected $fillable = ['id_regional', 'nim', 'id_kelas', 'status'];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'nim', 'nim');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas', 'id_kelas');
    }
}