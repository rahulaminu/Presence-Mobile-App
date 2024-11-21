<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Siswa;
use Illuminate\Support\Facades\Hash;

class SiswaSeeder extends Seeder
{
    public function run()
    {
        // Menambahkan data siswa
        Siswa::create([
            'nis' => '123',
            'nama' => 'John Doe',
            'no_hp' => '08123456789',
            'password' => Hash::make('123'), // Password yang di-hash
        ]);

        Siswa::create([
            'nis' => '321',
            'nama' => 'Asep Gula',
            'no_hp' => '08987654321',
            'password' => Hash::make('321'), // Password yang di-hash
        ]);

        // Tambahkan lebih banyak data siswa jika diperlukan
    }
}