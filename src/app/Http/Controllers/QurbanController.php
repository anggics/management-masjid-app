<?php

namespace App\Http\Controllers;

use App\Models\Mosque;
use App\Models\PaymentMethod;
use App\Models\QurbanDeposit;
use App\Models\QurbanParticipant;
use App\Models\QurbanType;
use App\Services\MediaService;
use App\Services\QurbanService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class QurbanController extends Controller
{
    /** Daftar peserta qurban — publik (F-USR-11). */
    public function index()
    {
        $mosque = Mosque::current();

        $participants = QurbanParticipant::where('mosque_id', $mosque->id)
            ->whereIn('status', ['active', 'completed'])
            ->with('qurbanType')
            ->orderByDesc('created_at')
            ->paginate(12);

        return view('public.qurban', [
            'mosque' => $mosque,
            'participants' => $participants,
        ]);
    }

    /** Halaman tabungan milik user (butuh login) — F-USR-16. */
    public function mine(Request $request)
    {
        $user = $request->user();

        $participants = QurbanParticipant::where('user_id', $user->id)
            ->with('deposits', 'qurbanType')
            ->orderByDesc('created_at')
            ->get();

        $mosque = Mosque::current();

        return view('user.qurban', [
            'user' => $user,
            'mosque' => $mosque,
            'participants' => $participants,
            'methods' => PaymentMethod::where('mosque_id', $mosque->id)->active()->get(),
            'types' => $mosque->qurbanTypes()->active()->latest()->get(),
            // Tahun hijriah (F m) — user memilih sendiri tahun qurbannya.
            'years' => $mosque->qurbanYears()->active()->orderByDesc('hijri_year')->get(),
            // Rekening qurban (F i.3) — diambil dari metode pembayaran tipe rekening_qurban.
            'qurbanAccounts' => PaymentMethod::where('mosque_id', $mosque->id)
                ->where('type', 'rekening_qurban')->active()
                ->orderBy('sort_order')->get(),
        ]);
    }

    /** User mengubah label tabungannya sendiri — F-USR-16 (hanya nama label). */
    public function updateLabel(Request $request, QurbanParticipant $participant)
    {
        abort_unless($participant->user_id === $request->user()->id, 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $participant->update(['name' => $data['name']]);

        return back()->with('success', 'Label tabungan diperbarui.');
    }

    /** User mendaftar sebagai peserta qurban — F-USR-13. */
    public function register(Request $request)
    {
        $mosque = Mosque::current();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'qurban_type_id' => ['required', Rule::exists('qurban_types', 'id')->where('mosque_id', $mosque->id)->where('is_active', true)],
            // Tahun hijriah dipilih sendiri oleh user (F m).
            'qurban_year_id' => ['required', Rule::exists('qurban_years', 'id')->where('mosque_id', $mosque->id)->where('is_active', true)],
        ]);

        // Jenis hewan & target diambil dari master jenis yang dipilih user.
        $type = QurbanType::findOrFail($data['qurban_type_id']);

        QurbanParticipant::create([
            'mosque_id' => $mosque->id,
            'user_id' => $request->user()->id,
            'qurban_type_id' => $type->id,
            'qurban_year_id' => $data['qurban_year_id'],
            'name' => $data['name'],
            'animal_type' => $type->animal_type,
            'target_amount' => $type->target_amount,
            'collected_amount' => 0,
            'status' => 'active',
        ]);

        return back()->with('success', 'Pendaftaran qurban berhasil. Silakan mulai menabung.');
    }

    /** Upload bukti transfer setoran — F-USR-14. */
    public function deposit(Request $request, QurbanParticipant $participant, MediaService $media, QurbanService $qurban)
    {
        abort_unless($participant->user_id === $request->user()->id, 403);

        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
            'notes' => ['nullable', 'string', 'max:500'],
            'proof' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
        ]);

        $url = $media->store($request->file('proof'), 'qurban-proofs');

        $qurban->submitDeposit($participant, $request->user(), [
            'amount' => $data['amount'],
            'notes' => $data['notes'] ?? null,
            'proof_image_url' => $url,
        ]);

        return back()->with('success', 'Bukti transfer terkirim. Menunggu verifikasi admin.');
    }
}
