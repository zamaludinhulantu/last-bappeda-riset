<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    use HasFactory;

    protected $table = 'berita';

    protected $fillable = [
        'judul',
        'slug',
        'ringkasan',
        'cuplikan',
        'isi',
        'berkas_sampul',
        'gambar_sampul',
        'status',
        'dipublikasikan_pada',
        'penulis_id',
    ];

    protected $casts = [
        'dipublikasikan_pada' => 'datetime',
    ];

    public const CREATED_AT = 'dibuat_pada';
    public const UPDATED_AT = 'diubah_pada';

    public function creator()
    {
        return $this->belongsTo(User::class, 'penulis_id');
    }

    public function scopePublished($query)
    {
        return $query->where(function($q) {
                $q->whereNull('status')->orWhere('status', 'published');
            });
    }
}
