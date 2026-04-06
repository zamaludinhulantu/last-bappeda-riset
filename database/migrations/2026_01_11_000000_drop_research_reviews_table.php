<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('ulasan_penelitian');
    }

    public function down(): void
    {
        Schema::create('ulasan_penelitian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penelitian_id')->constrained('penelitian')->onDelete('cascade');
            $table->foreignId('penelaah_id')->constrained('pengguna')->onDelete('cascade');
            $table->enum('keputusan', ['approved', 'rejected']);
            $table->text('catatan')->nullable();
            $table->timestamp('dibuat_pada')->nullable();
            $table->timestamp('diubah_pada')->nullable();
        });
    }
};
