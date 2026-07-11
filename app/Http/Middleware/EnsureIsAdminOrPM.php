<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class EnsureIsAdminOrPM
 * 
 * Middleware untuk membatasi akses route hanya kepada pengguna
 * dengan role 'admin' atau 'project_manager'. Pengguna lain (misal: employee) 
 * akan diarahkan kembali ke dashboard dengan pesan error.
 */
class EnsureIsAdminOrPM
{
    /**
     * Izinkan akses hanya untuk role admin atau project_manager.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $role = auth()->user()->role;

        if ($role === 'admin' || $role === 'project_manager') {
            return $next($request);
        }

        session()->flash('error', 'Akses ditolak. Fitur ini hanya untuk Admin dan Project Manager.');

        return redirect()->route('dashboard');
    }
}
