<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void
{
    Schema::create('penelitian', function (Blueprint $table) {
        $table->id();
        $table->string('judul');
        $table->string('penulis');
        $table->foreignId('institusi_id')->constrained('institusi')->cascadeOnDelete();
        $table->foreignId('bidang_id')->constrained('bidang')->cascadeOnDelete();
        $table->year('tahun');
        $table->text('abstrak')->nullable();
        $table->string('kata_kunci')->nullable();
        $table->string('berkas_pdf'); // path file PDF
        $table->enum('status', ['draft', 'submitted', 'approved', 'rejected'])->default('draft');
        $table->foreignId('pengunggah_id')->constrained('pengguna');
        $table->timestamp('diajukan_pada')->nullable();
        $table->timestamp('disetujui_pada')->nullable();
        $table->timestamp('ditolak_pada')->nullable();
        $table->foreignId('disetujui_oleh')->nullable()->constrained('pengguna')->nullOnDelete();
        $table->foreignId('ditolak_oleh')->nullable()->constrained('pengguna')->nullOnDelete();
        $table->timestamp('dibuat_pada')->nullable();
        $table->timestamp('diubah_pada')->nullable();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penelitian');
    }
};
