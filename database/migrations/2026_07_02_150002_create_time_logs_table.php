<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('time_logs', function (Blueprint $table) {
            $table->id();

            // User yang mencatat jam kerja — restrict delete agar data tetap terjaga
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();

            // Proyek terkait — restrict delete agar log tidak hilang
            $table->foreignId('project_id')->constrained('team_projects')->restrictOnDelete();

            // Task terkait (opsional, bisa log jam tanpa task spesifik)
            $table->foreignId('task_id')->nullable()->constrained('tasks')->nullOnDelete();

            $table->dateTime('start_time');
            $table->dateTime('end_time');

            // Durasi dalam jam, dihitung di aplikasi sebelum save (bukan generated column)
            $table->decimal('duration_hours', 5, 2);

            $table->text('notes')->nullable();

            // Status approval oleh PM/Admin
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');

            // User yang mereview time log ini
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();

            // Waktu review dilakukan
            $table->timestamp('reviewed_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_logs');
    }
};
