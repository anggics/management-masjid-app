<?php

namespace App\Http\Controllers;

use App\Models\FinancialRecord;
use App\Models\Mosque;
use App\Services\FinanceService;
use Illuminate\Http\Request;

class FinanceController extends Controller
{
    public function public(Request $request, FinanceService $finance)
    {
        $mosque = Mosque::current();
        $month = (int) $request->input('month', now()->month);
        $year = (int) $request->input('year', now()->year);

        $query = FinancialRecord::where('mosque_id', $mosque->id)
            ->whereYear('recorded_at', $year)
            ->whereMonth('recorded_at', $month);

        $summary = $finance->summary(clone $query);
        $records = (clone $query)->orderByDesc('recorded_at')->paginate(15)->withQueryString();

        // Saldo kas keseluruhan (semua waktu, tanpa filter tanggal) — F k.1.
        $totalBalance = $finance->summary(
            FinancialRecord::where('mosque_id', $mosque->id)
        )['balance'];

        return view('public.finance', [
            'mosque' => $mosque,
            'summary' => $summary,
            'totalBalance' => $totalBalance,
            'records' => $records,
            'month' => $month,
            'year' => $year,
        ]);
    }
}
