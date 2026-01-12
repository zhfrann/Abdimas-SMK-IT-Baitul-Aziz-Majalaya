<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SkorAsesmenSiswa extends Model
{
    protected $table = 'skor_asesmen_siswa';
    protected $primaryKey = 'skor_asesmen_siswa_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'riwayat_kelas_id',
        'asesmen_sumatif_id',
        'nilai',
        'tahun_ajaran_id',
    ];

    protected $casts = [
        'nilai' => 'integer',
    ];

    public function riwayatKelas(): BelongsTo
    {
        return $this->belongsTo(RiwayatKelas::class, 'riwayat_kelas_id', 'riwayat_kelas_id');
    }

    public function asesmenSumatif(): BelongsTo
    {
        return $this->belongsTo(AsesmenSumatif::class, 'asesmen_sumatif_id', 'asesmen_sumatif_id');
    }

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id', 'tahun_ajaran_id');
    }
}
