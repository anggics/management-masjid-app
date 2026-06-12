<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mosque;
use App\Models\PrayerCache;
use App\Services\MediaService;
use App\Services\PrayerTimeService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('admin.profile', [
            'mosque' => Mosque::current(),
            'prayerMethods' => array_keys(PrayerTimeService::METHODS),
        ]);
    }

    public function update(Request $request, MediaService $media)
    {
        $mosque = Mosque::current();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:1000'],
            'phone' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:2000'],
            'city' => ['required', 'string', 'max:120'],
            'prayer_method' => ['required', Rule::in(array_keys(PrayerTimeService::METHODS))],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        if ($request->hasFile('logo')) {
            $data['logo_url'] = $media->store($request->file('logo'), 'logos');
        }

        // Bila kota atau metode kalkulasi berubah, cache jadwal lama tidak valid lagi.
        $needsRefresh = $mosque->city !== $data['city']
            || $mosque->prayer_method !== $data['prayer_method'];

        $mosque->update($data);

        if ($needsRefresh) {
            PrayerCache::where('mosque_id', $mosque->id)->delete();
        }

        return back()->with('success', 'Profil masjid berhasil diperbarui.');
    }
}
