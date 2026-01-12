<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ekstrakurikuler extends Model
{
    protected $table = 'ekstrakurikuler';
    protected $primaryKey = 'ekstrakurikuler_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'nama_pelajaran',
        'tahun_ajaran_id',
        'user_id', // pembina
    ];

    /**
     * Ekskul berada pada satu tahun ajaran
     */
    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id', 'tahun_ajaran_id');
    }

    /**
     * Pembina ekskul (users.id)
     */
    public function pembina(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Data kehadiran ekskul (catatan sakit/izin/absen) - sesuai migration kamu tidak ada riwayat_kelas_id
     */
    public function kehadiran(): HasMany
    {
        return $this->hasMany(KehadiranEkstrakurikuler::class, 'ekstrakurikuler_id', 'ekstrakurikuler_id');
    }

    /**
     * Relasi peserta ekskul (pivot siswa_ekstrakurikuler)
     */
    public function peserta(): HasMany
    {
        return $this->hasMany(SiswaEkstrakurikuler::class, 'ekstrakurikuler_id', 'ekstrakurikuler_id');
    }
}
