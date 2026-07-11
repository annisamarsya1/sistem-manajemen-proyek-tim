<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

/**
 * Livewire Component: Login
 * 
 * Menangani logika halaman login otentikasi.
 * Menggunakan kredensial email dan password serta memverifikasi 
 * apakah akun dalam status aktif (is_active).
 */
class Login extends Component
{
    public string $email = '';
    public string $password = '';

    /**
     * Mengeksekusi proses login ketika form disubmit.
     */
    public function login(): void
    {
        $this->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'min:6'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
        ]);

        // Cek credentials + is_active sekaligus
        if (Auth::attempt(['email' => $this->email, 'password' => $this->password, 'is_active' => true])) {
            session()->regenerate();
            $this->redirect(route('dashboard'));
            return;
        }

        // Jika gagal, cek apakah user ada tapi is_active = false
        $user = \App\Models\User::where('email', $this->email)->first();

        if ($user && !$user->is_active) {
            $this->addError('email', 'Akun Anda telah dinonaktifkan. Hubungi administrator.');
            return;
        }

        // Credentials salah (email atau password tidak cocok)
        $this->addError('email', 'Email atau password salah.');
    }

    public function render()
    {
        return view('livewire.auth.login')
            ->layout('layouts.guest')
            ->title('Login');
    }
}
