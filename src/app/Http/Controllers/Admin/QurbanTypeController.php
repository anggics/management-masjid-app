<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mosque;
use App\Models\QurbanType;
use Illuminate\Http\Request;

class QurbanTypeController extends Controller
{
    public function index()
    {
        $mosque = Mosque::current();

        return view('admin.qurban-types', [
            'types' => $mosque->qurbanTypes()->latest()->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $data['mosque_id'] = Mosque::current()->id;

        QurbanType::create($data);

        return back()->with('success', 'Jenis hewan qurban ditambahkan.');
    }

    public function update(Request $request, QurbanType $qurbanType)
    {
        $qurbanType->update($this->validateData($request));

        return back()->with('success', 'Jenis hewan qurban diperbarui.');
    }

    public function destroy(QurbanType $qurbanType)
    {
        $qurbanType->delete();

        return back()->with('success', 'Jenis hewan qurban dihapus.');
    }

    private function validateData(Request $request): array
    {
        $data = $request->validate([
            'animal_type' => ['required', 'in:kambing,sapi'],
            'share_type' => ['required', 'in:group,individu'],
            'target_amount' => ['required', 'numeric', 'min:0'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
