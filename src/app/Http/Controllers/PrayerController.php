<?php

namespace App\Http\Controllers;

use App\Models\Mosque;
use App\Services\PrayerTimeService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PrayerController extends Controller
{
    public function index(Request $request, PrayerTimeService $prayer)
    {
        $mosque = Mosque::current();
        $date = $request->date('date') ?: Carbon::today();
        $date = Carbon::parse($date);

        $schedule = $prayer->getForDate($mosque, $date);

        return view('public.prayer', [
            'mosque' => $mosque,
            'schedule' => $schedule,
            'date' => $date,
            'prev' => $date->copy()->subDay()->toDateString(),
            'next' => $date->copy()->addDay()->toDateString(),
        ]);
    }
}
