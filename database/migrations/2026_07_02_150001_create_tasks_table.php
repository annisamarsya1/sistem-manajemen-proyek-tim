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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();

            // Relasi ke proyek — hapus proyek = hapus semua task-nya
            $table->foreignId('project_id')->constrained('team_projects')->cascadeOnDelete();

            $table->string('title', 200);
            $table->text('description')->nullable();

            // User yang ditugaskan. Null jika user dihapus atau belum di-assign.
            $table->foreignId('assignee_id')->nullable()->constrained('users')->nullOnDelete();

            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->date('start_date')->nullable();
            $table->date('due_date')->nullable();

            // Persentase progress task (0.00 - 100.00)
            $table->decimal('progress_percent', 5, 2)->default(0);

            $table->enum('status', ['todo', 'in_progress', 'review', 'done'])->default('todo');

            // Waktu task ditandai selesai (diisi otomatis saat status berubah ke 'done')
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
