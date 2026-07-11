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
            $table->string('title', 200);
            $table->text('description')->nullable();
            $table->string('client_name', 100)->nullable();
            $table->date('start_date')->nullable();
            $table->date('deadline');

            // Anggaran proyek, mendukung hingga 9.999.999.999,99
            $table->decimal('budget', 12, 2)->default(0);

            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->enum('status', ['planning', 'active', 'on_hold', 'completed', 'cancelled'])->default('planning');

            // User yang membuat proyek (PM/Admin). Null jika user dihapus.
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

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
