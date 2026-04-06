<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Research extends Model
{
    use HasFactory;
    protected $table = 'penelitian';

    protected $fillable = [
        'judul',
        'penulis',
        'nik_peneliti',
        'telepon_peneliti',
        'institusi_id',
        'bidang_id',
        'lokasi',
        'tahun',
        'tanggal_mulai',
        'tanggal_selesai',
        'abstrak',
        'kata_kunci',
        'berkas_pdf',
        'berkas_surat_kampus',
        'berkas_hasil',
        'berkas_surat_kesbang',
        'nomor_surat_kesbang',
        'tanggal_surat_kesbang',
        'status',
        'pengunggah_id',
        'diajukan_pada',
        'diverifikasi_kesbang_pada',
        'diverifikasi_kesbang_oleh',
        'disetujui_pada',
        'ditolak_pada',
        'alasan_penolakan',
        'catatan_keputusan',
        'diajukan_ulang_pada',
        'hasil_diunggah_pada',
        'disetujui_oleh',
        'ditolak_oleh'
    ];

    protected $casts = [
        'diajukan_pada' => 'datetime',
        'diverifikasi_kesbang_pada' => 'datetime',
        'disetujui_pada' => 'datetime',
        'ditolak_pada' => 'datetime',
        'hasil_diunggah_pada' => 'datetime',
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'diajukan_ulang_pada' => 'datetime',
        'tanggal_surat_kesbang' => 'date',
    ];

    public const CREATED_AT = 'dibuat_pada';
    public const UPDATED_AT = 'diubah_pada';

    // Relasi ke institusi
    public function institution()
    {
        return $this->belongsTo(Institution::class, 'institusi_id');
    }

    // Relasi ke bidang
    public function field()
    {
        return $this->belongsTo(Field::class, 'bidang_id');
    }

    // Relasi ke user pengunggah
    public function submitter()
    {
        return $this->belongsTo(User::class, 'pengunggah_id');
    }

    // Alias pengunggah (submitted_by)
    public function submittedBy()
    {
        return $this->submitter();
    }

    // Alias user untuk kompatibilitas tampilan lama
    public function user()
    {
        return $this->submitter();
    }

    // Relasi ke review penelitian
    public function reviews()
    {
        return $this->hasMany(ResearchReview::class, 'penelitian_id');
    }

    // Relasi penolak
    public function rejectedBy()
    {
        return $this->belongsTo(User::class, 'ditolak_oleh');
    }

    // Relasi verifikator Kesbang
    public function kesbangVerifier()
    {
        return $this->belongsTo(User::class, 'diverifikasi_kesbang_oleh');
    }

    // Relasi approver BAPPPEDA
    public function approver()
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }
}
