<?php

namespace App\Models;
use App\Traits\HasRegionalConnection;
use Illuminate\Database\Eloquent\Model;

class Dosen extends Model
{
    use HasRegionalConnection;
    protected $table = 'dosen';
    protected $primaryKey = 'nip';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['nip', 'id_regional', 'id_prodi', 'nama_dosen'];

    public function regional()
    {
        return $this->belongsTo(Regional::class, 'id_regional', 'id_regional');
    }

    public function kelas()
    {
        return $this->hasMany(Kelas::class, 'nip_dosen', 'nip');
    }
}
