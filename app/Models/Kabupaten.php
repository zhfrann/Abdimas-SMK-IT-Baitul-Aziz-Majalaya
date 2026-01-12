<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kabupaten extends Model
{
    protected $table = 'kabupaten';
    protected $primaryKey = 'kabupaten_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'kabupaten_id',
        'provinsi_id',
        'nama',
    ];

    public function provinsi(): BelongsTo
    {
        return $this->belongsTo(Provinsi::class, 'provinsi_id', 'provinsi_id');
    }

    public function kecamatan(): HasMany
    {
        return $this->hasMany(Kecamatan::class, 'kabupaten_id', 'kabupaten_id');
    }

    /**
     * (Opsional) daftar siswa yang lahir di kabupaten ini
     */
    public function siswaTempatLahir(): HasMany
    {
        return $this->hasMany(Siswa::class, 'tempat_lahir_kabupaten_id', 'kabupaten_id');
    }
}
