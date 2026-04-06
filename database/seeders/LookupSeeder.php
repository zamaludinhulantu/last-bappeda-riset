<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Field;
use App\Models\Institution;

class LookupSeeder extends Seeder
{
    public function run(): void
    {
        // Bidang penelitian awal
        $fields = ['Kesehatan', 'Pendidikan', 'Ekonomi', 'Pertanian', 'Teknologi'];
        foreach ($fields as $f) {
            Field::firstOrCreate(['nama' => $f]);
        }

        // Contoh institusi
        Institution::firstOrCreate([
            'nama' => 'Universitas Contoh',
            'jenis' => 'Universitas',
            'kota' => 'Bandung'
        ]);
    }
}
