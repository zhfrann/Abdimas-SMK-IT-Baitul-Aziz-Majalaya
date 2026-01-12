<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kelurahan extends Model
{
    protected $table = 'kelurahan';
    protected $primaryKey = 'kelurahan_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'kelurahan_id',
        'kecamatan_id',
        'nama',
    ];

    public function kecamatan(): BelongsTo
    {
        return $this->belongsTo(Kecamatan::class, 'kecamatan_id', 'kecamatan_id');
    }

    /**
     * Relasi yang pakai kelurahan_id nullable di tabel sekolah
     */
    public function sekolah(): HasMany
    {
        return $this->hasMany(Sekolah::class, 'kelurahan_id', 'kelurahan_id');
    }

    public function siswa(): HasMany
    {
        return $this->hasMany(Siswa::class, 'kelurahan_id', 'kelurahan_id');
    }

    public function orangTua(): HasMany
    {
        return $this->hasMany(OrangTua::class, 'kelurahan_id', 'kelurahan_id');
    }
}
