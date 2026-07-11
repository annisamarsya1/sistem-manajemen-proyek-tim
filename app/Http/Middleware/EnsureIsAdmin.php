<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class EnsureIsAdmin
 * 
 * Middleware untuk membatasi akses route hanya kepada pengguna
 * dengan role 'admin'. Pengguna lain akan diarahkan kembali ke dashboard
 * dengan pesan error.
 */
class EnsureIsAdmin
{
    /**
     * Izinkan akses hanya untuk role admin.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->user()->role === 'admin') {
            return $next($request);
        }

        session()->flash('error', 'Akses ditolak. Fitur ini hanya untuk Admin.');

        return redirect()->route('dashboard');
    }
}
