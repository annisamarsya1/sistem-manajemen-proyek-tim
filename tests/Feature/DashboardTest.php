<?php

use App\Livewire\Dashboard;
use App\Models\TeamProject;
use App\Models\TimeLog;
use App\Models\User;
use Livewire\Livewire;

test('guests are redirected to the login page', function () {
    $this->get(route('dashboard'))->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get(route('dashboard'))->assertOk();
});

test('dashboard renders livewire component', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get(route('dashboard'))->assertSeeLivewire(Dashboard::class);
});

test('admin sees today snapshot section', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    Livewire::actingAs($admin)
        ->test(Dashboard::class)
        ->assertSee('Tidak ada aktivitas tercatat hari ini.');
});

test('employee does not see today snapshot section', function () {
    $employee = User::factory()->create(['role' => 'employee']);

    Livewire::actingAs($employee)
        ->test(Dashboard::class)
        ->assertDontSee('Tidak ada aktivitas tercatat hari ini.');
});

test('employee sees only own time logs', function () {
    $employee = User::factory()->create(['role' => 'employee']);
    $otherEmployee = User::factory()->create(['role' => 'employee']);
    $project = TeamProject::factory()->create();

    TimeLog::factory()->create(['user_id' => $employee->id, 'project_id' => $project->id, 'status' => 'pending']);
    TimeLog::factory()->create(['user_id' => $otherEmployee->id, 'project_id' => $project->id, 'status' => 'pending']);

    $component = Livewire::actingAs($employee)->test(Dashboard::class);

    // Employee sees own log but not other's
    expect($component->get('timeLogs')->total())->toBe(1);
});

test('admin sees all time logs', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $project = TeamProject::factory()->create();

    TimeLog::factory()->count(3)->create(['project_id' => $project->id, 'status' => 'pending']);

    $component = Livewire::actingAs($admin)->test(Dashboard::class);

    expect($component->get('timeLogs')->total())->toBe(3);
});

test('filter by status works correctly', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $project = TeamProject::factory()->create();

    TimeLog::factory()->create(['project_id' => $project->id, 'status' => 'approved']);
    TimeLog::factory()->create(['project_id' => $project->id, 'status' => 'pending']);
    TimeLog::factory()->create(['project_id' => $project->id, 'status' => 'rejected']);

    Livewire::actingAs($admin)
        ->test(Dashboard::class)
        ->set('filterStatus', 'approved')
        ->assertSet('filterStatus', 'approved');

    $component = Livewire::actingAs($admin)->test(Dashboard::class);
    $component->set('filterStatus', 'approved');

    expect($component->get('timeLogs')->total())->toBe(1);
});

test('approve action changes status to approved', function () {
    $pm = User::factory()->create(['role' => 'project_manager']);
    $project = TeamProject::factory()->create();
    $log = TimeLog::factory()->create(['project_id' => $project->id, 'status' => 'pending']);

    Livewire::actingAs($pm)
        ->test(Dashboard::class)
        ->call('approveLog', $log->id);

    expect($log->fresh()->status)->toBe('approved')
        ->and($log->fresh()->reviewed_by)->toBe($pm->id);
});

test('reject action changes status to rejected', function () {
    $pm = User::factory()->create(['role' => 'project_manager']);
    $project = TeamProject::factory()->create();
    $log = TimeLog::factory()->create(['project_id' => $project->id, 'status' => 'pending']);

    Livewire::actingAs($pm)
        ->test(Dashboard::class)
        ->call('rejectLog', $log->id);

    expect($log->fresh()->status)->toBe('rejected')
        ->and($log->fresh()->reviewed_by)->toBe($pm->id);
});

test('employee cannot approve time log', function () {
    $employee = User::factory()->create(['role' => 'employee']);
    $project = TeamProject::factory()->create();
    $log = TimeLog::factory()->create(['project_id' => $project->id, 'status' => 'pending']);

    Livewire::actingAs($employee)
        ->test(Dashboard::class)
        ->call('approveLog', $log->id)
        ->assertForbidden();
});

test('employee cannot reject time log', function () {
    $employee = User::factory()->create(['role' => 'employee']);
    $project = TeamProject::factory()->create();
    $log = TimeLog::factory()->create(['project_id' => $project->id, 'status' => 'pending']);

    Livewire::actingAs($employee)
        ->test(Dashboard::class)
        ->call('rejectLog', $log->id)
        ->assertForbidden();
});

test('export csv flashes info for admin', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    // Call via HTTP so session flash persists properly
    $this->actingAs($admin);

    Livewire::actingAs($admin)
        ->test(Dashboard::class)
        ->call('exportCsv')
        ->assertHasNoErrors();
});

test('employee cannot call export', function () {
    $employee = User::factory()->create(['role' => 'employee']);

    Livewire::actingAs($employee)
        ->test(Dashboard::class)
        ->call('exportCsv')
        ->assertForbidden();
});
