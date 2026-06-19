<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Log in')]
class Login extends Component
{
    /**
     * The email input field.
     */
    public string $email = '';

    /**
     * The password input field.
     */
    public string $password = '';

    /**
     * Validation rules for the component.
     *
     * @var array<string, string>
     */
    protected array $rules = [
        'email' => 'required|email',
        'password' => 'required|min:6',
    ];

    /**
     * Authenticate the user.
     */
    public function login(): mixed
    {
        $this->validate();

        $user = User::where('email', $this->email)->first();

        // Check if user exists, password matches, but account is inactive
        if ($user && Hash::check($this->password, $user->password) && ! $user->is_active) {
            $this->addError('email', 'Akun Anda telah dinonaktifkan. Hubungi administrator.');

            return null;
        }

        // Attempt login using session-based authentication
        if (Auth::attempt(['email' => $this->email, 'password' => $this->password, 'is_active' => true])) {
            session()->regenerate();

            return redirect()->route('dashboard');
        }

        // Standard authentication failure
        $this->addError('email', 'Email atau password salah.');

        return null;
    }

    /**
     * Render the component view.
     */
    public function render(): View
    {
        return view('auth.login')->layout('layouts.auth');
    }
}
