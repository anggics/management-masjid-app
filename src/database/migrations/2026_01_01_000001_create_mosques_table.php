<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mosques', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            $table->text('description')->nullable();
            $table->string('logo_url')->nullable();
            $table->string('city')->default('Jakarta');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('prayer_method')->default('Kemenag');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mosques');
    }
};
