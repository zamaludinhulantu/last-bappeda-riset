<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('berita', 'cuplikan')) {
            DB::statement('ALTER TABLE berita MODIFY cuplikan TEXT NULL');
        } else {
            Schema::table('berita', function (Blueprint $table) {
                $table->text('cuplikan')->nullable()->after('slug');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('berita', 'cuplikan')) {
            return;
        }

        // Roll back to varchar(255) if needed.
        DB::statement('ALTER TABLE berita MODIFY cuplikan VARCHAR(255) NULL');
    }
};
