<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prayer_cache', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mosque_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->json('data');
            $table->timestamp('cached_at');
            $table->timestamps();

            $table->unique(['mosque_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prayer_cache');
    }
};
