<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('qurban_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mosque_id')->constrained()->cascadeOnDelete();
            $table->enum('animal_type', ['kambing', 'sapi']);
            $table->enum('share_type', ['group', 'individu']);
            $table->decimal('target_amount', 15, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qurban_types');
    }
};
