<?php

use App\Livewire\Auth\Login;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

test('login screen can be rendered', function () {
    $response = $this->get(route('login'));
    $response->assertOk();
});

test('users can authenticate using livewire login', function () {
    $user = User::factory()->create([
        'email' => 'karyawan@perusahaan.com',
        'password' => Hash::make('secret-password'),
        'is_active' => true,
    ]);

    Livewire::test(Login::class)
        ->set('email', 'karyawan@perusahaan.com')
        ->set('password', 'secret-password')
        ->call('login')
        ->assertRedirect(route('dashboard'));

    $this->assertAuthenticatedAs($user);
});

test('users can not authenticate with invalid password', function () {
    User::factory()->create([
        'email' => 'karyawan@perusahaan.com',
        'password' => Hash::make('secret-password'),
        'is_active' => true,
    ]);

    Livewire::test(Login::class)
        ->set('email', 'karyawan@perusahaan.com')
        ->set('password', 'wrong-password')
        ->call('login')
        ->assertHasErrors(['email' => 'Email atau password salah.']);

    $this->assertGuest();
});

test('users with inactive account can not authenticate', function () {
    User::factory()->create([
        'email' => 'nonaktif@perusahaan.com',
        'password' => Hash::make('secret-password'),
        'is_active' => false,
    ]);

    Livewire::test(Login::class)
        ->set('email', 'nonaktif@perusahaan.com')
        ->set('password', 'secret-password')
        ->call('login')
        ->assertHasErrors(['email' => 'Akun Anda telah dinonaktifkan. Hubungi administrator.']);

    $this->assertGuest();
});

test('users can logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('logout'));

    $response->assertRedirect(route('login'));
    $this->assertGuest();
});

test('admin only middleware allows admin to access users list', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $response = $this->actingAs($admin)->get(route('users'));
    $response->assertOk();
});

test('admin only middleware redirects employee to dashboard with error', function () {
    $employee = User::factory()->create(['role' => 'employee']);
    $response = $this->actingAs($employee)->get(route('users'));

    $response->assertRedirect(route('dashboard'));
    $response->assertSessionHas('error', 'Akses ditolak. Fitur ini hanya untuk Admin.');
});

test('admin or pm middleware allows project manager to access projects', function () {
    $pm = User::factory()->create(['role' => 'project_manager']);
    $response = $this->actingAs($pm)->get(route('projects'));
    $response->assertOk();
});

test('admin or pm middleware redirects employee to dashboard with error', function () {
    $employee = User::factory()->create(['role' => 'employee']);
    $response = $this->actingAs($employee)->get(route('projects'));

    $response->assertRedirect(route('dashboard'));
    $response->assertSessionHas('error', 'Akses ditolak. Fitur ini hanya untuk Admin dan Project Manager.');
});
