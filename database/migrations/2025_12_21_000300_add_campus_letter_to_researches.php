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
        Schema::table('penelitian', function (Blueprint $table) {
            if (!Schema::hasColumn('penelitian', 'berkas_surat_kampus')) {
                $table->string('berkas_surat_kampus')->nullable()->after('berkas_pdf');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penelitian', function (Blueprint $table) {
            if (Schema::hasColumn('penelitian', 'berkas_surat_kampus')) {
                $table->dropColumn('berkas_surat_kampus');
            }
        });
    }
};
