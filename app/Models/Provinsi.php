<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Provinsi extends Model
{
    protected $table = 'provinsi';
    protected $primaryKey = 'provinsi_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'provinsi_id',
        'nama',
    ];

    public function kabupaten(): HasMany
    {
        return $this->hasMany(Kabupaten::class, 'provinsi_id', 'provinsi_id');
    }
}
