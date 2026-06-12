<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrayerCache extends Model
{
    protected $table = 'prayer_cache';

    protected $fillable = [
        'mosque_id', 'date', 'data', 'cached_at',
    ];

    protected $casts = [
        'date' => 'date',
        'data' => 'array',
        'cached_at' => 'datetime',
    ];
}
