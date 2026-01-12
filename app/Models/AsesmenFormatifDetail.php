<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AsesmenFormatifDetail extends Model
{
    protected $table = 'asesmen_formatif_detail';
    protected $primaryKey = 'asesmen_formatif_detail_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $casts = [
        'kktp' => 'boolean',
        'tampil' => 'boolean',
    ];

    protected $fillable = [
        'asesmen_formatif_id',
        'tujuan_pembelajaran_id',
        'kktp',
        'tampil',
    ];

    public function asesmenFormatif(): BelongsTo
    {
        return $this->belongsTo(AsesmenFormatif::class, 'asesmen_formatif_id', 'asesmen_formatif_id');
    }

    public function tujuanPembelajaran(): BelongsTo
    {
        return $this->belongsTo(TujuanPembelajaran::class, 'tujuan_pembelajaran_id', 'tujuan_pembelajaran_id');
    }
}
