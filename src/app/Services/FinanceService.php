<?php

namespace App\Services;

use App\Models\FinancialRecord;
use App\Models\FinancialRecordLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FinanceService
{
    public function create(array $data): FinancialRecord
    {
        return DB::transaction(function () use ($data) {
            $record = FinancialRecord::create($data);

            $this->log($record, 'created', null, $record->toArray());

            return $record;
        });
    }

    public function update(FinancialRecord $record, array $data): FinancialRecord
    {
        return DB::transaction(function () use ($record, $data) {
            $old = $record->toArray();
            $record->update($data);

            $this->log($record, 'updated', $old, $record->fresh()->toArray());

            return $record;
        });
    }

    public function delete(FinancialRecord $record): void
    {
        DB::transaction(function () use ($record) {
            $this->log($record, 'deleted', $record->toArray(), null);
            $record->delete();
        });
    }

    /**
     * Ringkasan total pemasukan, pengeluaran, dan saldo.
     */
    public function summary($query): array
    {
        $base = clone $query;
        $income = (clone $base)->where('type', 'income')->sum('amount');
        $expense = (clone $base)->where('type', 'expense')->sum('amount');

        return [
            'income' => (float) $income,
            'expense' => (float) $expense,
            'balance' => (float) $income - (float) $expense,
        ];
    }

    private function log(FinancialRecord $record, string $action, ?array $old, ?array $new): void
    {
        FinancialRecordLog::create([
            'financial_record_id' => $record->id,
            'action' => $action,
            'old_data' => $old,
            'new_data' => $new,
            'performed_by' => Auth::id(),
        ]);
    }
}
