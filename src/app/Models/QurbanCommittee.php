<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Panitia qurban (nama, alamat, no WA).
 */
class QurbanCommittee extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'mosque_id', 'name', 'address', 'whatsapp',
    ];

    public function mosque(): BelongsTo
    {
        return $this->belongsTo(Mosque::class);
    }
}
