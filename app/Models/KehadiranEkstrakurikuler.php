<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KehadiranEkstrakurikuler extends Model
{
    protected $table = 'kehadiran_ekstrakurikuler';
    protected $primaryKey = 'kehadiran_ekstrakurikuler_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'ekstrakurikuler_id',
        'sakit',
        'izin',
        'absen',
    ];

    protected $casts = [
        'sakit' => 'integer',
        'izin'  => 'integer',
        'absen' => 'integer',
    ];

    public function ekstrakurikuler(): BelongsTo
    {
        return $this->belongsTo(Ekstrakurikuler::class, 'ekstrakurikuler_id', 'ekstrakurikuler_id');
    }
}
