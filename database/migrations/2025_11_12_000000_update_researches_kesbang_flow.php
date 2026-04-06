<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penelitian', function (Blueprint $table) {
            // Tambahan data registrasi awal
            $table->string('nik_peneliti', 32)->after('penulis');
            $table->date('tanggal_mulai')->nullable()->after('tahun');
            $table->date('tanggal_selesai')->nullable()->after('tanggal_mulai');

            // Verifikasi Kesbangpol
            $table->timestamp('diverifikasi_kesbang_pada')->nullable()->after('diajukan_pada');
            $table->foreignId('diverifikasi_kesbang_oleh')->nullable()->after('diverifikasi_kesbang_pada')
                ->constrained('pengguna')->nullOnDelete();

            // Penanda unggah hasil
            $table->timestamp('hasil_diunggah_pada')->nullable()->after('disetujui_pada');

            // Catatan: kolom pdf_path tetap non-nullable; nilai awal dapat berupa string kosong
        });
    }

    public function down(): void
    {
        Schema::table('penelitian', function (Blueprint $table) {
            $table->dropColumn(['nik_peneliti', 'tanggal_mulai', 'tanggal_selesai', 'diverifikasi_kesbang_pada', 'hasil_diunggah_pada']);
            $table->dropConstrainedForeignId('diverifikasi_kesbang_oleh');
        });
    }
};
