<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FinancialRecord;
use App\Models\Mosque;
use App\Services\FinanceService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class FinanceController extends Controller
{
    public function index(Request $request, FinanceService $finance)
    {
        [$query, $period] = $this->filteredQuery($request);

        // Saldo kas keseluruhan (semua waktu, tanpa filter tanggal) — F e.1.
        $allTime = FinancialRecord::where('mosque_id', Mosque::current()->id);

        return view('admin.finance', [
            'mosque' => Mosque::current(),
            'summary' => $finance->summary(clone $query),
            'totalBalance' => $finance->summary($allTime)['balance'],
            'records' => (clone $query)->orderByDesc('recorded_at')->paginate(20)->withQueryString(),
            'period' => $period,
        ]);
    }

    public function store(Request $request, FinanceService $finance)
    {
        $data = $this->validateData($request);
        $data['mosque_id'] = Mosque::current()->id;
        $data['recorded_by'] = $request->user()->id;

        $finance->create($data);

        return back()->with('success', 'Catatan keuangan ditambahkan.');
    }

    public function update(Request $request, FinancialRecord $record, FinanceService $finance)
    {
        $finance->update($record, $this->validateData($request));

        return back()->with('success', 'Catatan keuangan diperbarui.');
    }

    public function destroy(FinancialRecord $record, FinanceService $finance)
    {
        $finance->delete($record);

        return back()->with('success', 'Catatan keuangan dihapus (tercatat di audit log).');
    }

    public function export(Request $request, FinanceService $finance)
    {
        [$query, $period] = $this->filteredQuery($request);

        $records = (clone $query)->orderByDesc('recorded_at')->get();
        $summary = $finance->summary(clone $query);

        $pdf = Pdf::loadView('admin.finance-pdf', [
            'mosque' => Mosque::current(),
            'records' => $records,
            'summary' => $summary,
            'period' => $period,
        ]);

        return $pdf->download('laporan-keuangan-'.$period['year'].'-'.$period['month'].'.pdf');
    }

    private function filteredQuery(Request $request): array
    {
        $month = (int) $request->input('month', now()->month);
        $year = (int) $request->input('year', now()->year);

        $query = FinancialRecord::where('mosque_id', Mosque::current()->id)
            ->whereYear('recorded_at', $year)
            ->whereMonth('recorded_at', $month);

        return [$query, ['month' => $month, 'year' => $year]];
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'type' => ['required', 'in:income,expense'],
            'category' => ['required', 'string', 'max:120'],
            'amount' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string', 'max:1000'],
            'recorded_at' => ['required', 'date'],
        ]);
    }
}
