<?php

namespace App\Models;
use App\Traits\HasRegionalConnection;
use Illuminate\Database\Eloquent\Model;

class Regional extends Model
{
    use HasRegionalConnection;
    protected $table = 'regional';
    protected $primaryKey = 'id_regional';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = ['id_regional', 'nama_regional', 'lokasi', 'fakultas'];

    public function mahasiswa()
    {
        return $this->hasMany(Mahasiswa::class, 'id_regional', 'id_regional');
    }

    public function dosen()
    {
        return $this->hasMany(Dosen::class, 'id_regional', 'id_regional');
    }

    public function ruangan()
    {
        return $this->hasMany(Ruangan::class, 'id_regional', 'id_regional');
    }

    public function kelas()
    {
        return $this->hasMany(Kelas::class, 'id_regional', 'id_regional');
    }
}
