<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinancialRecord extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'mosque_id', 'type', 'category', 'amount',
        'description', 'recorded_by', 'recorded_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'recorded_at' => 'date',
    ];

    public function mosque(): BelongsTo
    {
        return $this->belongsTo(Mosque::class);
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(FinancialRecordLog::class);
    }
}
