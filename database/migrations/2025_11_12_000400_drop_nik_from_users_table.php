<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('pengguna', 'nik')) {
            Schema::table('pengguna', function (Blueprint $table) {
                $table->dropUnique('pengguna_nik_unique');
                $table->dropColumn('nik');
            });
        }
    }

    public function down(): void
    {
        Schema::table('pengguna', function (Blueprint $table) {
            $table->string('nik', 32)->nullable()->unique()->after('surel');
        });
    }
};
