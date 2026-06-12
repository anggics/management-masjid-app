<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financial_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mosque_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['income', 'expense']);
            $table->string('category');
            $table->decimal('amount', 15, 2);
            $table->text('description')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->date('recorded_at');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['mosque_id', 'recorded_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_records');
    }
};
