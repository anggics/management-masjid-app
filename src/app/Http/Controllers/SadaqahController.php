<?php

namespace App\Http\Controllers;

use App\Models\Mosque;
use App\Models\PaymentMethod;

class SadaqahController extends Controller
{
    public function show()
    {
        $mosque = Mosque::current();

        // Rekening qurban tidak ditampilkan di halaman sedekah (F l.1).
        $methods = PaymentMethod::where('mosque_id', $mosque->id)
            ->active()
            ->where('type', '!=', 'rekening_qurban')
            ->orderBy('sort_order')
            ->get();

        return view('public.sadaqah', [
            'mosque' => $mosque,
            'methods' => $methods,
        ]);
    }
}
