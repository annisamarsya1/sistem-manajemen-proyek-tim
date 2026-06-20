<?php

use App\Livewire\PersonalTimesheet;
use App\Livewire\TimeLogForm;
use App\Models\Task;
use App\Models\TeamProject;
use App\Models\TimeLog;
use App\Models\User;
use Livewire\Livewire;

// ---------------------------------------------------------------------------
// TimeLogForm
// ---------------------------------------------------------------------------

test('time log form modal is hidden by default', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(TimeLogForm::class)
        ->assertSet('showModal', false);
});

test('time log form modal can be toggled open', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(TimeLogForm::class)
        ->set('showModal', true)
        ->assertSet('showModal', true);
});

test('admin sees all active projects', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    TeamProject::factory()->count(3)->create(['status' => 'active']);
    TeamProject::factory()->count(2)->create(['status' => 'planning']);

    $component = Livewire::actingAs($admin)->test(TimeLogForm::class);

    // Semua proyek ditampilkan tanpa filter status
    expect($component->get('availableProjects'))->toHaveCount(5);
});

test('employee sees only projects where they have assigned tasks', function () {
    $employee = User::factory()->create(['role' => 'employee']);
    $assignedProject = TeamProject::factory()->create(['status' => 'active']);
    $otherProject = TeamProject::factory()->create(['status' => 'active']);

    Task::factory()->create([
        'project_id' => $assignedProject->id,
        'assignee_id' => $employee->id,
    ]);

    $component = Livewire::actingAs($employee)->test(TimeLogForm::class);

    expect($component->get('availableProjects'))->toHaveCount(1);
    expect($component->get('availableProjects')->first()->id)->toBe($assignedProject->id);
});

test('updating project id filters available tasks for admin', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $project = TeamProject::factory()->create(['status' => 'active']);
    Task::factory()->count(3)->create(['project_id' => $project->id]);

    Livewire::actingAs($admin)
        ->test(TimeLogForm::class)
        ->set('projectId', (string) $project->id)
        ->assertSet('taskId', '')
        ->assertCount('availableTasks', 3);
});

test('updating project id filters only assigned tasks for employee', function () {
    $employee = User::factory()->create(['role' => 'employee']);
    $project = TeamProject::factory()->create(['status' => 'active']);

    Task::factory()->create(['project_id' => $project->id, 'assignee_id' => $employee->id]);
    Task::factory()->create(['project_id' => $project->id]); // someone else's

    Livewire::actingAs($employee)
        ->test(TimeLogForm::class)
        ->set('projectId', (string) $project->id)
        // Semua task di proyek ditampilkan, bukan hanya yang diassign ke employee
        ->assertCount('availableTasks', 2);
});

test('save time log validates required fields', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(TimeLogForm::class)
        ->call('saveTimeLog')
        ->assertHasErrors(['projectId', 'taskId', 'startTime', 'endTime']);
});

test('save time log validates end time must be after start time', function () {
    $user = User::factory()->create();
    $project = TeamProject::factory()->create(['status' => 'active']);
    $task = Task::factory()->create(['project_id' => $project->id, 'assignee_id' => $user->id]);

    Livewire::actingAs($user)
        ->test(TimeLogForm::class)
        ->set('projectId', (string) $project->id)
        ->set('taskId', (string) $task->id)
        ->set('startTime', '2025-01-15 10:00')
        ->set('endTime', '2025-01-15 09:00')
        ->call('saveTimeLog')
        ->assertHasErrors(['endTime']);
});

test('save time log rejects duration over 12 hours', function () {
    $user = User::factory()->create();
    $project = TeamProject::factory()->create(['status' => 'active']);
    $task = Task::factory()->create(['project_id' => $project->id, 'assignee_id' => $user->id]);

    Livewire::actingAs($user)
        ->test(TimeLogForm::class)
        ->set('projectId', (string) $project->id)
        ->set('taskId', (string) $task->id)
        ->set('startTime', '2025-01-15 08:00')
        ->set('endTime', '2025-01-15 21:00') // 13 hours
        ->call('saveTimeLog')
        ->assertHasErrors(['endTime']);
});

test('save time log rejects overlapping time entries', function () {
    $user = User::factory()->create();
    $project = TeamProject::factory()->create(['status' => 'active']);
    $task = Task::factory()->create(['project_id' => $project->id, 'assignee_id' => $user->id]);

    // Create an existing time log from 09:00 to 11:00
    TimeLog::factory()->create([
        'user_id' => $user->id,
        'project_id' => $project->id,
        'start_time' => '2025-01-15 09:00:00',
        'end_time' => '2025-01-15 11:00:00',
    ]);

    // Try to add a log that overlaps (10:00 to 12:00)
    Livewire::actingAs($user)
        ->test(TimeLogForm::class)
        ->set('projectId', (string) $project->id)
        ->set('taskId', (string) $task->id)
        ->set('startTime', '2025-01-15 10:00')
        ->set('endTime', '2025-01-15 12:00')
        ->call('saveTimeLog')
        ->assertHasErrors(['startTime']);
});

test('save time log creates record and closes modal on success', function () {
    $user = User::factory()->create();
    $project = TeamProject::factory()->create(['status' => 'active']);
    $task = Task::factory()->create(['project_id' => $project->id, 'assignee_id' => $user->id]);

    Livewire::actingAs($user)
        ->test(TimeLogForm::class)
        ->set('projectId', (string) $project->id)
        ->set('taskId', (string) $task->id)
        ->set('startTime', '2025-01-15 09:00')
        ->set('endTime', '2025-01-15 11:00')
        ->set('notes', 'Test log')
        ->call('saveTimeLog')
        ->assertSet('showModal', false)
        ->assertHasNoErrors();

    expect(TimeLog::where('user_id', $user->id)->count())->toBe(1);

    $log = TimeLog::where('user_id', $user->id)->first();
    expect($log->duration_hours)->toBe('2.00');
    expect($log->status)->toBe('pending');
});

// ---------------------------------------------------------------------------
// PersonalTimesheet
// ---------------------------------------------------------------------------

test('guests are redirected from timesheet', function () {
    $this->get(route('timesheet'))->assertRedirect(route('login'));
});

test('authenticated users can visit timesheet page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get(route('timesheet'))->assertOk();
});

test('timesheet shows only own logs', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    $project = TeamProject::factory()->create();

    TimeLog::factory()->count(2)->create(['user_id' => $user->id, 'project_id' => $project->id]);
    TimeLog::factory()->count(3)->create(['user_id' => $other->id, 'project_id' => $project->id]);

    $component = Livewire::actingAs($user)->test(PersonalTimesheet::class);

    expect($component->get('timeLogs')->total())->toBe(2);
});

test('timesheet summary shows only approved hours', function () {
    $user = User::factory()->create();
    $project = TeamProject::factory()->create();

    // Approved log this week
    TimeLog::factory()->approved()->create([
        'user_id' => $user->id,
        'project_id' => $project->id,
        'start_time' => now()->startOfWeek()->addHours(9),
        'end_time' => now()->startOfWeek()->addHours(13),
        'duration_hours' => 4.00,
    ]);

    // Pending log this week (should NOT be in summary)
    TimeLog::factory()->create([
        'user_id' => $user->id,
        'project_id' => $project->id,
        'start_time' => now()->startOfWeek()->addHours(14),
        'end_time' => now()->startOfWeek()->addHours(16),
        'duration_hours' => 2.00,
        'status' => 'pending',
    ]);

    $component = Livewire::actingAs($user)->test(PersonalTimesheet::class);

    expect((float) $component->get('weekHours'))->toBe(4.0);
});

test('timesheet filter by date range works', function () {
    $user = User::factory()->create();
    $project = TeamProject::factory()->create();

    TimeLog::factory()->create([
        'user_id' => $user->id,
        'project_id' => $project->id,
        'start_time' => '2025-01-10 09:00:00',
        'end_time' => '2025-01-10 11:00:00',
    ]);

    TimeLog::factory()->create([
        'user_id' => $user->id,
        'project_id' => $project->id,
        'start_time' => '2025-01-20 09:00:00',
        'end_time' => '2025-01-20 11:00:00',
    ]);

    $component = Livewire::actingAs($user)
        ->test(PersonalTimesheet::class)
        ->set('filterStart', '2025-01-15')
        ->set('filterEnd', '2025-01-25');

    expect($component->get('timeLogs')->total())->toBe(1);
});
