<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class SuperAdminSeeder extends Seeder
{
    /**
     * Jalankan seeder untuk membuat/menjamin akun admin utama.
     */
    public function run(): void
    {
        $accounts = [
            [
                'nama' => 'Super Admin',
                'surel' => env('SUPERADMIN_EMAIL', 'superadmin@bapppeda.local'),
                'kata_sandi' => env('SUPERADMIN_PASSWORD', 'bappeda@123'),
                'peran' => 'superadmin',
            ],
            [
                'nama' => 'Admin BAPPPEDA',
                'surel' => env('ADMIN_EMAIL', 'admin@bapppeda.local'),
                'kata_sandi' => env('ADMIN_PASSWORD', 'bappeda@123'),
                'peran' => 'admin',
            ],
            [
                'nama' => 'Kesbangpol',
                'surel' => env('KESBANG_EMAIL', 'kesbang@bapppeda.local'),
                'kata_sandi' => env('KESBANG_PASSWORD', 'bappeda@123'),
                'peran' => 'kesbangpol',
            ],
        ];

        foreach ($accounts as $account) {
            User::updateOrCreate(
                ['surel' => $account['surel']],
                [
                    'nama' => $account['nama'],
                    'kata_sandi' => $account['kata_sandi'],
                    'peran' => $account['peran'],
                    'surel_terverifikasi_pada' => now(),
                ]
            );
        }
    }
}
