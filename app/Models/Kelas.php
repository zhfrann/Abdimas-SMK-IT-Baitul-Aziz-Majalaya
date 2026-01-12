<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;

    protected $table = 'kelas';
    protected $primaryKey = 'kelas_id';
    protected $fillable = ['nama_kelas'];

    public function kelasAjar()
    {
        return $this->hasMany(KelasAjar::class, 'kelas_id', 'kelas_id');
    }
}
