<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KehadiranIntrakurikuler extends Model
{
    protected $table = 'kehadiran_intrakurikuler';
    protected $primaryKey = 'kehadiran_intrakurikuler_id';

    protected $fillable = [
        'intrakurikuler_id',
        'riwayat_kelas_id',
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

    public function intrakurikuler()
    {
        return $this->belongsTo(Intrakurikuler::class, 'intrakurikuler_id', 'intrakurikuler_id');
    }

    public function riwayatKelas()
    {
        return $this->belongsTo(RiwayatKelas::class, 'riwayat_kelas_id', 'riwayat_kelas_id');
    }

}
