<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class QurbanParticipant extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'mosque_id', 'user_id', 'qurban_type_id', 'qurban_year_id', 'name', 'animal_type',
        'target_amount', 'collected_amount', 'status',
    ];

    protected $casts = [
        'target_amount' => 'decimal:2',
        'collected_amount' => 'decimal:2',
    ];

    public function mosque(): BelongsTo
    {
        return $this->belongsTo(Mosque::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function qurbanType(): BelongsTo
    {
        return $this->belongsTo(QurbanType::class);
    }

    public function qurbanYear(): BelongsTo
    {
        return $this->belongsTo(QurbanYear::class);
    }

    public function deposits(): HasMany
    {
        return $this->hasMany(QurbanDeposit::class, 'participant_id')->latest();
    }

    public function progressPercent(): int
    {
        if ((float) $this->target_amount <= 0) {
            return 0;
        }

        return (int) min(100, round(($this->collected_amount / $this->target_amount) * 100));
    }
}
