<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AsesmenFormatif extends Model
{
    protected $table = 'asesmen_formatif';
    protected $primaryKey = 'asesmen_formatif_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'intrakurikuler_id',
        'deskripsi_catatan_tertinggi',
        'deskripsi_catatan_terendah',
    ];

    public function intrakurikuler(): BelongsTo
    {
        return $this->belongsTo(Intrakurikuler::class, 'intrakurikuler_id', 'intrakurikuler_id');
    }

    public function details(): HasMany
    {
        return $this->hasMany(AsesmenFormatifDetail::class, 'asesmen_formatif_id', 'asesmen_formatif_id');
    }
}
