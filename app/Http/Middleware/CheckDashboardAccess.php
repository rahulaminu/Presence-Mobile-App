<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckDashboardAccess
{
    public function handle(Request $request, Closure $next)
    {
        // Memeriksa apakah sesi pengguna ada
        if (!$request->session()->has('siswa_id')) {
            return redirect('/'); // Redirect ke halaman login jika sesi tidak ada
        }

        return $next($request);
    }
}