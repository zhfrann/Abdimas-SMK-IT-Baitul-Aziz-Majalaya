<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AsesmenSumatif extends Model
{
    protected $table = 'asesmen_sumatif';
    protected $primaryKey = 'asesmen_sumatif_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'intrakurikuler_id',
        'tahun_ajaran_id',
        'asesmen_type',
        'lingkup_materi_id',
        'asesmen_no',
    ];

    public function intrakurikuler(): BelongsTo
    {
        return $this->belongsTo(Intrakurikuler::class, 'intrakurikuler_id', 'intrakurikuler_id');
    }

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id', 'tahun_ajaran_id');
    }

    public function lingkupMateri(): BelongsTo
    {
        return $this->belongsTo(LingkupMateri::class, 'lingkup_materi_id', 'lingkup_materi_id');
    }

    public function skorSiswa(): HasMany
    {
        return $this->hasMany(SkorAsesmenSiswa::class, 'asesmen_sumatif_id', 'asesmen_sumatif_id');
    }
}
