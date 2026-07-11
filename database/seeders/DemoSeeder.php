<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\TaskComment;
use App\Models\TeamProject;
use App\Models\TimeLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        
        TeamProject::truncate();
        Task::truncate();
        TimeLog::truncate();
        TaskComment::truncate();

        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        $pm = User::where('email', 'pm@proyektim.com')->first();
        $admin = User::where('email', 'admin@proyektim.com')->first();
        $employee1 = User::where('email', 'employee1@proyektim.com')->first();
        $employee2 = User::where('email', 'employee2@proyektim.com')->first();
        $employee3 = User::where('email', 'employee3@proyektim.com')->first();
        
        if (!$pm || !$admin || !$employee1) {
            $this->command->warn('Tolong jalankan DatabaseSeeder dulu untuk membuat akun admin/pm/employee.');
            return;
        }

        $creatorId = $pm->id;

        // 3 Projects
        $projects = [
            [
                'title' => 'Website Redesign',
                'description' => 'Redesign website korporat dengan UI/UX yang modern.',
                'client_name' => 'PT Maju Bersama',
                'start_date' => Carbon::now()->subDays(10),
                'deadline' => Carbon::now()->addDays(30),
                'budget' => 50000000,
                'priority' => 'high',
                'status' => 'active',
                'created_by' => $creatorId,
                'created_at' => Carbon::now()->subDays(10),
            ],
            [
                'title' => 'Mobile App MVP',
                'description' => 'Aplikasi mobile untuk pelanggan setia (iOS & Android).',
                'client_name' => 'Tech Startup Inc.',
                'start_date' => Carbon::now()->addDays(5),
                'deadline' => Carbon::now()->addDays(60),
                'budget' => 120000000,
                'priority' => 'urgent',
                'status' => 'planning',
                'created_by' => $creatorId,
                'created_at' => Carbon::now()->subDays(2),
            ],
            [
                'title' => 'API Integration',
                'description' => 'Integrasi API Payment Gateway pihak ketiga.',
                'client_name' => 'Toko Online Sejahtera',
                'start_date' => Carbon::now()->subDays(40),
                'deadline' => Carbon::now()->subDays(10),
                'budget' => 15000000,
                'priority' => 'medium',
                'status' => 'completed',
                'created_by' => $creatorId,
                'created_at' => Carbon::now()->subDays(45),
            ],
        ];

        foreach ($projects as $projectData) {
            TeamProject::create($projectData);
        }

        $p1 = TeamProject::where('title', 'Website Redesign')->first();
        $p2 = TeamProject::where('title', 'Mobile App MVP')->first();
        $p3 = TeamProject::where('title', 'API Integration')->first();

        // 10 Tasks
        $tasks = [
            ['title' => 'Buat Wireframe Homepage', 'project_id' => $p1->id, 'assignee_id' => $employee1->id, 'status' => 'done', 'priority' => 'high', 'due_date' => Carbon::now()->subDays(5)],
            ['title' => 'Desain UI Homepage', 'project_id' => $p1->id, 'assignee_id' => $employee1->id, 'status' => 'review', 'priority' => 'high', 'due_date' => Carbon::now()->addDays(2)],
            ['title' => 'Slice HTML/CSS', 'project_id' => $p1->id, 'assignee_id' => $employee2?->id ?? $employee1->id, 'status' => 'in_progress', 'priority' => 'medium', 'due_date' => Carbon::now()->addDays(10)],
            ['title' => 'Setup Database', 'project_id' => $p1->id, 'assignee_id' => null, 'status' => 'todo', 'priority' => 'medium', 'due_date' => Carbon::now()->addDays(15)],
            
            ['title' => 'Riset Kompetitor', 'project_id' => $p2->id, 'assignee_id' => $employee1->id, 'status' => 'done', 'priority' => 'medium', 'due_date' => Carbon::now()->addDays(10)],
            ['title' => 'Tech Stack Decision', 'project_id' => $p2->id, 'assignee_id' => null, 'status' => 'todo', 'priority' => 'high', 'due_date' => Carbon::now()->addDays(15)],
            
            ['title' => 'Baca Dokumentasi API', 'project_id' => $p3->id, 'assignee_id' => $employee1->id, 'status' => 'done', 'priority' => 'low', 'due_date' => Carbon::now()->subDays(20)],
            ['title' => 'Implementasi Endpoint Sandbox', 'project_id' => $p3->id, 'assignee_id' => $employee2?->id ?? $employee1->id, 'status' => 'done', 'priority' => 'high', 'due_date' => Carbon::now()->subDays(15)],
            ['title' => 'UAT Testing', 'project_id' => $p3->id, 'assignee_id' => $employee1->id, 'status' => 'done', 'priority' => 'high', 'due_date' => Carbon::now()->subDays(12)],
            ['title' => 'Deploy Production', 'project_id' => $p3->id, 'assignee_id' => null, 'status' => 'done', 'priority' => 'high', 'due_date' => Carbon::now()->subDays(10)],
        ];

        foreach ($tasks as $taskData) {
            Task::create($taskData);
        }

        // Time Logs for Employees
        $employeesToSeed = [$employee1, $employee2, $employee3];
        $allTasks = Task::all();

        foreach ($employeesToSeed as $emp) {
            if (!$emp) continue;
            
            $logCount = ($emp->id === $employee1->id) ? 20 : 10;
            
            for ($i = 0; $i < $logCount; $i++) {
                $task = $allTasks->random();
                
                $start = Carbon::now()->subDays(rand(0, 3))->setHour(rand(8, 14))->setMinute(0);
                $durationHours = rand(1, 5) + (rand(0, 1) ? 0.5 : 0);
                $end = clone $start;
                $end->addMinutes($durationHours * 60);

                $status = ['pending', 'approved', 'rejected'][rand(0, 2)];
                
                TimeLog::create([
                    'user_id' => $emp->id,
                    'project_id' => $task->project_id,
                    'task_id' => $task->id,
                    'start_time' => $start,
                    'end_time' => $end,
                    'duration_hours' => $durationHours,
                    'notes' => 'Mengerjakan ' . $task->title,
                    'status' => $status,
                    'reviewed_by' => $status !== 'pending' ? $pm->id : null,
                    'reviewed_at' => $status !== 'pending' ? clone $end->addHours(2) : null,
                ]);
            }
        }

        $this->command->info('Data demo berhasil di-seed.');
    }
}
