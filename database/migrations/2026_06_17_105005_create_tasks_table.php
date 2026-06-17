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
            $table->foreignId('project_id')->constrained('team_projects')->cascadeOnDelete()->comment('Relasi ke proyek tim');
            $table->string('title', 200)->comment('Judul tugas');
            $table->text('description')->nullable()->comment('Deskripsi tugas');
            $table->foreignId('assignee_id')->nullable()->constrained('users')->nullOnDelete()->comment('Relasi ke user penerima tugas');
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium')->comment('Prioritas tugas: low, medium, high');
            $table->date('start_date')->nullable()->comment('Tanggal mulai pengerjaan');
            $table->date('due_date')->nullable()->comment('Batas waktu pengerjaan');
            $table->decimal('progress_percent', 5, 2)->default(0)->comment('Persentase progress tugas (0-100)');
            $table->enum('status', ['todo', 'in_progress', 'review', 'done'])->default('todo')->comment('Status tugas: todo, in_progress, review, done');
            $table->timestamp('completed_at')->nullable()->comment('Waktu tugas selesai diselesaikan');
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
