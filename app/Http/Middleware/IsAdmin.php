<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah user yang login punya role 'admin'
        if (Auth::check() && Auth::user()->role === 'admin') {
            return $next($request); // Boleh lewat
        }

        // Kalau bukan admin, tendang balik ke halaman utama
        return redirect('/')->with('pesan', 'Akses Ditolak! Anda bukan Admin.')->with('status', 'error');
    }
}