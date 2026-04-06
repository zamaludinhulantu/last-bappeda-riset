<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penelitian', function (Blueprint $table) {
            if (!Schema::hasColumn('penelitian', 'nomor_surat_kesbang')) {
                $table->string('nomor_surat_kesbang')->nullable()->after('berkas_surat_kesbang');
            }
            if (!Schema::hasColumn('penelitian', 'tanggal_surat_kesbang')) {
                $table->date('tanggal_surat_kesbang')->nullable()->after('nomor_surat_kesbang');
            }
        });
    }

    public function down(): void
    {
        Schema::table('penelitian', function (Blueprint $table) {
            if (Schema::hasColumn('penelitian', 'nomor_surat_kesbang')) {
                $table->dropColumn('nomor_surat_kesbang');
            }
            if (Schema::hasColumn('penelitian', 'tanggal_surat_kesbang')) {
                $table->dropColumn('tanggal_surat_kesbang');
            }
        });
    }
};
