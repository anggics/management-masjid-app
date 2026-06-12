<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrayerOverride extends Model
{
    protected $fillable = [
        'mosque_id', 'date', 'fajr', 'dhuha', 'dhuhr',
        'asr', 'maghrib', 'isha', 'note', 'created_by',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function mosque(): BelongsTo
    {
        return $this->belongsTo(Mosque::class);
    }
}
