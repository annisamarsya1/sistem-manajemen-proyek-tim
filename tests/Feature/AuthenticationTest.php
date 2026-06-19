<?php

use App\Livewire\Auth\Login;
use App\Models\User;
use Livewire\Livewire;

test('halaman login dapat diakses oleh guest', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});

test('login berhasil dengan kredensial valid dan akun aktif', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => bcrypt('password123'),
        'role' => 'employee',
        'is_active' => true,
    ]);

    Livewire::test(Login::class)
        ->set('email', 'test@example.com')
        ->set('password', 'password123')
        ->call('login')
        ->assertRedirect(route('dashboard'));

    $this->assertAuthenticated();
});

test('login gagal dengan password salah', function () {
    User::factory()->create([
        'email' => 'test@example.com',
        'password' => bcrypt('password123'),
        'is_active' => true,
    ]);

    Livewire::test(Login::class)
        ->set('email', 'test@example.com')
        ->set('password', 'wrongpassword')
        ->call('login')
        ->assertHasErrors(['email']);

    $this->assertGuest();
});

test('login gagal jika akun dinonaktifkan', function () {
    User::factory()->create([
        'email' => 'inactive@example.com',
        'password' => bcrypt('password123'),
        'is_active' => false,
    ]);

    Livewire::test(Login::class)
        ->set('email', 'inactive@example.com')
        ->set('password', 'password123')
        ->call('login')
        ->assertHasErrors(['email']);

    $this->assertGuest();
});

test('pesan error spesifik untuk akun nonaktif', function () {
    User::factory()->create([
        'email' => 'inactive@example.com',
        'password' => bcrypt('password123'),
        'is_active' => false,
    ]);

    $component = Livewire::test(Login::class)
        ->set('email', 'inactive@example.com')
        ->set('password', 'password123')
        ->call('login');

    $component->assertHasErrors(['email']);

    expect($component->errors()->first('email'))
        ->toContain('dinonaktifkan');
});

test('validasi email wajib diisi', function () {
    Livewire::test(Login::class)
        ->set('email', '')
        ->set('password', 'password123')
        ->call('login')
        ->assertHasErrors(['email' => 'required']);
});

test('validasi password minimal 6 karakter', function () {
    Livewire::test(Login::class)
        ->set('email', 'test@example.com')
        ->set('password', '123')
        ->call('login')
        ->assertHasErrors(['password' => 'min']);
});

test('user yang sudah login di-redirect dari halaman login', function () {
    $user = User::factory()->create(['is_active' => true]);

    $this->actingAs($user)
        ->get('/login')
        ->assertRedirect('/dashboard');
});

test('logout berhasil dan redirect ke login', function () {
    $user = User::factory()->create(['is_active' => true]);

    $this->actingAs($user)
        ->post(route('logout'))
        ->assertRedirect(route('login'));

    $this->assertGuest();
});

test('middleware admin_or_pm memblokir employee', function () {
    $employee = User::factory()->create(['role' => 'employee', 'is_active' => true]);

    $this->actingAs($employee)
        ->get('/projects')
        ->assertRedirect('/dashboard');
});

test('middleware admin_or_pm mengizinkan admin', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

    $this->actingAs($admin)
        ->get('/projects')
        ->assertStatus(200);
});

test('middleware admin_or_pm mengizinkan project manager', function () {
    $pm = User::factory()->create(['role' => 'project_manager', 'is_active' => true]);

    $this->actingAs($pm)
        ->get('/projects')
        ->assertStatus(200);
});

test('middleware admin_only memblokir project manager', function () {
    $pm = User::factory()->create(['role' => 'project_manager', 'is_active' => true]);

    $this->actingAs($pm)
        ->get('/users')
        ->assertRedirect('/dashboard');
});

test('middleware admin_only mengizinkan admin', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

    $this->actingAs($admin)
        ->get('/users')
        ->assertStatus(200);
});
