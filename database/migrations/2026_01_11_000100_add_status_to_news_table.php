<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('berita') || Schema::hasColumn('berita', 'status')) {
            return;
        }

        Schema::table('berita', function (Blueprint $table) {
            $table->string('status', 30)->nullable()->after('penulis_id');
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('berita') || !Schema::hasColumn('berita', 'status')) {
            return;
        }

        Schema::table('berita', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
