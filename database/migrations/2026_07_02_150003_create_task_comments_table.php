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
        Schema::create('task_comments', function (Blueprint $table) {
            $table->id();

            // Hapus task = hapus semua komentarnya
            $table->foreignId('task_id')->constrained('tasks')->cascadeOnDelete();

            // User yang berkomentar — restrict agar histori komentar terjaga
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();

            $table->text('comment');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_comments');
    }
};
