<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Master jenis hewan qurban yang dikelola admin.
 * Dipakai sebagai dropdown saat admin menambah peserta & saat user mendaftar qurban.
 */
class QurbanType extends Model
{
    protected $fillable = [
        'mosque_id', 'animal_type', 'share_type', 'target_amount', 'is_active',
    ];

    protected $casts = [
        'target_amount' => 'decimal:2',
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

    /** Label dropdown: "Sapi (Group — Rp 3.500.000)". */
    public function label(): string
    {
        return sprintf(
            '%s (%s — Rp %s)',
            ucfirst($this->animal_type),
            ucfirst($this->share_type),
            number_format((float) $this->target_amount, 0, ',', '.'),
        );
    }
}
