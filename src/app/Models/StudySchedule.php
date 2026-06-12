<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudySchedule extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'mosque_id', 'title', 'speaker', 'scheduled_at', 'location',
        'description', 'poster_url', 'status', 'created_by',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    public function mosque(): BelongsTo
    {
        return $this->belongsTo(Mosque::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeUpcoming($query)
    {
        return $query->whereIn('status', ['upcoming', 'ongoing'])->orderBy('scheduled_at');
    }
}
