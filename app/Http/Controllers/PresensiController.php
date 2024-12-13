<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


class PresensiController extends Controller
{
    public function create()
    {
        $hariini = date('Y-m-d');
        $nis = Auth::guard('siswa')->user()->nis;

        // Cek apakah sudah ada presensi pulang hari ini
        $sudahPulang = DB::table('presensi')
            ->where('tgl_presensi', $hariini)
            ->where('nis', $nis)
            ->whereNotNull('jam_out') // Pastikan jam_out tidak null
            ->exists();

        $cek = DB::table('presensi')->where('tgl_presensi', $hariini)->where('nis', $nis)->count();
        return view('presensi.create', compact('cek', 'sudahPulang'));
    }

    public function store(Request $request)
    {
        $siswa = Auth::guard('siswa')->user();
        if (!$siswa) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        $nis = $siswa->nis;
        $tgl_presensi = date('Y-m-d');
        $jam = date('H:i:s');
        $lokasi = $request->lokasi;
        $image = $request->image;
        $folderPath = "public/uploads/absensi/";
        $formatName = $nis . "_" . $tgl_presensi;
        $image_parts = explode(";base64,", $image);
        $image_base64 = base64_decode($image_parts[1]);
        $fileName = $formatName . ".png";
        $file = $folderPath . $fileName;

        // cek apakah sudah ada data presensi hari ini
        $cek = DB::table('presensi')->where('tgl_presensi', $tgl_presensi)->where('nis', $nis)->count();
        if ($cek > 0) {
            $data_pulang = [
                'jam_out' => $jam,
                'lokasi_out' => $lokasi,
                'foto_out' => $fileName
            ];
            $update = DB::table('presensi')->where('tgl_presensi', $tgl_presensi)->where('nis', $nis)->update($data_pulang);
            if ($update) {
                Storage::put($file, $image_base64);
                return response()->json(['message' => 'Terima kasih, Anda sudah melakukan presensi pulang']);
            } else {
                return response()->json(['error' => 'Gagal melakukan presensi']);
            }
        }
        // jika belum ada data presensi hari ini
        $data = [
            'nis' => $nis,
            'tgl_presensi' => $tgl_presensi,
            'jam_in' => $jam,
            'lokasi_in' => $lokasi,
            'foto_in' => $fileName
        ];
        $simpan = DB::table('presensi')->insert($data);
        if ($simpan) {
            Storage::put($file, $image_base64);
            return response()->json(['message' => 'Berhasil melakukan presensi']);
        } else {
            return response()->json(['error' => 'Gagal melakukan presensi']);
        }
    }
}
