<?php

namespace App\Services;

use App\Models\AppNotification;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Kirim notifikasi in-app + (opsional) FCM push ke satu user.
     */
    public function notify(User $user, string $type, string $title, string $body, array $data = []): void
    {
        AppNotification::create([
            'user_id' => $user->id,
            'type' => $type,
            'title' => $title,
            'body' => $body,
            'data' => $data,
        ]);

        $this->pushFcm($user, $title, $body, $data);
    }

    /**
     * Kirim ke banyak user sekaligus (mis. semua admin & staff).
     *
     * @param  Collection<int,User>  $users
     */
    public function notifyMany(Collection $users, string $type, string $title, string $body, array $data = []): void
    {
        $users->each(fn (User $u) => $this->notify($u, $type, $title, $body, $data));
    }

    private function pushFcm(User $user, string $title, string $body, array $data): void
    {
        $serverKey = config('services.fcm.server_key');

        if (! $serverKey || ! $user->fcm_token) {
            return; // Fallback ke in-app saja
        }

        try {
            Http::withToken($serverKey)
                ->timeout(5)
                ->post('https://fcm.googleapis.com/fcm/send', [
                    'to' => $user->fcm_token,
                    'notification' => ['title' => $title, 'body' => $body],
                    'data' => $data,
                ]);
        } catch (\Throwable $e) {
            Log::warning('FCM push gagal: '.$e->getMessage());
        }
    }
}
