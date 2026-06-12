<?php

namespace App\Http\Controllers;

use App\Models\Mosque;
use App\Models\StudySchedule;
use Illuminate\Http\Request;

class StudyController extends Controller
{
    public function public(Request $request)
    {
        $mosque = Mosque::current();
        $filter = $request->input('filter', 'upcoming');

        $query = StudySchedule::where('mosque_id', $mosque->id);

        if ($filter === 'past') {
            $query->whereIn('status', ['done', 'cancelled'])->orderByDesc('scheduled_at');
        } else {
            $query->whereIn('status', ['upcoming', 'ongoing'])->orderBy('scheduled_at');
        }

        return view('public.study', [
            'mosque' => $mosque,
            'schedules' => $query->paginate(12)->withQueryString(),
            'filter' => $filter,
        ]);
    }

    public function show(StudySchedule $study)
    {
        return view('public.study-detail', [
            'study' => $study,
            'mosque' => Mosque::current(),
        ]);
    }
}
