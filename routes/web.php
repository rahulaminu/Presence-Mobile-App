<?php

use App\Http\Controllers\AuthC;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

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
});
Route::post('/login', [AuthC::class, 'login']);

// Terapkan middleware untuk route dashboard
Route::middleware(['check.dashboard'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
});
