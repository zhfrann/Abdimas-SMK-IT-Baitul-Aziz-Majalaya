<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Intrakurikuler extends Model
{
    use HasFactory;

    protected $table = 'intrakurikuler';
    protected $primaryKey = 'intrakurikuler_id';


    protected $fillable = [
        'nama_pelajaran',
        'kelas_ajar_id',
        'pengampu_user_id',
    ];

    /**
     * Intrakurikuler milik satu KelasAjar
     */
    public function kelasAjar(): BelongsTo
    {
        return $this->belongsTo(KelasAjar::class, 'kelas_ajar_id', 'kelas_ajar_id');
    }

    /**
     * Intrakurikuler diajar oleh satu User (pengampu)
     */
    public function pengampu(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pengampu_user_id', 'id');
    }

    /**
     * 1 Intrakurikuler punya banyak data kehadiran (per riwayat_kelas)
     */
    public function kehadiran(): HasMany
    {
        return $this->hasMany(KehadiranIntrakurikuler::class, 'intrakurikuler_id', 'intrakurikuler_id');
    }

    /**
     * 1 Intrakurikuler punya banyak tujuan pembelajaran
     */
    public function tujuanPembelajaran(): HasMany
    {
        return $this->hasMany(TujuanPembelajaran::class, 'intrakurikuler_id', 'intrakurikuler_id');
    }

    /**
     * 1 Intrakurikuler punya banyak asesmen formatif
     */
    public function asesmenFormatif(): HasMany
    {
        return $this->hasMany(AsesmenFormatif::class, 'intrakurikuler_id', 'intrakurikuler_id');
    }

    /**
     * 1 Intrakurikuler punya banyak lingkup materi
     */
    public function lingkupMateri(): HasMany
    {
        return $this->hasMany(LingkupMateri::class, 'intrakurikuler_id', 'intrakurikuler_id');
    }

    /**
     * 1 Intrakurikuler punya banyak asesmen sumatif
     */
    public function asesmenSumatif(): HasMany
    {
        return $this->hasMany(AsesmenSumatif::class, 'intrakurikuler_id', 'intrakurikuler_id');
    }
}
