<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('penelitian', function (Blueprint $table) {
            $table->string('berkas_hasil')->nullable()->after('berkas_pdf');
        });
    }

    public function down(): void
    {
        Schema::table('penelitian', function (Blueprint $table) {
            $table->dropColumn('berkas_hasil');
        });
    }
};
