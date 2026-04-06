<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactInfo extends Model
{
    use HasFactory;

    protected $table = 'informasi_kontak';

    protected $fillable = [
        'judul',
        'subjudul',
        'surel',
        'telepon',
        'whatsapp',
        'alamat',
        'jam_layanan',
    ];

    public const CREATED_AT = 'dibuat_pada';
    public const UPDATED_AT = 'diubah_pada';

    public static function defaults(): array
    {
        return [
            'judul' => 'Siap membantu kebutuhan data Anda',
            'subjudul' => 'Hubungi untuk pertanyaan, masukan, atau permintaan data tambahan.',
            'surel' => 'publikasi@bapppeda.go.id',
            'telepon' => '(0435) 123-456',
            'whatsapp' => null,
            'alamat' => 'Jl. Pembangunan No. 1, Kota Gorontalo',
            'jam_layanan' => 'Senin - Jumat, 08.00 - 16.00 WITA',
        ];
    }

    public static function current(): self
    {
        return static::first() ?? new static(static::defaults());
    }

    public function value(string $key): ?string
    {
        $value = $this->{$key} ?? null;
        if (is_string($value) && trim($value) !== '') {
            return $value;
        }

        $defaults = static::defaults();
        return $defaults[$key] ?? null;
    }
}
