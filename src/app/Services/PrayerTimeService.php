<?php

namespace App\Services;

use App\Models\Mosque;
use App\Models\PrayerCache;
use App\Models\PrayerOverride;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PrayerTimeService
{
    /**
     * Pemetaan metode kalkulasi → kode method Aladhan API (F-ADM-22).
     *
     * @see https://aladhan.com/calculation-methods
     */
    public const METHODS = [
        'Kemenag' => 20, // Kementerian Agama RI
        'MWL' => 3,      // Muslim World League
        'ISNA' => 2,     // Islamic Society of North America
        'Egypt' => 5,    // Egyptian General Authority
        'Makkah' => 4,   // Umm al-Qura, Makkah
        'Karachi' => 1,  // University of Islamic Sciences, Karachi
    ];

    /** Kode Aladhan untuk metode tersimpan, fallback ke konfigurasi default. */
    public static function methodCode(?string $method): int
    {
        return self::METHODS[$method] ?? (int) config('services.aladhan.method');
    }

    /**
     * Ambil jadwal sholat untuk tanggal tertentu.
     * Prioritas: override manual > cache (<24 jam) > Aladhan API > cache lama (fallback).
     *
     * @return array{date:string, times:array<string,string>, source:string, note:?string}
     */
    public function getForDate(Mosque $mosque, Carbon $date): array
    {
        // 1) Override manual admin
        $override = PrayerOverride::where('mosque_id', $mosque->id)
            ->whereDate('date', $date->toDateString())
            ->first();

        if ($override) {
            return [
                'date' => $date->toDateString(),
                'times' => $this->mapOverride($override),
                'source' => 'override',
                'note' => $override->note,
            ];
        }

        // 2) Cache valid (<24 jam)
        $cache = PrayerCache::where('mosque_id', $mosque->id)
            ->whereDate('date', $date->toDateString())
            ->first();

        if ($cache && $cache->cached_at && $cache->cached_at->gt(now()->subDay())) {
            return [
                'date' => $date->toDateString(),
                'times' => $this->mapApi($cache->data),
                'source' => 'cache',
                'note' => null,
            ];
        }

        // 3) Fetch dari Aladhan API
        try {
            $data = $this->fetchFromApi($mosque, $date);

            PrayerCache::updateOrCreate(
                ['mosque_id' => $mosque->id, 'date' => $date->toDateString()],
                ['data' => $data, 'cached_at' => now()],
            );

            return [
                'date' => $date->toDateString(),
                'times' => $this->mapApi($data),
                'source' => 'api',
                'note' => null,
            ];
        } catch (\Throwable $e) {
            Log::warning('Aladhan API gagal: '.$e->getMessage());

            // 4) Fallback ke cache lama bila ada
            if ($cache) {
                return [
                    'date' => $date->toDateString(),
                    'times' => $this->mapApi($cache->data),
                    'source' => 'stale-cache',
                    'note' => null,
                ];
            }

            return [
                'date' => $date->toDateString(),
                'times' => [],
                'source' => 'unavailable',
                'note' => 'Jadwal sholat sementara tidak tersedia.',
            ];
        }
    }

    private function fetchFromApi(Mosque $mosque, Carbon $date): array
    {
        $base = rtrim(config('services.aladhan.base_url'), '/');
        $url = $base.'/timingsByCity/'.$date->format('d-m-Y');

        $response = Http::timeout(8)->retry(2, 200)->get($url, [
            'city' => $mosque->city ?: config('services.aladhan.city'),
            'country' => config('services.aladhan.country'),
            'method' => self::methodCode($mosque->prayer_method),
        ]);

        $response->throw();

        return $response->json('data') ?? [];
    }

    private function mapApi(array $data): array
    {
        $t = $data['timings'] ?? [];

        return [
            'Subuh' => $this->clean($t['Fajr'] ?? null),
            'Dhuha' => $this->clean($t['Sunrise'] ?? null),
            'Dzuhur' => $this->clean($t['Dhuhr'] ?? null),
            'Ashar' => $this->clean($t['Asr'] ?? null),
            'Maghrib' => $this->clean($t['Maghrib'] ?? null),
            'Isya' => $this->clean($t['Isha'] ?? null),
        ];
    }

    private function mapOverride(PrayerOverride $o): array
    {
        return array_filter([
            'Subuh' => $o->fajr ? substr($o->fajr, 0, 5) : null,
            'Dhuha' => $o->dhuha ? substr($o->dhuha, 0, 5) : null,
            'Dzuhur' => $o->dhuhr ? substr($o->dhuhr, 0, 5) : null,
            'Ashar' => $o->asr ? substr($o->asr, 0, 5) : null,
            'Maghrib' => $o->maghrib ? substr($o->maghrib, 0, 5) : null,
            'Isya' => $o->isha ? substr($o->isha, 0, 5) : null,
        ]);
    }

    private function clean(?string $time): ?string
    {
        if (! $time) {
            return null;
        }

        // Aladhan kadang mengembalikan "04:38 (WIB)"
        return trim(explode(' ', $time)[0]);
    }
}
