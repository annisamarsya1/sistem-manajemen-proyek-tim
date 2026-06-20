<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\TaskComment;
use App\Models\TeamProject;
use App\Models\TimeLog;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DemoSeeder extends Seeder
{
    /**
     * Seed demo data — safe to run multiple times (idempotent).
     * Clears existing demo data before re-seeding.
     */
    public function run(): void
    {
        // ---------------------------------------------------------------------------
        // Resolve key users (created by DatabaseSeeder)
        // ---------------------------------------------------------------------------

        $admin = User::where('email', 'admin@proyektim.com')->first();
        $pm = User::where('email', 'pm@proyektim.com')->first();
        $employees = User::where('role', 'employee')
            ->where('is_active', true)
            ->orderBy('email')
            ->get();

        if (! $pm || $employees->isEmpty()) {
            $this->command->warn('Demo users not found. Run php artisan db:seed first.');

            return;
        }

        [$emp1, $emp2, $emp3] = [
            $employees->get(0),
            $employees->get(1) ?? $employees->get(0),
            $employees->get(2) ?? $employees->get(0),
        ];

        // ---------------------------------------------------------------------------
        // Clean up previous demo data (by title pattern)
        // ---------------------------------------------------------------------------

        $demoProjectTitles = ['Website Redesign', 'Mobile App MVP', 'API Integration'];

        $existingProjects = TeamProject::whereIn('title', $demoProjectTitles)->get();

        foreach ($existingProjects as $project) {
            TimeLog::where('project_id', $project->id)->delete();
            TaskComment::whereIn('task_id', $project->tasks()->pluck('id'))->delete();
            $project->tasks()->delete();
            $project->delete();
        }

        // ---------------------------------------------------------------------------
        // 3 Projects
        // ---------------------------------------------------------------------------

        $project1 = TeamProject::create([
            'title' => 'Website Redesign',
            'description' => 'Perancangan ulang tampilan website perusahaan dengan desain modern dan responsif.',
            'client_name' => 'PT Maju Bersama',
            'budget' => 25000000,
            'start_date' => now()->subDays(15)->format('Y-m-d'),
            'deadline' => now()->addDays(30)->format('Y-m-d'),
            'priority' => 'high',
            'status' => 'active',
            'created_by' => $pm->id,
        ]);

        $project2 = TeamProject::create([
            'title' => 'Mobile App MVP',
            'description' => 'Pengembangan MVP aplikasi mobile untuk platform iOS dan Android.',
            'client_name' => 'Startup Teknologi Nusantara',
            'budget' => 50000000,
            'start_date' => now()->addDays(5)->format('Y-m-d'),
            'deadline' => now()->addDays(60)->format('Y-m-d'),
            'priority' => 'urgent',
            'status' => 'planning',
            'created_by' => $pm->id,
        ]);

        $project3 = TeamProject::create([
            'title' => 'API Integration',
            'description' => 'Integrasi REST API dengan sistem pihak ketiga untuk sinkronisasi data real-time.',
            'client_name' => 'CV Digital Solution',
            'budget' => 15000000,
            'start_date' => now()->subDays(40)->format('Y-m-d'),
            'deadline' => now()->subDays(10)->format('Y-m-d'),
            'priority' => 'medium',
            'status' => 'completed',
            'created_by' => $admin->id,
        ]);

        // ---------------------------------------------------------------------------
        // 10 Tasks spread across projects
        // ---------------------------------------------------------------------------

        $tasks = [
            // Project 1 — Website Redesign (active)
            Task::create([
                'project_id' => $project1->id,
                'title' => 'Wireframe halaman utama',
                'description' => 'Buat wireframe untuk Hero Section, Features, dan Footer.',
                'assignee_id' => $emp1->id,
                'priority' => 'high',
                'start_date' => now()->subDays(10)->format('Y-m-d'),
                'due_date' => now()->subDays(3)->format('Y-m-d'),
                'status' => 'done',
                'progress_percent' => 100,
                'completed_at' => now()->subDays(3),
            ]),
            Task::create([
                'project_id' => $project1->id,
                'title' => 'Desain UI komponen navigasi',
                'description' => 'Desain navbar desktop dan mobile dengan Figma.',
                'assignee_id' => $emp1->id,
                'priority' => 'high',
                'start_date' => now()->subDays(3)->format('Y-m-d'),
                'due_date' => now()->addDays(7)->format('Y-m-d'),
                'status' => 'in_progress',
                'progress_percent' => 60,
            ]),
            Task::create([
                'project_id' => $project1->id,
                'title' => 'Implementasi halaman About Us',
                'description' => 'Konversi desain Figma ke HTML/CSS menggunakan Tailwind.',
                'assignee_id' => $emp2->id,
                'priority' => 'medium',
                'start_date' => now()->addDays(2)->format('Y-m-d'),
                'due_date' => now()->addDays(14)->format('Y-m-d'),
                'status' => 'todo',
                'progress_percent' => 0,
            ]),
            Task::create([
                'project_id' => $project1->id,
                'title' => 'Review & QA desain final',
                'description' => 'Review cross-browser dan responsivitas semua halaman.',
                'assignee_id' => $emp3->id,
                'priority' => 'medium',
                'start_date' => now()->addDays(20)->format('Y-m-d'),
                'due_date' => now()->addDays(28)->format('Y-m-d'),
                'status' => 'todo',
                'progress_percent' => 0,
            ]),

            // Project 2 — Mobile App MVP (planning)
            Task::create([
                'project_id' => $project2->id,
                'title' => 'Riset fitur kompetitor',
                'description' => 'Analisis 5 aplikasi kompetitor dan dokumentasikan fitur utama.',
                'assignee_id' => $emp2->id,
                'priority' => 'medium',
                'start_date' => now()->addDays(5)->format('Y-m-d'),
                'due_date' => now()->addDays(12)->format('Y-m-d'),
                'status' => 'todo',
                'progress_percent' => 0,
            ]),
            Task::create([
                'project_id' => $project2->id,
                'title' => 'Setup project React Native',
                'description' => 'Inisialisasi project, konfigurasi navigation, dan struktur folder.',
                'assignee_id' => $emp3->id,
                'priority' => 'high',
                'start_date' => now()->addDays(7)->format('Y-m-d'),
                'due_date' => now()->addDays(15)->format('Y-m-d'),
                'status' => 'todo',
                'progress_percent' => 0,
            ]),

            // Project 3 — API Integration (completed)
            Task::create([
                'project_id' => $project3->id,
                'title' => 'Mapping endpoint API pihak ketiga',
                'description' => 'Dokumentasikan semua endpoint yang dibutuhkan beserta payload.',
                'assignee_id' => $emp1->id,
                'priority' => 'high',
                'start_date' => now()->subDays(35)->format('Y-m-d'),
                'due_date' => now()->subDays(25)->format('Y-m-d'),
                'status' => 'done',
                'progress_percent' => 100,
                'completed_at' => now()->subDays(25),
            ]),
            Task::create([
                'project_id' => $project3->id,
                'title' => 'Implementasi OAuth 2.0 client',
                'description' => 'Implementasi flow autentikasi OAuth 2.0 menggunakan Laravel HTTP Client.',
                'assignee_id' => $emp2->id,
                'priority' => 'high',
                'start_date' => now()->subDays(25)->format('Y-m-d'),
                'due_date' => now()->subDays(18)->format('Y-m-d'),
                'status' => 'done',
                'progress_percent' => 100,
                'completed_at' => now()->subDays(18),
            ]),
            Task::create([
                'project_id' => $project3->id,
                'title' => 'Unit test untuk semua endpoint',
                'description' => 'Tulis Pest tests untuk seluruh service class integrasi.',
                'assignee_id' => $emp3->id,
                'priority' => 'medium',
                'start_date' => now()->subDays(18)->format('Y-m-d'),
                'due_date' => now()->subDays(12)->format('Y-m-d'),
                'status' => 'review',
                'progress_percent' => 90,
            ]),
            Task::create([
                'project_id' => $project3->id,
                'title' => 'Deploy ke staging & smoke test',
                'description' => 'Deploy ke environment staging dan lakukan smoke testing end-to-end.',
                'assignee_id' => $emp1->id,
                'priority' => 'high',
                'start_date' => now()->subDays(12)->format('Y-m-d'),
                'due_date' => now()->subDays(10)->format('Y-m-d'),
                'status' => 'done',
                'progress_percent' => 100,
                'completed_at' => now()->subDays(10),
            ]),
        ];

        // ---------------------------------------------------------------------------
        // 20 Time Logs with varied statuses
        // ---------------------------------------------------------------------------

        $logData = [
            // Approved logs — various employees & projects
            ['user' => $emp1, 'project' => $project1, 'task' => $tasks[0], 'daysAgo' => 10, 'startHour' => 8,  'duration' => 8, 'status' => 'approved', 'reviewer' => $pm],
            ['user' => $emp1, 'project' => $project1, 'task' => $tasks[0], 'daysAgo' => 9,  'startHour' => 8,  'duration' => 7, 'status' => 'approved', 'reviewer' => $pm],
            ['user' => $emp2, 'project' => $project1, 'task' => $tasks[1], 'daysAgo' => 8,  'startHour' => 9,  'duration' => 6, 'status' => 'approved', 'reviewer' => $pm],
            ['user' => $emp3, 'project' => $project3, 'task' => $tasks[6], 'daysAgo' => 30, 'startHour' => 8,  'duration' => 8, 'status' => 'approved', 'reviewer' => $admin],
            ['user' => $emp3, 'project' => $project3, 'task' => $tasks[6], 'daysAgo' => 29, 'startHour' => 8,  'duration' => 7, 'status' => 'approved', 'reviewer' => $admin],
            ['user' => $emp1, 'project' => $project3, 'task' => $tasks[7], 'daysAgo' => 24, 'startHour' => 9,  'duration' => 8, 'status' => 'approved', 'reviewer' => $pm],
            ['user' => $emp2, 'project' => $project3, 'task' => $tasks[7], 'daysAgo' => 23, 'startHour' => 8,  'duration' => 6, 'status' => 'approved', 'reviewer' => $pm],
            ['user' => $emp1, 'project' => $project3, 'task' => $tasks[9], 'daysAgo' => 11, 'startHour' => 8,  'duration' => 8, 'status' => 'approved', 'reviewer' => $admin],

            // Rejected logs
            ['user' => $emp2, 'project' => $project1, 'task' => $tasks[2], 'daysAgo' => 7,  'startHour' => 22, 'duration' => 10, 'status' => 'rejected', 'reviewer' => $pm],
            ['user' => $emp3, 'project' => $project3, 'task' => $tasks[8], 'daysAgo' => 15, 'startHour' => 6,  'duration' => 11, 'status' => 'rejected', 'reviewer' => $admin],

            // Pending logs — waiting for approval
            ['user' => $emp1, 'project' => $project1, 'task' => $tasks[1], 'daysAgo' => 2,  'startHour' => 8,  'duration' => 7, 'status' => 'pending', 'reviewer' => null],
            ['user' => $emp1, 'project' => $project1, 'task' => $tasks[1], 'daysAgo' => 1,  'startHour' => 8,  'duration' => 8, 'status' => 'pending', 'reviewer' => null],
            ['user' => $emp2, 'project' => $project1, 'task' => $tasks[2], 'daysAgo' => 3,  'startHour' => 9,  'duration' => 6, 'status' => 'pending', 'reviewer' => null],
            ['user' => $emp2, 'project' => $project1, 'task' => $tasks[2], 'daysAgo' => 2,  'startHour' => 9,  'duration' => 7, 'status' => 'pending', 'reviewer' => null],
            ['user' => $emp3, 'project' => $project3, 'task' => $tasks[8], 'daysAgo' => 14, 'startHour' => 9,  'duration' => 8, 'status' => 'pending', 'reviewer' => null],
            ['user' => $emp3, 'project' => $project3, 'task' => $tasks[8], 'daysAgo' => 13, 'startHour' => 8,  'duration' => 6, 'status' => 'pending', 'reviewer' => null],
            ['user' => $emp1, 'project' => $project3, 'task' => $tasks[9], 'daysAgo' => 12, 'startHour' => 8,  'duration' => 7, 'status' => 'pending', 'reviewer' => null],
            ['user' => $emp2, 'project' => $project1, 'task' => $tasks[1], 'daysAgo' => 5,  'startHour' => 10, 'duration' => 5, 'status' => 'pending', 'reviewer' => null],
            ['user' => $emp3, 'project' => $project1, 'task' => $tasks[3], 'daysAgo' => 4,  'startHour' => 8,  'duration' => 4, 'status' => 'pending', 'reviewer' => null],
            ['user' => $emp1, 'project' => $project1, 'task' => $tasks[0], 'daysAgo' => 6,  'startHour' => 8,  'duration' => 8, 'status' => 'pending', 'reviewer' => null],
        ];

        $notes = [
            'Pengerjaan rutin sesuai jadwal sprint.',
            'Progress sesuai target, dokumentasi diperbarui.',
            'Menemukan beberapa issue minor, sudah diperbaiki.',
            'Review dengan tim dan revisi selesai.',
            'Koordinasi dengan klien untuk feedback.',
            'Testing dan debugging komponen utama.',
            'Implementasi berdasarkan feedback review terbaru.',
        ];

        foreach ($logData as $i => $entry) {
            $startTime = Carbon::now()
                ->subDays($entry['daysAgo'])
                ->setHour($entry['startHour'])
                ->setMinute(0)
                ->setSecond(0);

            $endTime = (clone $startTime)->addHours($entry['duration']);
            $durationHours = round($entry['duration'], 2);

            $log = TimeLog::create([
                'user_id' => $entry['user']->id,
                'project_id' => $entry['project']->id,
                'task_id' => $entry['task']->id,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'duration_hours' => $durationHours,
                'notes' => $notes[$i % count($notes)],
                'status' => $entry['status'],
                'reviewed_by' => $entry['reviewer']?->id,
                'reviewed_at' => $entry['reviewer'] ? $endTime->addHours(2) : null,
            ]);
        }

        // ---------------------------------------------------------------------------
        // Sample comments on a completed task
        // ---------------------------------------------------------------------------

        TaskComment::create([
            'task_id' => $tasks[0]->id,
            'user_id' => $emp1->id,
            'comment' => 'Wireframe sudah selesai dan di-upload ke Figma. Silakan direview.',
        ]);

        TaskComment::create([
            'task_id' => $tasks[0]->id,
            'user_id' => $pm->id,
            'comment' => 'Sudah saya review, ada sedikit revisi di bagian hero section. Tolong perbaiki spacing-nya.',
        ]);

        TaskComment::create([
            'task_id' => $tasks[0]->id,
            'user_id' => $emp1->id,
            'comment' => 'Revisi sudah selesai, spacing hero section sudah diperbaiki sesuai feedback.',
        ]);

        $this->command->info('DemoSeeder selesai: 3 proyek, 10 tugas, 20 time logs, dan komentar berhasil dibuat.');
    }
}
