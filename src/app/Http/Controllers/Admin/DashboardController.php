<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FinancialRecord;
use App\Models\Mosque;
use App\Models\QurbanDeposit;
use App\Models\QurbanParticipant;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $mosque = Mosque::current();

        $income = FinancialRecord::where('mosque_id', $mosque->id)->where('type', 'income')->sum('amount');
        $expense = FinancialRecord::where('mosque_id', $mosque->id)->where('type', 'expense')->sum('amount');

        return view('admin.dashboard', [
            'mosque' => $mosque,
            'stats' => [
                'jamaah' => User::where('role', 'user')->count(),
                'qurban' => QurbanParticipant::where('mosque_id', $mosque->id)->count(),
                'pending_deposits' => QurbanDeposit::pending()->count(),
                'balance' => (float) $income - (float) $expense,
            ],
            'pending' => QurbanDeposit::pending()->with(['participant', 'user'])->latest()->limit(5)->get(),
        ]);
    }
}
