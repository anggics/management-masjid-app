<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentMethod extends Model
{
    protected $fillable = [
        'mosque_id', 'type', 'label', 'qris_image_url',
        'bank_name', 'account_number', 'account_name',
        'is_active', 'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function mosque(): BelongsTo
    {
        return $this->belongsTo(Mosque::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
