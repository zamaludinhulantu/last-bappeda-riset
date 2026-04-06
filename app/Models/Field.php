<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Field extends Model
{
    use HasFactory;

    protected $table = 'bidang';

    protected $fillable = ['nama'];

    public const CREATED_AT = 'dibuat_pada';
    public const UPDATED_AT = 'diubah_pada';

    // Relasi: satu bidang bisa punya banyak penelitian
    public function researches()
    {
        return $this->hasMany(Research::class, 'bidang_id');
    }

}
