<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Master tahun (hijriah) pelaksanaan qurban.
 * Dipakai sebagai dropdown saat menambah data qurban & filter.
 */
class QurbanYear extends Model
{
    protected $fillable = [
        'mosque_id', 'hijri_year', 'is_active',
    ];

    protected $casts = [
        'hijri_year' => 'integer',
        'is_active' => 'boolean',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function mosque(): BelongsTo
    {
        return $this->belongsTo(Mosque::class);
    }

    public function participants(): HasMany
    {
        return $this->hasMany(QurbanParticipant::class);
    }

    /** Label dropdown: "1447 H". */
    public function label(): string
    {
        return $this->hijri_year.' H';
    }
}
