<?php

namespace App\Services;

use App\Models\QurbanDeposit;
use App\Models\QurbanParticipant;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class QurbanService
{
    public function __construct(private NotificationService $notifications)
    {
    }

    /**
     * User mengirim bukti transfer (status: pending) lalu admin/staff dinotifikasi.
     */
    public function submitDeposit(QurbanParticipant $participant, User $user, array $data): QurbanDeposit
    {
        $deposit = QurbanDeposit::create([
            'participant_id' => $participant->id,
            'user_id' => $user->id,
            'amount' => $data['amount'],
            'proof_image_url' => $data['proof_image_url'],
            'notes' => $data['notes'] ?? null,
            'status' => 'pending',
        ]);

        $admins = User::whereIn('role', ['admin', 'staff'])->get();
        $this->notifications->notifyMany(
            $admins,
            'deposit_submitted',
            'Bukti transfer qurban baru',
            "{$user->name} mengirim setoran sebesar Rp ".number_format((float) $data['amount'], 0, ',', '.'),
            ['deposit_id' => $deposit->id, 'participant_id' => $participant->id],
        );

        return $deposit;
    }

    /**
     * Admin/staff memverifikasi setoran — update saldo secara atomik.
     */
    public function verifyDeposit(QurbanDeposit $deposit, User $verifier): QurbanDeposit
    {
        return DB::transaction(function () use ($deposit, $verifier) {
            if ($deposit->status === 'verified') {
                return $deposit;
            }

            $deposit->update([
                'status' => 'verified',
                'verified_by' => $verifier->id,
                'verified_at' => now(),
                'rejection_reason' => null,
            ]);

            $participant = $deposit->participant()->lockForUpdate()->first();
            $participant->increment('collected_amount', $deposit->amount);

            if ((float) $participant->collected_amount >= (float) $participant->target_amount
                && (float) $participant->target_amount > 0) {
                $participant->update(['status' => 'completed']);
            }

            if ($deposit->user) {
                $this->notifications->notify(
                    $deposit->user,
                    'deposit_verified',
                    'Setoran qurban diverifikasi',
                    'Setoran Anda sebesar Rp '.number_format((float) $deposit->amount, 0, ',', '.').' telah diverifikasi.',
                    ['deposit_id' => $deposit->id],
                );
            }

            return $deposit;
        });
    }

    public function rejectDeposit(QurbanDeposit $deposit, User $verifier, string $reason): QurbanDeposit
    {
        $deposit->update([
            'status' => 'rejected',
            'verified_by' => $verifier->id,
            'verified_at' => now(),
            'rejection_reason' => $reason,
        ]);

        if ($deposit->user) {
            $this->notifications->notify(
                $deposit->user,
                'deposit_rejected',
                'Setoran qurban ditolak',
                "Setoran Anda ditolak. Alasan: {$reason}",
                ['deposit_id' => $deposit->id],
            );
        }

        return $deposit;
    }
}
