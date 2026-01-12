<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LingkupMateri extends Model
{
    protected $table = 'lingkup_materi';
    protected $primaryKey = 'lingkup_materi_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'intrakurikuler_id',
        'nama_materi',
    ];

    public function intrakurikuler(): BelongsTo
    {
        return $this->belongsTo(Intrakurikuler::class, 'intrakurikuler_id', 'intrakurikuler_id');
    }

    public function asesmenSumatif(): HasMany
    {
        return $this->hasMany(AsesmenSumatif::class, 'lingkup_materi_id', 'lingkup_materi_id');
    }
}
