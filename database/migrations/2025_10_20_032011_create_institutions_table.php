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
    Schema::create('institusi', function (Blueprint $table) {
        $table->id();
        $table->string('nama')->unique();
        $table->string('jenis')->nullable(); // Universitas, Lembaga, LSM, dll.
        $table->string('kota')->nullable();
        $table->timestamp('dibuat_pada')->nullable();
        $table->timestamp('diubah_pada')->nullable();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('institusi');
    }
};
