<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TahunAjaran extends Model
{
    use HasFactory;

    protected $table = 'tahun_ajaran';
    protected $primaryKey = 'tahun_ajaran_id';
    protected $fillable = ['tahun', 'semester'];

    public function kelasAjar()
    {
        return $this->hasMany(KelasAjar::class, 'tahun_ajaran_id', 'tahun_ajaran_id');
    }

    public function asesmenSumatif()
    {
        return $this->hasMany(AsesmenSumatif::class, 'tahun_ajaran_id', 'tahun_ajaran_id');
    }
}
