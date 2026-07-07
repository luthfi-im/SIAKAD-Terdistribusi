<?php

namespace App\Models;
use App\Traits\HasRegionalConnection;
use Illuminate\Database\Eloquent\Model;

class Ruangan extends Model
{
    use HasRegionalConnection;
    protected $table = 'ruangan';
    protected $primaryKey = 'id_ruangan';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['id_ruangan', 'id_regional', 'nama_ruangan', 'kapasitas'];

    public function regional()
    {
        return $this->belongsTo(Regional::class, 'id_regional', 'id_regional');
    }

    public function kelas()
    {
        return $this->hasMany(Kelas::class, 'id_ruangan', 'id_ruangan');
    }
}
