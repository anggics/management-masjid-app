<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinancialRecordLog extends Model
{
    protected $fillable = [
        'financial_record_id', 'action', 'old_data', 'new_data', 'performed_by',
    ];

    protected $casts = [
        'old_data' => 'array',
        'new_data' => 'array',
    ];

    public function record(): BelongsTo
    {
        return $this->belongsTo(FinancialRecord::class, 'financial_record_id');
    }

    public function performer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
}
