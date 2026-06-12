<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mosque;
use App\Models\QurbanCommittee;
use Illuminate\Http\Request;

class CommitteeController extends Controller
{
    public function index(Request $request)
    {
        $mosque = Mosque::current();

        $query = $mosque->qurbanCommittees();

        // Pencarian berdasarkan nama atau alamat
        if ($search = trim((string) $request->input('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%'.$search.'%')
                    ->orWhere('address', 'like', '%'.$search.'%');
            });
        }

        return view('admin.committees', [
            'committees' => $query->latest()->paginate(20)->withQueryString(),
            'search' => $search ?? '',
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $data['mosque_id'] = Mosque::current()->id;

        QurbanCommittee::create($data);

        return back()->with('success', 'Panitia qurban ditambahkan.');
    }

    public function update(Request $request, QurbanCommittee $committee)
    {
        $committee->update($this->validateData($request));

        return back()->with('success', 'Data panitia diperbarui.');
    }

    public function destroy(QurbanCommittee $committee)
    {
        $committee->delete();

        return back()->with('success', 'Panitia qurban dihapus.');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'whatsapp' => ['nullable', 'string', 'max:30'],
        ]);
    }
}
