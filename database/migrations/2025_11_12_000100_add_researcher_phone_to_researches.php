<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penelitian', function (Blueprint $table) {
            $table->string('telepon_peneliti', 32)->after('nik_peneliti');
        });
    }

    public function down(): void
    {
        Schema::table('penelitian', function (Blueprint $table) {
            $table->dropColumn('telepon_peneliti');
        });
    }
};
