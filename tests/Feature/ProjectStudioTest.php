<?php

use App\Livewire\ProjectStudio;
use App\Models\TeamProject;
use App\Models\User;
use Livewire\Livewire;

// ---------------------------------------------------------------------------
// Access control
// ---------------------------------------------------------------------------

test('guest is redirected from projects page', function () {
    $this->get(route('projects'))->assertRedirect(route('login'));
});

test('employee is redirected from projects page', function () {
    $employee = User::factory()->create(['role' => 'employee']);

    $this->actingAs($employee)->get(route('projects'))->assertRedirect(route('dashboard'));
});

test('admin can access projects page', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)->get(route('projects'))->assertOk();
});

test('project manager can access projects page', function () {
    $pm = User::factory()->create(['role' => 'project_manager']);

    $this->actingAs($pm)->get(route('projects'))->assertOk();
});

// ---------------------------------------------------------------------------
// List & filter
// ---------------------------------------------------------------------------

test('projects are listed in the table', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    TeamProject::factory()->count(3)->create();

    Livewire::actingAs($admin)
        ->test(ProjectStudio::class)
        ->assertSee(TeamProject::first()->title);
});

test('filter by status shows only matching projects', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    TeamProject::factory()->create(['status' => 'active', 'title' => 'Proyek Aktif']);
    TeamProject::factory()->create(['status' => 'planning', 'title' => 'Proyek Planning']);

    Livewire::actingAs($admin)
        ->test(ProjectStudio::class)
        ->set('filterStatus', 'active')
        ->assertSee('Proyek Aktif')
        ->assertDontSee('Proyek Planning');
});

test('filter by priority shows only matching projects', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    TeamProject::factory()->create(['priority' => 'urgent', 'title' => 'Proyek Urgent']);
    TeamProject::factory()->create(['priority' => 'low', 'title' => 'Proyek Low Priority']);

    Livewire::actingAs($admin)
        ->test(ProjectStudio::class)
        ->set('filterPriority', 'urgent')
        ->assertSee('Proyek Urgent')
        ->assertDontSee('Proyek Low Priority');
});

// ---------------------------------------------------------------------------
// Create
// ---------------------------------------------------------------------------

test('admin can create a project', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    Livewire::actingAs($admin)
        ->test(ProjectStudio::class)
        ->set('title', 'Proyek Baru Test')
        ->set('deadline', now()->addMonth()->format('Y-m-d'))
        ->set('priority', 'high')
        ->set('status', 'active')
        ->call('save')
        ->assertHasNoErrors();

    expect(TeamProject::where('title', 'Proyek Baru Test')->exists())->toBeTrue();
});

test('create sets created_by to current user', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    Livewire::actingAs($admin)
        ->test(ProjectStudio::class)
        ->set('title', 'Proyek Creator Test')
        ->set('deadline', now()->addMonth()->format('Y-m-d'))
        ->call('save');

    $project = TeamProject::where('title', 'Proyek Creator Test')->first();
    expect($project->created_by)->toBe($admin->id);
});

test('create validates required fields', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    Livewire::actingAs($admin)
        ->test(ProjectStudio::class)
        ->set('title', '')
        ->set('deadline', '')
        ->call('save')
        ->assertHasErrors(['title' => 'required', 'deadline' => 'required']);
});

test('create validates deadline after start date', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    Livewire::actingAs($admin)
        ->test(ProjectStudio::class)
        ->set('title', 'Test Project')
        ->set('startDate', now()->addMonth()->format('Y-m-d'))
        ->set('deadline', now()->format('Y-m-d'))
        ->call('save')
        ->assertHasErrors(['deadline']);
});

test('employee cannot create project', function () {
    $employee = User::factory()->create(['role' => 'employee']);

    Livewire::actingAs($employee)
        ->test(ProjectStudio::class)
        ->set('title', 'Proyek Employee')
        ->set('deadline', now()->addMonth()->format('Y-m-d'))
        ->call('save')
        ->assertForbidden();
});

test('modal is closed and form reset after successful create', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $component = Livewire::actingAs($admin)
        ->test(ProjectStudio::class)
        ->set('title', 'Proyek Reset Test')
        ->set('deadline', now()->addMonth()->format('Y-m-d'))
        ->call('save');

    expect($component->get('showModal'))->toBeFalse()
        ->and($component->get('title'))->toBe('');
});

// ---------------------------------------------------------------------------
// Edit
// ---------------------------------------------------------------------------

test('editProject loads project data into form', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $project = TeamProject::factory()->create(['title' => 'Proyek Edit Test']);

    $component = Livewire::actingAs($admin)
        ->test(ProjectStudio::class)
        ->call('editProject', $project->id);

    expect($component->get('title'))->toBe('Proyek Edit Test')
        ->and($component->get('editingId'))->toBe($project->id)
        ->and($component->get('showModal'))->toBeTrue();
});

test('admin can update a project', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $project = TeamProject::factory()->create(['title' => 'Judul Lama']);

    Livewire::actingAs($admin)
        ->test(ProjectStudio::class)
        ->call('editProject', $project->id)
        ->set('title', 'Judul Baru')
        ->call('update')
        ->assertHasNoErrors();

    expect($project->fresh()->title)->toBe('Judul Baru');
});

test('employee cannot update project', function () {
    $employee = User::factory()->create(['role' => 'employee']);
    $project = TeamProject::factory()->create();

    Livewire::actingAs($employee)
        ->test(ProjectStudio::class)
        ->set('editingId', $project->id)
        ->set('title', 'Coba Edit')
        ->set('deadline', now()->addMonth()->format('Y-m-d'))
        ->call('update')
        ->assertForbidden();
});

// ---------------------------------------------------------------------------
// Delete
// ---------------------------------------------------------------------------

test('admin can delete a project', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $project = TeamProject::factory()->create();

    Livewire::actingAs($admin)
        ->test(ProjectStudio::class)
        ->call('deleteProject', $project->id)
        ->assertHasNoErrors();

    expect(TeamProject::find($project->id))->toBeNull();
});

test('employee cannot delete project', function () {
    $employee = User::factory()->create(['role' => 'employee']);
    $project = TeamProject::factory()->create();

    Livewire::actingAs($employee)
        ->test(ProjectStudio::class)
        ->call('deleteProject', $project->id)
        ->assertForbidden();

    expect(TeamProject::find($project->id))->not->toBeNull();
});
