<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prayer_overrides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mosque_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->time('fajr')->nullable();
            $table->time('dhuha')->nullable();
            $table->time('dhuhr')->nullable();
            $table->time('asr')->nullable();
            $table->time('maghrib')->nullable();
            $table->time('isha')->nullable();
            $table->string('note')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['mosque_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prayer_overrides');
    }
};
