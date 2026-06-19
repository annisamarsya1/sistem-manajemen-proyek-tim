<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureIsAdminOrPM
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && (auth()->user()->role === 'admin' || auth()->user()->role === 'project_manager')) {
            return $next($request);
        }

        session()->flash('error', 'Akses ditolak. Fitur ini hanya untuk Admin dan Project Manager.');

        return redirect()->route('dashboard');
    }
}
