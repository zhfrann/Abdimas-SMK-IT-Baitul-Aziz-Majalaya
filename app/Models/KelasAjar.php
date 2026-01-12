<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KelasAjar extends Model
{
    use HasFactory;

    protected $table = 'kelas_ajar';
    protected $primaryKey = 'kelas_ajar_id';
    protected $fillable = ['kelas_id', 'tahun_ajaran_id', 'wali_user_id'];

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id');
    }

    public function waliKelas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'wali_user_id');
    }
}
