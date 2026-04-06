<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penelitian', function (Blueprint $table) {
            if (!Schema::hasColumn('penelitian', 'berkas_surat_kesbang')) {
                $table->string('berkas_surat_kesbang')
                    ->nullable()
                    ->after('berkas_pdf');
            }
        });
    }

    public function down(): void
    {
        Schema::table('penelitian', function (Blueprint $table) {
            if (Schema::hasColumn('penelitian', 'berkas_surat_kesbang')) {
                $table->dropColumn('berkas_surat_kesbang');
            }
        });
    }
};
