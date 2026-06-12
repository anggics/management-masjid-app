<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mosque;
use App\Models\PrayerOverride;
use Illuminate\Http\Request;

class PrayerOverrideController extends Controller
{
    public function index()
    {
        $mosque = Mosque::current();

        return view('admin.prayer', [
            'mosque' => $mosque,
            'overrides' => PrayerOverride::where('mosque_id', $mosque->id)
                ->orderByDesc('date')
                ->paginate(20),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'date' => ['required', 'date'],
            'fajr' => ['nullable', 'date_format:H:i'],
            'dhuha' => ['nullable', 'date_format:H:i'],
            'dhuhr' => ['nullable', 'date_format:H:i'],
            'asr' => ['nullable', 'date_format:H:i'],
            'maghrib' => ['nullable', 'date_format:H:i'],
            'isha' => ['nullable', 'date_format:H:i'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        $mosque = Mosque::current();

        PrayerOverride::updateOrCreate(
            ['mosque_id' => $mosque->id, 'date' => $data['date']],
            array_merge($data, ['created_by' => $request->user()->id]),
        );

        return back()->with('success', 'Override jadwal sholat disimpan.');
    }

    public function destroy(PrayerOverride $override)
    {
        $override->delete();

        return back()->with('success', 'Override jadwal sholat dihapus.');
    }
}
