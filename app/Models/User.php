<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Guru mapel: 1 user bisa mengampu banyak intrakurikuler
     * FK: intrakurikuler.pengampu_user_id -> users.id
     */
    public function intrakurikulerDiampu(): HasMany
    {
        return $this->hasMany(Intrakurikuler::class, 'pengampu_user_id', 'id');
    }

    /**
     * Wali kelas: 1 user bisa jadi wali di banyak kelas_ajar
     * FK: kelas_ajar.wali_user_id -> users.id
     */
    public function kelasAjarWali(): HasMany
    {
        return $this->hasMany(KelasAjar::class, 'wali_user_id', 'id');
    }

    /**
     * Profil staff: 1 user hanya 1 staff (user_id UNIQUE di tabel staff)
     * FK: staff.user_id -> users.id
     */
    public function staff(): HasOne
    {
        return $this->hasOne(Staff::class, 'user_id', 'id');
    }

    /**
     * Profil orang tua: 1 user hanya 1 orang_tua (user_id UNIQUE)
     * FK: orang_tua.user_id -> users.id
     */
    public function orangTua(): HasOne
    {
        return $this->hasOne(OrangTua::class, 'user_id', 'id');
    }

    /**
     * Profil siswa: 1 user hanya 1 siswa (user_id UNIQUE)
     * FK: siswa.user_id -> users.id
     */
    public function siswa(): HasOne
    {
        return $this->hasOne(Siswa::class, 'user_id', 'id');
    }
}
