<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah tipe 'rekening_qurban' pada enum payment_methods.type.
        DB::statement("ALTER TABLE payment_methods MODIFY COLUMN type ENUM('qris','bank_transfer','rekening_qurban') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE payment_methods MODIFY COLUMN type ENUM('qris','bank_transfer') NOT NULL");
    }
};
