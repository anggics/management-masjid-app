<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mosque;
use App\Models\QurbanParticipant;
use App\Models\QurbanType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class QurbanController extends Controller
{
    public function index(Request $request)
    {
        $mosque = Mosque::current();

        $query = $mosque->qurbanParticipants()->with(['qurbanType', 'qurbanYear', 'user']);

        // Filter berdasarkan nama
        if ($search = trim((string) $request->input('search'))) {
            $query->where('name', 'like', '%'.$search.'%');
        }

        // Filter berdasarkan jenis hewan
        if ($animal = $request->input('animal_type')) {
            $query->where('animal_type', $animal);
        }

        // Filter berdasarkan tahun qurban (master data)
        if ($yearId = $request->input('qurban_year_id')) {
            $query->where('qurban_year_id', $yearId);
        }

        return view('admin.qurban', [
            'mosque' => $mosque,
            'participants' => $query->latest()->paginate(20)->withQueryString(),
            'types' => $mosque->qurbanTypes()->active()->latest()->get(),
            'years' => $mosque->qurbanYears()->active()->orderByDesc('hijri_year')->get(),
            'filters' => [
                'search' => $search ?? '',
                'animal_type' => $animal,
                'qurban_year_id' => $yearId,
            ],
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $data['mosque_id'] = Mosque::current()->id;

        QurbanParticipant::create($data);

        return back()->with('success', 'Peserta qurban ditambahkan.');
    }

    public function update(Request $request, QurbanParticipant $participant)
    {
        $participant->update($this->validateData($request));

        return back()->with('success', 'Data qurban diperbarui.');
    }

    public function destroy(QurbanParticipant $participant)
    {
        $participant->delete(); // soft delete

        return back()->with('success', 'Data qurban dihapus.');
    }

    private function validateData(Request $request): array
    {
        $mosqueId = Mosque::current()->id;

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'qurban_type_id' => ['required', Rule::exists('qurban_types', 'id')->where('mosque_id', $mosqueId)],
            'qurban_year_id' => ['required', Rule::exists('qurban_years', 'id')->where('mosque_id', $mosqueId)],
            'collected_amount' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', 'in:active,completed,cancelled'],
        ]);

        // Jenis hewan & target diambil dari master jenis yang dipilih.
        $type = QurbanType::findOrFail($data['qurban_type_id']);
        $data['animal_type'] = $type->animal_type;
        $data['target_amount'] = $type->target_amount;

        return $data;
    }
}
