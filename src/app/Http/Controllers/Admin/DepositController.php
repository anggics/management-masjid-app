<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QurbanDeposit;
use App\Services\QurbanService;
use Illuminate\Http\Request;

class DepositController extends Controller
{
    public function index()
    {
        return view('admin.deposits', [
            'pending' => QurbanDeposit::pending()
                ->with(['participant', 'user'])
                ->latest()
                ->paginate(20),
        ]);
    }

    public function verify(QurbanDeposit $deposit, Request $request, QurbanService $qurban)
    {
        $qurban->verifyDeposit($deposit, $request->user());

        return back()->with('success', 'Setoran diverifikasi dan saldo tabungan diperbarui.');
    }

    public function reject(QurbanDeposit $deposit, Request $request, QurbanService $qurban)
    {
        $data = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:500'],
        ]);

        $qurban->rejectDeposit($deposit, $request->user(), $data['rejection_reason']);

        return back()->with('success', 'Setoran ditolak.');
    }
}
