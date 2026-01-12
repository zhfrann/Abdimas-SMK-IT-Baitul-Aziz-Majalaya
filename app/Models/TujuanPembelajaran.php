<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TujuanPembelajaran extends Model
{
    protected $table = 'tujuan_pembelajaran';
    protected $primaryKey = 'tujuan_pembelajaran_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'intrakurikuler_id',
        'deskripsi',
    ];

    public function intrakurikuler(): BelongsTo
    {
        return $this->belongsTo(Intrakurikuler::class, 'intrakurikuler_id', 'intrakurikuler_id');
    }

    public function formatifDetails(): HasMany
    {
        return $this->hasMany(AsesmenFormatifDetail::class, 'tujuan_pembelajaran_id', 'tujuan_pembelajaran_id');
    }
}
