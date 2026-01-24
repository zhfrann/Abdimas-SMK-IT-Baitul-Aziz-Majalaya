<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KehadiranEkstrakurikuler extends Model
{
    protected $table = 'kehadiran_ekstrakurikuler';
    protected $primaryKey = 'kehadiran_ekstrakurikuler_id';

    protected $fillable = [
        'ekstrakurikuler_id',
        'siswa_ekstrakurikuler_id',
        'tanggal',
        'status',
        'note',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'tanggal' => 'date:Y-m-d',
    ];

    // ===== RELATIONS =====

    public function ekstrakurikuler()
    {
        return $this->belongsTo(Ekstrakurikuler::class, 'ekstrakurikuler_id', 'ekstrakurikuler_id');
    }

    public function siswaEkstrakurikuler()
    {
        return $this->belongsTo(SiswaEkstrakurikuler::class, 'siswa_ekstrakurikuler_id', 'siswa_ekstrakurikuler_id');
    }
}
