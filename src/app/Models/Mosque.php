<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mosque extends Model
{
    protected $fillable = [
        'name', 'address', 'phone', 'description', 'logo_url',
        'city', 'latitude', 'longitude', 'prayer_method',
    ];

    public static function current(): self
    {
        return static::query()->firstOrCreate(
            ['id' => 1],
            ['name' => 'Masjid', 'city' => config('services.aladhan.city')]
        );
    }

    public function paymentMethods(): HasMany
    {
        return $this->hasMany(PaymentMethod::class);
    }

    public function qurbanParticipants(): HasMany
    {
        return $this->hasMany(QurbanParticipant::class);
    }

    public function qurbanTypes(): HasMany
    {
        return $this->hasMany(QurbanType::class);
    }

    public function qurbanYears(): HasMany
    {
        return $this->hasMany(QurbanYear::class);
    }

    public function qurbanCommittees(): HasMany
    {
        return $this->hasMany(QurbanCommittee::class);
    }

    public function financialRecords(): HasMany
    {
        return $this->hasMany(FinancialRecord::class);
    }

    public function studySchedules(): HasMany
    {
        return $this->hasMany(StudySchedule::class);
    }
}
