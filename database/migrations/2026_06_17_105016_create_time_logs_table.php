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
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete()->comment('Relasi ke user pelaku pencatatan waktu');
            $table->foreignId('project_id')->constrained('team_projects')->restrictOnDelete()->comment('Relasi ke proyek tim');
            $table->foreignId('task_id')->nullable()->constrained('tasks')->nullOnDelete()->comment('Relasi ke tugas tertentu');
            $table->dateTime('start_time')->comment('Waktu mulai pencatatan');
            $table->dateTime('end_time')->comment('Waktu selesai pencatatan');
            $table->decimal('duration_hours', 5, 2)->comment('Durasi dalam jam (dihitung secara manual di aplikasi sebelum disimpan)');
            $table->text('notes')->nullable()->comment('Catatan pekerjaan');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->comment('Status log: pending, approved, rejected');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete()->comment('User peninjau (biasanya admin atau pm)');
            $table->timestamp('reviewed_at')->nullable()->comment('Waktu log ditinjau');
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
