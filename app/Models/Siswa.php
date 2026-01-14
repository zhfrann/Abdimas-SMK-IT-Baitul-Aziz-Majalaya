<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Siswa extends Model
{
    protected $table = 'siswa';
    protected $primaryKey = 'siswa_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'user_id',
        'nis',
        'nisn',
        'nama',
        'jenis_kelamin',
        'tempat_lahir_kabupaten_id',
        'tanggal_lahir',
        'agama',
        'pendidikan_sebelumnya',
        'alamat',
        'orang_tua_id',
        'kelurahan_id',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    /**
     * Profil siswa terkait satu akun user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Satu siswa punya satu data orang tua (FK orang_tua_id)
     */
    public function orangTua(): BelongsTo
    {
        return $this->belongsTo(OrangTua::class, 'orang_tua_id', 'orang_tua_id');
    }

    /**
     * Domisili siswa (kelurahan)
     */
    public function kelurahan(): BelongsTo
    {
        return $this->belongsTo(Kelurahan::class, 'kelurahan_id', 'kelurahan_id');
    }

    /**
     * Tempat lahir siswa (kabupaten)
     */
    public function tempatLahirKabupaten(): BelongsTo
    {
        return $this->belongsTo(Kabupaten::class, 'tempat_lahir_kabupaten_id', 'kabupaten_id');
    }

    /**
     * Riwayat kelas siswa (bisa banyak, tergantung tahun ajaran/kelas_ajar)
     */
    public function riwayatKelas(): HasMany
    {
        return $this->hasMany(RiwayatKelas::class, 'siswa_id', 'siswa_id');
    }

    public function siswaEkstrakurikuler(): HasMany
    {
        return $this->hasMany(SiswaEkstrakurikuler::class, 'siswa_id', 'siswa_id');
    }
}
