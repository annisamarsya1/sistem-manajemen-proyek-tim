<?php

use App\Livewire\KanbanBoard;
use App\Models\Task;
use App\Models\TaskComment;
use App\Models\TeamProject;
use App\Models\User;
use Livewire\Livewire;

// ---------------------------------------------------------------------------
// Helpers
// ---------------------------------------------------------------------------

function adminUser(): User
{
    return User::factory()->create(['role' => 'admin', 'is_active' => true]);
}

function pmUser(): User
{
    return User::factory()->create(['role' => 'project_manager', 'is_active' => true]);
}

function employeeUser(): User
{
    return User::factory()->create(['role' => 'employee', 'is_active' => true]);
}

// ---------------------------------------------------------------------------
// Access
// ---------------------------------------------------------------------------

it('redirects guests to login', function () {
    $this->get(route('tasks'))->assertRedirect(route('login'));
});

it('renders kanban board for authenticated users', function () {
    $this->actingAs(adminUser())
        ->get(route('tasks'))
        ->assertOk()
        ->assertSeeLivewire(KanbanBoard::class);
});

// ---------------------------------------------------------------------------
// Kanban board shows tasks
// ---------------------------------------------------------------------------

it('shows tasks grouped by status', function () {
    $admin = adminUser();
    $project = TeamProject::factory()->create();

    $todoTask = Task::factory()->create(['project_id' => $project->id, 'status' => 'todo']);
    $doneTask = Task::factory()->create(['project_id' => $project->id, 'status' => 'done']);

    Livewire::actingAs($admin)
        ->test(KanbanBoard::class)
        ->assertSee($todoTask->title)
        ->assertSee($doneTask->title);
});

it('employee only sees their own tasks', function () {
    $employee = employeeUser();
    $other = employeeUser();
    $project = TeamProject::factory()->create();

    $myTask = Task::factory()->create([
        'project_id' => $project->id,
        'assignee_id' => $employee->id,
        'status' => 'todo',
    ]);
    $otherTask = Task::factory()->create([
        'project_id' => $project->id,
        'assignee_id' => $other->id,
        'status' => 'todo',
    ]);

    Livewire::actingAs($employee)
        ->test(KanbanBoard::class)
        ->assertSee($myTask->title)
        ->assertDontSee($otherTask->title);
});

// ---------------------------------------------------------------------------
// Filter by project
// ---------------------------------------------------------------------------

it('filters tasks by project', function () {
    $admin = adminUser();
    $projectA = TeamProject::factory()->create();
    $projectB = TeamProject::factory()->create();

    $taskA = Task::factory()->create(['project_id' => $projectA->id, 'status' => 'todo']);
    $taskB = Task::factory()->create(['project_id' => $projectB->id, 'status' => 'todo']);

    Livewire::actingAs($admin)
        ->test(KanbanBoard::class)
        ->set('filterProjectId', (string) $projectA->id)
        ->assertSee($taskA->title)
        ->assertDontSee($taskB->title);
});

// ---------------------------------------------------------------------------
// Drag-and-drop: updateTaskStatus
// ---------------------------------------------------------------------------

it('admin can update task status via drag and drop', function () {
    $admin = adminUser();
    $task = Task::factory()->create(['status' => 'todo']);

    Livewire::actingAs($admin)
        ->test(KanbanBoard::class)
        ->call('updateTaskStatus', $task->id, 'in_progress');

    expect($task->fresh()->status)->toBe('in_progress');
});

it('employee can move their own task', function () {
    $employee = employeeUser();
    $task = Task::factory()->create(['status' => 'todo', 'assignee_id' => $employee->id]);

    Livewire::actingAs($employee)
        ->test(KanbanBoard::class)
        ->call('updateTaskStatus', $task->id, 'in_progress');

    expect($task->fresh()->status)->toBe('in_progress');
});

it('employee cannot move another users task', function () {
    $employee = employeeUser();
    $other = employeeUser();
    $task = Task::factory()->create(['status' => 'todo', 'assignee_id' => $other->id]);

    Livewire::actingAs($employee)
        ->test(KanbanBoard::class)
        ->call('updateTaskStatus', $task->id, 'in_progress');

    expect($task->fresh()->status)->toBe('todo');
});

it('sets completed_at when task moved to done', function () {
    $admin = adminUser();
    $task = Task::factory()->create(['status' => 'todo', 'completed_at' => null]);

    Livewire::actingAs($admin)
        ->test(KanbanBoard::class)
        ->call('updateTaskStatus', $task->id, 'done');

    expect($task->fresh()->completed_at)->not->toBeNull();
});

it('clears completed_at when task moved out of done', function () {
    $admin = adminUser();
    $task = Task::factory()->create(['status' => 'done', 'completed_at' => now()]);

    Livewire::actingAs($admin)
        ->test(KanbanBoard::class)
        ->call('updateTaskStatus', $task->id, 'in_progress');

    expect($task->fresh()->completed_at)->toBeNull();
});

// ---------------------------------------------------------------------------
// Create Task
// ---------------------------------------------------------------------------

it('admin can create a task', function () {
    $admin = adminUser();
    $project = TeamProject::factory()->create();
    $employee = employeeUser();

    Livewire::actingAs($admin)
        ->test(KanbanBoard::class)
        ->set('taskTitle', 'New Test Task')
        ->set('taskProjectId', (string) $project->id)
        ->set('taskAssigneeId', (string) $employee->id)
        ->set('taskPriority', 'high')
        ->set('taskDueDate', now()->addDays(7)->format('Y-m-d'))
        ->call('saveTask');

    $this->assertDatabaseHas('tasks', [
        'title' => 'New Test Task',
        'project_id' => $project->id,
        'assignee_id' => $employee->id,
        'priority' => 'high',
        'status' => 'todo',
    ]);
});

it('employee cannot create a task', function () {
    $employee = employeeUser();

    Livewire::actingAs($employee)
        ->test(KanbanBoard::class)
        ->call('openCreateModal')
        ->assertForbidden();
});

it('validates required fields on save task', function () {
    $admin = adminUser();

    Livewire::actingAs($admin)
        ->test(KanbanBoard::class)
        ->call('saveTask')
        ->assertHasErrors(['taskTitle', 'taskProjectId']);
});

// ---------------------------------------------------------------------------
// Edit / Update Task
// ---------------------------------------------------------------------------

it('admin can edit a task', function () {
    $admin = adminUser();
    $project = TeamProject::factory()->create();
    $task = Task::factory()->create(['project_id' => $project->id, 'status' => 'todo']);

    Livewire::actingAs($admin)
        ->test(KanbanBoard::class)
        ->call('editTask', $task->id)
        ->assertSet('editingTaskId', $task->id)
        ->assertSet('taskTitle', $task->title)
        ->set('taskTitle', 'Updated Title')
        ->set('taskStatus', 'in_progress')
        ->set('taskProgressPercent', 50)
        ->call('updateTask');

    expect($task->fresh()->title)->toBe('Updated Title')
        ->and($task->fresh()->status)->toBe('in_progress')
        ->and((int) $task->fresh()->progress_percent)->toBe(50);
});

it('employee cannot edit a task', function () {
    $employee = employeeUser();
    $task = Task::factory()->create();

    Livewire::actingAs($employee)
        ->test(KanbanBoard::class)
        ->call('editTask', $task->id)
        ->assertForbidden();
});

// ---------------------------------------------------------------------------
// Delete Task
// ---------------------------------------------------------------------------

it('admin can delete a task and its comments', function () {
    $admin = adminUser();
    $task = Task::factory()->create();
    TaskComment::factory()->create(['task_id' => $task->id]);

    Livewire::actingAs($admin)
        ->test(KanbanBoard::class)
        ->call('deleteTask', $task->id);

    $this->assertModelMissing($task);
    $this->assertDatabaseEmpty('task_comments');
});

it('employee cannot delete a task', function () {
    $employee = employeeUser();
    $task = Task::factory()->create();

    Livewire::actingAs($employee)
        ->test(KanbanBoard::class)
        ->call('deleteTask', $task->id)
        ->assertForbidden();
});

// ---------------------------------------------------------------------------
// Task Detail
// ---------------------------------------------------------------------------

it('opens task detail modal', function () {
    $admin = adminUser();
    $task = Task::factory()->create();

    Livewire::actingAs($admin)
        ->test(KanbanBoard::class)
        ->call('openTask', $task->id)
        ->assertSet('selectedTaskId', $task->id)
        ->assertSee($task->title);
});

it('employee cannot view a task assigned to someone else', function () {
    $employee = employeeUser();
    $task = Task::factory()->create(['assignee_id' => employeeUser()->id]);

    Livewire::actingAs($employee)
        ->test(KanbanBoard::class)
        ->call('openTask', $task->id)
        ->assertForbidden();
});

// ---------------------------------------------------------------------------
// Comments
// ---------------------------------------------------------------------------

it('authenticated user can add a comment', function () {
    $admin = adminUser();
    $task = Task::factory()->create();

    Livewire::actingAs($admin)
        ->test(KanbanBoard::class)
        ->call('openTask', $task->id)
        ->set('newComment', 'This is a test comment.')
        ->call('addComment');

    $this->assertDatabaseHas('task_comments', [
        'task_id' => $task->id,
        'user_id' => $admin->id,
        'comment' => 'This is a test comment.',
    ]);
});

it('clears comment input after submission', function () {
    $admin = adminUser();
    $task = Task::factory()->create();

    Livewire::actingAs($admin)
        ->test(KanbanBoard::class)
        ->call('openTask', $task->id)
        ->set('newComment', 'Hello comment')
        ->call('addComment')
        ->assertSet('newComment', '');
});

it('validates comment is not empty', function () {
    $admin = adminUser();
    $task = Task::factory()->create();

    Livewire::actingAs($admin)
        ->test(KanbanBoard::class)
        ->call('openTask', $task->id)
        ->set('newComment', '')
        ->call('addComment')
        ->assertHasErrors(['newComment']);
});
