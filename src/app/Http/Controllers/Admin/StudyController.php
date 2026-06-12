<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mosque;
use App\Models\StudySchedule;
use App\Services\MediaService;
use Illuminate\Http\Request;

class StudyController extends Controller
{
    public function index(Request $request)
    {
        $mosque = Mosque::current();

        $query = $mosque->studySchedules();

        // Filter berdasarkan nama kajian
        if ($search = trim((string) $request->input('search'))) {
            $query->where('title', 'like', '%'.$search.'%');
        }

        // Filter berdasarkan tanggal kajian
        if ($date = $request->input('date')) {
            $query->whereDate('scheduled_at', $date);
        }

        return view('admin.study', [
            'mosque' => $mosque,
            'schedules' => $query->orderByDesc('scheduled_at')->paginate(20)->withQueryString(),
            'filters' => ['search' => $search ?? '', 'date' => $date],
        ]);
    }

    public function store(Request $request, MediaService $media)
    {
        $data = $this->validateData($request);
        $data['mosque_id'] = Mosque::current()->id;
        $data['created_by'] = $request->user()->id;

        if ($request->hasFile('poster')) {
            $data['poster_url'] = $media->store($request->file('poster'), 'posters');
        }

        StudySchedule::create($data);

        return back()->with('success', 'Jadwal kajian ditambahkan.');
    }

    public function update(Request $request, StudySchedule $study, MediaService $media)
    {
        $data = $this->validateData($request);

        if ($request->hasFile('poster')) {
            $data['poster_url'] = $media->store($request->file('poster'), 'posters');
        }

        $study->update($data);

        return back()->with('success', 'Jadwal kajian diperbarui.');
    }

    public function destroy(StudySchedule $study)
    {
        $study->delete();

        return back()->with('success', 'Jadwal kajian dihapus.');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'speaker' => ['required', 'string', 'max:255'],
            'scheduled_at' => ['required', 'date'],
            'location' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'status' => ['required', 'in:upcoming,ongoing,done,cancelled'],
            'poster' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
        ]);
    }
}
