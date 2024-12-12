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
                // Set session siswa_id jika login berhasil
                $request->session()->put('siswa_id', $siswa->id);
                return redirect('/dashboard');
            } else {
                return back()->withErrors([
                    'password' => 'Password salah.',
                ]);
            }
        }

        return back()->withErrors([
            'nis' => 'NIS atau password salah.',
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'nis' => 'required|unique:siswa',
            'nama' => 'required',
            'password' => 'required',
        ]);

        Siswa::create([
            'nis' => $request->nis,
            'nama' => $request->nama,
            'password' => Hash::make($request->password),
        ]);

        return redirect('/')->with('success', 'Akun berhasil dibuat.');
    }

    public function logout(Request $request)
    {
        $request->session()->flush();
        return redirect('/');
    }
}
