<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PenilaianEkstrakurikuler extends Model
{
    protected $table = 'penilaian_ekstrakurikuler';
    protected $primaryKey = 'penilaian_ekstrakurikuler_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'siswa_ekstrakurikuler_id',
        'deskripsi',
    ];

    public function siswaEkskul(): BelongsTo
    {
        return $this->belongsTo(SiswaEkstrakurikuler::class, 'siswa_ekstrakurikuler_id', 'siswa_ekstrakurikuler_id');
    }
}
