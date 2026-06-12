<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Mosque;
use App\Services\PrayerTimeService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PrayerApiController extends Controller
{
    public function index(Request $request, PrayerTimeService $prayer): JsonResponse
    {
        $mosque = Mosque::current();
        $date = $request->date('date') ? Carbon::parse($request->date('date')) : Carbon::today();

        return response()->json([
            'success' => true,
            'message' => 'Jadwal sholat berhasil diambil',
            'data' => $prayer->getForDate($mosque, $date),
        ]);
    }
}
