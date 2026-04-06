<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penelitian', function (Blueprint $table) {
            $table->index('status');
            $table->index('disetujui_pada');
            $table->index('tahun');
            $table->index('dibuat_pada');
        });
    }

    public function down(): void
    {
        Schema::table('penelitian', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['disetujui_pada']);
            $table->dropIndex(['tahun']);
            $table->dropIndex(['dibuat_pada']);
        });
    }
};
