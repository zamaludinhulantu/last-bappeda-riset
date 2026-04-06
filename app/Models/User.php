<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'pengguna';

    protected $authPasswordName = 'kata_sandi';
    protected $rememberTokenName = 'token_ingat';

    /**
     * Kolom yang boleh diisi otomatis (mass assignment)
     */
    protected $fillable = [
        'nama',
        'surel',
        'kata_sandi',
        'institusi_id', // ditambahkan untuk hubungan dengan kampus/lembaga
        'peran',
    ];

    /**
     * Kolom yang disembunyikan saat data user diubah ke JSON
     */
    protected $hidden = [
        'kata_sandi',
        'token_ingat',
    ];

    /**
     * Tipe data yang di-cast otomatis oleh Laravel
     */
    protected function casts(): array
    {
        return [
            'surel_terverifikasi_pada' => 'datetime',
            'kata_sandi' => 'hashed', // otomatis hash saat disimpan
        ];
    }

    public const CREATED_AT = 'dibuat_pada';
    public const UPDATED_AT = 'diubah_pada';

    /**
     * Relasi: User -> Institution (banyak user dari satu lembaga)
     */
    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class, 'institusi_id');
    }

    /**
     * Relasi: User -> Research (satu user bisa mengunggah banyak penelitian)
     */
    public function researches(): HasMany
    {
        return $this->hasMany(Research::class, 'pengunggah_id');
    }

    /**
     * Relasi: User -> ResearchReview (user bisa jadi reviewer)
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(ResearchReview::class, 'penelaah_id');
    }

    /**
     * Helper untuk memeriksa role pengguna.
     */
    public function hasRole(string|array $roles): bool
    {
        $roles = is_array($roles) ? $roles : func_get_args();
        return in_array($this->peran, $roles, true);
    }

    public function hasAdminAccess(): bool
    {
        return $this->hasRole(['admin', 'superadmin']);
    }

    public function hasKesbangAccess(): bool
    {
        return $this->hasRole(['kesbangpol', 'superadmin']);
    }

    public function isSuperAdmin(): bool
    {
        return $this->peran === 'superadmin';
    }

    public function getEmailForPasswordReset(): string
    {
        return $this->surel;
    }

    public function getEmailForVerification(): string
    {
        return $this->surel;
    }

    public function routeNotificationForMail($notification): string
    {
        return $this->surel;
    }
}
