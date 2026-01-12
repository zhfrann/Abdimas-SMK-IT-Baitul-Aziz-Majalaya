<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrangTua extends Model
{
    protected $table = 'orang_tua';
    protected $primaryKey = 'orang_tua_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'user_id',
        'nama_ayah',
        'nama_ibu',
        'pekerjaan_ayah',
        'pekerjaan_ibu',
        'jalan',
        'kelurahan_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function kelurahan(): BelongsTo
    {
        return $this->belongsTo(Kelurahan::class, 'kelurahan_id', 'kelurahan_id');
    }

    /**
     * 1 orang tua bisa punya banyak siswa (di tabel siswa ada orang_tua_id)
     */
    public function siswa(): HasMany
    {
        return $this->hasMany(Siswa::class, 'orang_tua_id', 'orang_tua_id');
    }
}
