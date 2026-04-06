<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResearchReview extends Model
{
    use HasFactory;

    protected $table = 'ulasan_penelitian';

    protected $fillable = ['penelitian_id', 'penelaah_id', 'keputusan', 'catatan'];

    public const CREATED_AT = 'dibuat_pada';
    public const UPDATED_AT = 'diubah_pada';

    public function research()
    {
        return $this->belongsTo(Research::class, 'penelitian_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'penelaah_id');
    }
}
