<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Class AuthController
 * 
 * Menangani proses otentikasi pengguna, saat ini difokuskan pada fitur logout.
 * Untuk login, logika ditangani melalui Livewire component.
 */
class AuthController extends Controller
{
    /**
     * Logout user, invalidate session, dan redirect ke login.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
