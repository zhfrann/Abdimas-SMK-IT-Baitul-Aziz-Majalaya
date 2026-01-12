<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SiswaEkstrakurikuler extends Model
{
    protected $table = 'siswa_ekstrakurikuler';
    protected $primaryKey = 'siswa_ekstrakurikuler_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'riwayat_kelas_id',
        'ekstrakurikuler_id',
    ];

    public function riwayatKelas(): BelongsTo
    {
        return $this->belongsTo(RiwayatKelas::class, 'riwayat_kelas_id', 'riwayat_kelas_id');
    }

    public function ekstrakurikuler(): BelongsTo
    {
        return $this->belongsTo(Ekstrakurikuler::class, 'ekstrakurikuler_id', 'ekstrakurikuler_id');
    }
}
