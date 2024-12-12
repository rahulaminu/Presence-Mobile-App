<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Siswa extends Authenticatable
{
    use HasFactory;

    protected $table = 'siswa';
    public $timestamps = false;
    protected $fillable = [
        'nis',
        'nama',
        'no_hp',
        'password',
    ];

    protected $hidden = [
        'password'
    ];
}