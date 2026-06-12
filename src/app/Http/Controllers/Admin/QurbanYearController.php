<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mosque;
use App\Models\QurbanYear;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class QurbanYearController extends Controller
{
    public function index()
    {
        $mosque = Mosque::current();

        return view('admin.qurban-years', [
            'years' => $mosque->qurbanYears()->orderByDesc('hijri_year')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $mosque = Mosque::current();

        $data = $request->validate([
            'hijri_year' => [
                'required', 'integer', 'min:1300', 'max:1600',
                Rule::unique('qurban_years')->where('mosque_id', $mosque->id),
            ],
        ]);
        $data['mosque_id'] = $mosque->id;
        $data['is_active'] = $request->boolean('is_active', true);

        QurbanYear::create($data);

        return back()->with('success', 'Tahun qurban ditambahkan.');
    }

    public function update(Request $request, QurbanYear $qurbanYear)
    {
        // Toggle status aktif/nonaktif.
        $qurbanYear->update(['is_active' => ! $qurbanYear->is_active]);

        return back()->with('success', 'Status tahun qurban diperbarui.');
    }

    public function destroy(QurbanYear $qurbanYear)
    {
        $qurbanYear->delete();

        return back()->with('success', 'Tahun qurban dihapus.');
    }
}
