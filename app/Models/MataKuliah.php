<?php

namespace App\Models;

use App\Traits\HasRegionalConnection;
use Illuminate\Database\Eloquent\Model;

class MataKuliah extends Model
{
    use HasRegionalConnection;
    protected $table = 'mata_kuliah';
    protected $primaryKey = 'kode_mk';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['kode_mk', 'kode_mk_prasyarat', 'id_prodi', 'nama_mk', 'sks', 'semester'];

    public function prasyarat()
    {
        return $this->belongsTo(MataKuliah::class, 'kode_mk_prasyarat', 'kode_mk');
    }

    public function kelas()
    {
        return $this->hasMany(Kelas::class, 'kode_mk', 'kode_mk');
    }
}
