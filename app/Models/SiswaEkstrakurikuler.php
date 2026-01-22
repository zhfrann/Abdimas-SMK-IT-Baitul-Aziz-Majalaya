<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SiswaEkstrakurikuler extends Model
{
    protected $table = 'siswa_ekstrakurikuler';
    protected $primaryKey = 'siswa_ekstrakurikuler_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'siswa_id',
        'ekstrakurikuler_id',
    ];

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'siswa_id', 'siswa_id');
    }

    public function ekstrakurikuler(): BelongsTo
    {
        return $this->belongsTo(Ekstrakurikuler::class, 'ekstrakurikuler_id', 'ekstrakurikuler_id');
    }

    public function penilaians(): HasMany
    {
        return $this->hasMany(PenilaianEkstrakurikuler::class, 'siswa_ekstrakurikuler_id', 'siswa_ekstrakurikuler_id');
    }
}
