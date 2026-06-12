<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mosque;
use App\Models\PaymentMethod;
use App\Services\MediaService;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    public function index()
    {
        $mosque = Mosque::current();

        return view('admin.payments', [
            'mosque' => $mosque,
            'methods' => $mosque->paymentMethods()->orderBy('sort_order')->get(),
        ]);
    }

    public function store(Request $request, MediaService $media)
    {
        $data = $this->validateData($request);

        if ($request->hasFile('qris_image')) {
            $data['qris_image_url'] = $media->store($request->file('qris_image'), 'qris');
        }

        $data['mosque_id'] = Mosque::current()->id;
        PaymentMethod::create($data);

        return back()->with('success', 'Metode pembayaran ditambahkan.');
    }

    public function update(Request $request, PaymentMethod $method, MediaService $media)
    {
        $data = $this->validateData($request);

        if ($request->hasFile('qris_image')) {
            $data['qris_image_url'] = $media->store($request->file('qris_image'), 'qris');
        }

        $method->update($data);

        return back()->with('success', 'Metode pembayaran diperbarui.');
    }

    public function destroy(PaymentMethod $method)
    {
        $method->delete();

        return back()->with('success', 'Metode pembayaran dihapus.');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'type' => ['required', 'in:qris,bank_transfer,rekening_qurban'],
            'label' => ['required', 'string', 'max:255'],
            'bank_name' => ['nullable', 'string', 'max:120'],
            'account_number' => ['nullable', 'string', 'max:60'],
            'account_name' => ['nullable', 'string', 'max:120'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:127'],
            'qris_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
        ]);
    }
}
