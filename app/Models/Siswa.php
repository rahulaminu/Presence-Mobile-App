<?php

namespace App\Models;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Siswa extends Model
{
    use HasFactory;

    // Menentukan nama tabel jika tidak sesuai dengan konvensi
    protected $table = 'siswa';

    // Menentukan field yang dapat diisi massal
    protected $fillable = [
        'nis',
        'nama',
        'no_hp',
        'password',
    ];
}