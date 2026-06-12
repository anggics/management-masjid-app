<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('qurban_participants', function (Blueprint $table) {
            $table->foreignId('qurban_type_id')->nullable()->after('user_id')
                ->constrained('qurban_types')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('qurban_participants', function (Blueprint $table) {
            $table->dropConstrainedForeignId('qurban_type_id');
        });
    }
};
