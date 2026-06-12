<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('qurban_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mosque_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->enum('animal_type', ['sapi', 'kambing']);
            $table->decimal('target_amount', 15, 2)->default(0);
            $table->decimal('collected_amount', 15, 2)->default(0);
            $table->enum('status', ['active', 'completed', 'cancelled'])->default('active');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qurban_participants');
    }
};
