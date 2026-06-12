<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('qurban_years', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mosque_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('hijri_year'); // mis. 1447
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['mosque_id', 'hijri_year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qurban_years');
    }
};
