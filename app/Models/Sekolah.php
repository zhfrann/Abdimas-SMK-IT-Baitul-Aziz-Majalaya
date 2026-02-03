<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sekolah extends Model
{
    protected $table = 'sekolah';
    protected $primaryKey = 'npsn';
    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = [
        'npsn',
        'nama_sekolah',
        'nss',
        'alamat',
        'kode_pos',
        'kelurahan_id',
        'website',
        'email',
        'telp',
        'nama_kepala_sekolah',
        'nuptk_kepala_sekolah',
    ];

    public function kelurahan(): BelongsTo
    {
        return $this->belongsTo(Kelurahan::class, 'kelurahan_id', 'kelurahan_id');
    }
}
