<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KehadiranIntrakurikuler extends Model
{
    protected $table = 'kehadiran_intrakurikuler';
    protected $primaryKey = 'kehadiran_intrakurikuler_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'intrakurikuler_id',
        'riwayat_kelas_id',
        'sakit',
        'izin',
        'absen',
    ];

    public function intrakurikuler(): BelongsTo
    {
        return $this->belongsTo(Intrakurikuler::class, 'intrakurikuler_id', 'intrakurikuler_id');
    }

    public function riwayatKelas(): BelongsTo
    {
        return $this->belongsTo(RiwayatKelas::class, 'riwayat_kelas_id', 'riwayat_kelas_id');
    }
}
