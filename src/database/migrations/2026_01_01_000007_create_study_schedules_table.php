<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('study_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mosque_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('speaker');
            $table->dateTime('scheduled_at');
            $table->string('location');
            $table->text('description')->nullable();
            $table->string('poster_url')->nullable();
            $table->enum('status', ['upcoming', 'ongoing', 'done', 'cancelled'])->default('upcoming');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['mosque_id', 'scheduled_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('study_schedules');
    }
};
