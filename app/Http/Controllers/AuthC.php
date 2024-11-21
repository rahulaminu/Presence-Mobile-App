<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthC extends Controller
{
    public function login(Request $request)
    {
        // Validasi input
        $request->validate([
            'nis' => 'required',
            'password' => 'required',
        ]);

        // Mencari siswa berdasarkan NIS
        $siswa = Siswa::where('nis', $request->nis)->first();

        // Memeriksa apakah siswa ditemukan dan password cocok
        if ($siswa) {
            if (Hash::check($request->password, $siswa->password)) {
                return redirect('/dashboard');
            } else {
                // Jika password tidak cocok
                return back()->withErrors([
                    'password' => 'Password salah.',
                ]);
            }
        }

        // Jika gagal, kembali ke halaman login dengan pesan error
        return back()->withErrors([
            'nis' => 'NIS atau password salah.',
        ]);
    }
}
