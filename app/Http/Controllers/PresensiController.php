<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


class PresensiController extends Controller
{
    public function create(){
        return view('presensi.create');
    }
    
    public function store(Request $request){
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
        $formatName = $nis."_".$tgl_presensi;
        $image_parts = explode(";base64,", $image);
        $image_base64 = base64_decode($image_parts[1]);
        $fileName = $formatName.".png";
        $file = $folderPath.$fileName;
        Storage::put($file, $image_base64);
        
        return response()->json(['message' => 'Berhasil melakukan presensi']);
    }
}
