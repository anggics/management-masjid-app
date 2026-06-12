<?php

namespace Database\Seeders;

use App\Models\FinancialRecord;
use App\Models\Mosque;
use App\Models\PaymentMethod;
use App\Models\QurbanCommittee;
use App\Models\QurbanParticipant;
use App\Models\QurbanType;
use App\Models\QurbanYear;
use App\Models\StudySchedule;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $mosque = Mosque::current();
        $admin = User::where('role', 'admin')->first();
        $user = User::where('role', 'user')->first();

        // Metode pembayaran
        PaymentMethod::updateOrCreate(
            ['mosque_id' => $mosque->id, 'label' => 'QRIS Masjid'],
            ['type' => 'qris', 'is_active' => true, 'sort_order' => 0],
        );
        PaymentMethod::updateOrCreate(
            ['mosque_id' => $mosque->id, 'label' => 'BCA - Masjid Al-Hidayah'],
            [
                'type' => 'bank_transfer',
                'bank_name' => 'BCA',
                'account_number' => '1234567890',
                'account_name' => 'DKM Mushola',
                'is_active' => true,
                'sort_order' => 1,
            ],
        );

        // Kajian
        StudySchedule::updateOrCreate(
            ['mosque_id' => $mosque->id, 'title' => 'Kajian Tafsir Al-Quran'],
            [
                'speaker' => 'Ustadz Hasan',
                'scheduled_at' => now()->addDays(3)->setTime(19, 30),
                'location' => 'Ruang Utama Masjid',
                'description' => 'Kajian rutin tafsir Al-Quran ba\'da Isya.',
                'status' => 'upcoming',
                'created_by' => $admin?->id,
            ],
        );

        // Keuangan
        FinancialRecord::updateOrCreate(
            ['mosque_id' => $mosque->id, 'category' => 'Infaq Jumat', 'recorded_at' => now()->startOfMonth()->toDateString()],
            ['type' => 'income', 'amount' => 5000000, 'description' => 'Infaq jamaah Jumat', 'recorded_by' => $admin?->id],
        );
        FinancialRecord::updateOrCreate(
            ['mosque_id' => $mosque->id, 'category' => 'Listrik & Air', 'recorded_at' => now()->startOfMonth()->addDays(2)->toDateString()],
            ['type' => 'expense', 'amount' => 750000, 'description' => 'Tagihan bulanan', 'recorded_by' => $admin?->id],
        );

        // Master jenis hewan qurban
        $kambing = QurbanType::updateOrCreate(
            ['mosque_id' => $mosque->id, 'animal_type' => 'kambing', 'share_type' => 'individu'],
            ['target_amount' => 2000000, 'is_active' => true],
        );
        QurbanType::updateOrCreate(
            ['mosque_id' => $mosque->id, 'animal_type' => 'sapi', 'share_type' => 'group'],
            ['target_amount' => 3500000, 'is_active' => true],
        );
        QurbanType::updateOrCreate(
            ['mosque_id' => $mosque->id, 'animal_type' => 'sapi', 'share_type' => 'individu'],
            ['target_amount' => 25000000, 'is_active' => true],
        );

        // Master tahun qurban (hijriah)
        $year = QurbanYear::updateOrCreate(
            ['mosque_id' => $mosque->id, 'hijri_year' => 1447],
            ['is_active' => true],
        );
        QurbanYear::updateOrCreate(
            ['mosque_id' => $mosque->id, 'hijri_year' => 1446],
            ['is_active' => true],
        );

        // Panitia qurban
        QurbanCommittee::updateOrCreate(
            ['mosque_id' => $mosque->id, 'name' => 'Panitia Qurban 1447 H'],
            ['address' => 'Sekretariat Masjid', 'whatsapp' => '081234567890'],
        );

        // Metode pembayaran tipe rekening qurban (tampil di tabungan user)
        PaymentMethod::updateOrCreate(
            ['mosque_id' => $mosque->id, 'label' => 'Rekening Qurban BSI'],
            [
                'type' => 'rekening_qurban',
                'bank_name' => 'BSI',
                'account_number' => '9876543210',
                'account_name' => 'Panitia Qurban Masjid',
                'is_active' => true,
                'sort_order' => 0,
            ],
        );

        // Qurban
        QurbanParticipant::updateOrCreate(
            ['mosque_id' => $mosque->id, 'user_id' => $user?->id, 'name' => 'Tabungan Qurban Budi'],
            [
                'qurban_type_id' => $kambing->id,
                'qurban_year_id' => $year->id,
                'animal_type' => $kambing->animal_type,
                'target_amount' => $kambing->target_amount,
                'collected_amount' => 500000,
                'status' => 'active',
            ],
        );
    }
}
