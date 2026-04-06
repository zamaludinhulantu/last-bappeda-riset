<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Institution extends Model
{
    use HasFactory;

    protected $table = 'institusi';

    protected $fillable = ['nama', 'jenis', 'kota'];

    public const CREATED_AT = 'dibuat_pada';
    public const UPDATED_AT = 'diubah_pada';

    // Relasi: satu institusi punya banyak penelitian
    public function researches()
    {
        return $this->hasMany(Research::class, 'institusi_id');
    }

    // Relasi: satu institusi punya banyak user (peneliti)
    public function users()
    {
        return $this->hasMany(User::class, 'institusi_id');
    }
}
