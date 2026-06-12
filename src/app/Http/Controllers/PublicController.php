<?php

namespace App\Http\Controllers;

use App\Models\Mosque;
use App\Models\StudySchedule;
use App\Services\PrayerTimeService;
use Carbon\Carbon;

class PublicController extends Controller
{
    public function home(PrayerTimeService $prayer)
    {
        $mosque = Mosque::current();
        $today = $prayer->getForDate($mosque, Carbon::today());

        $upcoming = StudySchedule::where('mosque_id', $mosque->id)
            ->upcoming()
            ->limit(3)
            ->get();

        return view('public.home', [
            'mosque' => $mosque,
            'prayer' => $today,
            'upcoming' => $upcoming,
        ]);
    }
}
