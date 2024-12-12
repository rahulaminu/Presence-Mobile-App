<?php

use App\Http\Controllers\AuthC;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PresensiController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
})->name('login');
Route::post('/login', [AuthC::class, 'login']);
Route::post('/logout', [AuthC::class, 'logout'])->name('logout');

// Terapkan middleware untuk route dashboard
Route::middleware(['auth:siswa'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);

    //Presensi
    Route::get('/presensi/create', [PresensiController::class, 'create']);
    Route::post('/presensi/store', [PresensiController::class, 'store']);
});


