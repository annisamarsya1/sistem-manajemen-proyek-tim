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
        Schema::create('team_projects', function (Blueprint $table) {
            $table->id();
            $table->string('title', 200)->comment('Judul proyek tim');
            $table->text('description')->nullable()->comment('Deskripsi detail proyek');
            $table->string('client_name', 100)->nullable()->comment('Nama klien pemilik proyek');
            $table->date('start_date')->nullable()->comment('Tanggal mulai proyek');
            $table->date('deadline')->comment('Batas waktu penyelesaian proyek');
            $table->decimal('budget', 12, 2)->default(0)->comment('Anggaran proyek');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium')->comment('Prioritas: low, medium, high, urgent');
            $table->enum('status', ['planning', 'active', 'on_hold', 'completed', 'cancelled'])->default('planning')->comment('Status proyek: planning, active, on_hold, completed, cancelled');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete()->comment('Pembuat proyek dari tabel users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('team_projects');
    }
};
