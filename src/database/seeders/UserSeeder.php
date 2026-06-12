<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['name' => 'Admin DKM', 'email' => 'admin@masjid.test', 'role' => 'admin', 'whatsapp' => '081200000001'],
            ['name' => 'Staff Sekretariat', 'email' => 'staff@masjid.test', 'role' => 'staff', 'whatsapp' => '081200000002'],
            ['name' => 'Budi Jamaah', 'email' => 'user@masjid.test', 'role' => 'user', 'whatsapp' => '081200000003'],
        ];

        foreach ($users as $u) {
            User::updateOrCreate(
                ['email' => $u['email']],
                [
                    'name' => $u['name'],
                    'role' => $u['role'],
                    'whatsapp' => $u['whatsapp'],
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ],
            );
        }
    }
}
