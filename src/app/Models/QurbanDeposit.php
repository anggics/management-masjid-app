<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QurbanDeposit extends Model
{
    protected $fillable = [
        'participant_id', 'user_id', 'amount', 'proof_image_url',
        'notes', 'status', 'verified_by', 'verified_at', 'rejection_reason',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'verified_at' => 'datetime',
    ];

    public function participant(): BelongsTo
    {
        return $this->belongsTo(QurbanParticipant::class, 'participant_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
