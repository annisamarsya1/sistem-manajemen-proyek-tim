<?php

use App\Livewire\UserManagement;
use App\Models\User;
use Livewire\Livewire;

// ---------------------------------------------------------------------------
// Akses halaman
// ---------------------------------------------------------------------------

test('guest tidak bisa akses halaman users', function () {
    $this->get(route('users'))->assertRedirect(route('login'));
});

test('employee tidak bisa akses halaman users', function () {
    $employee = User::factory()->create(['role' => 'employee']);

    $this->actingAs($employee)->get(route('users'))->assertRedirect(route('dashboard'));
});

test('project_manager tidak bisa akses halaman users', function () {
    $pm = User::factory()->create(['role' => 'project_manager']);

    $this->actingAs($pm)->get(route('users'))->assertRedirect(route('dashboard'));
});

test('admin bisa akses halaman users', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)->get(route('users'))->assertOk();
});

// ---------------------------------------------------------------------------
// Daftar & pencarian
// ---------------------------------------------------------------------------

test('halaman menampilkan daftar pengguna', function () {
    $admin = User::factory()->create(['role' => 'admin', 'name' => 'Admin User']);
    User::factory()->create(['name' => 'Budi Santoso']);

    Livewire::actingAs($admin)
        ->test(UserManagement::class)
        ->assertSee('Budi Santoso');
});

test('pencarian memfilter pengguna berdasarkan nama', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    User::factory()->create(['name' => 'Budi Santoso']);
    User::factory()->create(['name' => 'Citra Dewi']);

    Livewire::actingAs($admin)
        ->test(UserManagement::class)
        ->set('search', 'Budi')
        ->assertSee('Budi Santoso')
        ->assertDontSee('Citra Dewi');
});

test('pencarian memfilter pengguna berdasarkan email', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    User::factory()->create(['email' => 'budi@contoh.com', 'name' => 'Budi']);
    User::factory()->create(['email' => 'citra@contoh.com', 'name' => 'Citra']);

    Livewire::actingAs($admin)
        ->test(UserManagement::class)
        ->set('search', 'budi@contoh')
        ->assertSee('budi@contoh.com')
        ->assertDontSee('citra@contoh.com');
});

// ---------------------------------------------------------------------------
// Buat pengguna baru
// ---------------------------------------------------------------------------

test('admin bisa membuat pengguna baru', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    Livewire::actingAs($admin)
        ->test(UserManagement::class)
        ->set('name', 'Pengguna Baru')
        ->set('email', 'baru@contoh.com')
        ->set('password', 'rahasia123')
        ->set('passwordConfirmation', 'rahasia123')
        ->set('role', 'employee')
        ->call('save')
        ->assertHasNoErrors()
        ->assertSet('showModal', false);

    expect(User::where('email', 'baru@contoh.com')->exists())->toBeTrue();
});

test('validasi gagal jika email sudah ada saat membuat pengguna', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    User::factory()->create(['email' => 'ada@contoh.com']);

    Livewire::actingAs($admin)
        ->test(UserManagement::class)
        ->set('name', 'Test')
        ->set('email', 'ada@contoh.com')
        ->set('password', 'rahasia123')
        ->set('passwordConfirmation', 'rahasia123')
        ->set('role', 'employee')
        ->call('save')
        ->assertHasErrors(['email']);
});

test('validasi gagal jika password tidak cocok saat membuat pengguna', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    Livewire::actingAs($admin)
        ->test(UserManagement::class)
        ->set('name', 'Test')
        ->set('email', 'test@contoh.com')
        ->set('password', 'rahasia123')
        ->set('passwordConfirmation', 'berbeda456')
        ->set('role', 'employee')
        ->call('save')
        ->assertHasErrors(['password']);
});

// ---------------------------------------------------------------------------
// Edit pengguna
// ---------------------------------------------------------------------------

test('admin bisa mengedit pengguna', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create(['name' => 'Nama Lama']);

    Livewire::actingAs($admin)
        ->test(UserManagement::class)
        ->call('editUser', $user->id)
        ->assertSet('editingId', $user->id)
        ->assertSet('name', 'Nama Lama')
        ->assertSet('showModal', true)
        ->set('name', 'Nama Baru')
        ->call('update')
        ->assertHasNoErrors()
        ->assertSet('showModal', false);

    expect($user->fresh()->name)->toBe('Nama Baru');
});

test('password tidak diubah jika kosong saat edit', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create();
    $hashLama = $user->password;

    Livewire::actingAs($admin)
        ->test(UserManagement::class)
        ->call('editUser', $user->id)
        ->set('password', '')
        ->set('passwordConfirmation', '')
        ->call('update')
        ->assertHasNoErrors();

    expect($user->fresh()->password)->toBe($hashLama);
});

test('validasi email unik mengabaikan pengguna yang sedang diedit', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create(['email' => 'sama@contoh.com']);

    Livewire::actingAs($admin)
        ->test(UserManagement::class)
        ->call('editUser', $user->id)
        ->set('email', 'sama@contoh.com')
        ->call('update')
        ->assertHasNoErrors(['email']);
});

// ---------------------------------------------------------------------------
// Toggle aktif / nonaktif
// ---------------------------------------------------------------------------

test('admin bisa menonaktifkan pengguna lain', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create(['is_active' => true]);

    Livewire::actingAs($admin)
        ->test(UserManagement::class)
        ->call('toggleActive', $user->id);

    expect($user->fresh()->is_active)->toBeFalse();
});

test('admin bisa mengaktifkan kembali pengguna nonaktif', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create(['is_active' => false]);

    Livewire::actingAs($admin)
        ->test(UserManagement::class)
        ->call('toggleActive', $user->id);

    expect($user->fresh()->is_active)->toBeTrue();
});

test('admin tidak bisa menonaktifkan akunnya sendiri', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

    Livewire::actingAs($admin)
        ->test(UserManagement::class)
        ->call('toggleActive', $admin->id);

    expect($admin->fresh()->is_active)->toBeTrue();
});

// ---------------------------------------------------------------------------
// Otorisasi: non-admin tidak bisa memanggil method
// ---------------------------------------------------------------------------

test('employee mendapat 403 jika memanggil save', function () {
    $employee = User::factory()->create(['role' => 'employee']);

    Livewire::actingAs($employee)
        ->test(UserManagement::class)
        ->call('save')
        ->assertForbidden();
});

test('employee mendapat 403 jika memanggil toggleActive', function () {
    $employee = User::factory()->create(['role' => 'employee']);
    $target = User::factory()->create();

    Livewire::actingAs($employee)
        ->test(UserManagement::class)
        ->call('toggleActive', $target->id)
        ->assertForbidden();
});
