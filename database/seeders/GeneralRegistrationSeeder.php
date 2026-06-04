<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Registration;
use App\Models\Event;
use Illuminate\Support\Str;

class GeneralRegistrationSeeder extends Seeder
{
    /**
     * Jalankan seeder untuk mengisi data pendaftaran event umum.
     */
    public function run(): void
    {
        // Daftar nama peserta realistis
        $names = [
            'Ahmad Fauzi', 'Siti Aminah', 'Budi Santoso', 'Dewi Lestari', 'Eko Prasetyo',
            'Fitriani', 'Gilang Ramadhan', 'Hana Pertiwi', 'Indra Wijaya', 'Joko Susilo',
            'Kartika Sari', 'Lukman Hakim', 'Maya Indah', 'Nanda Saputra', 'Olivia Putri',
            'Putu Gede', 'Rina Marlina', 'Sakti Wirawan', 'Taufik Hidayat', 'Utami Putri'
        ];

        // Ambil event UMUM yang aktif (status 1)
        $events = Event::where('status', 1)->where('type', 'umum')->get();

        if ($events->isEmpty()) {
            $this->command->info('Tidak ada event UMUM aktif. Silakan buat event umum terlebih dahulu.');
            return;
        }

        foreach ($events as $event) {
            // Hitung peserta yang sudah terdaftar
            $currentCount = Registration::where('event_id', $event->id)->count();
            $maxCount = $event->slot_tim ?? 50; // Untuk umum slot_tim dianggap kuota peserta
            $needed = $maxCount - $currentCount;

            // Jika sudah penuh atau ingin mengisi sebagian saja (misal max 20 per seed)
            $toFill = min($needed, 20);

            if ($toFill <= 0) {
                $this->command->warn("Event Umum: [{$event->nama_event}] sudah penuh ({$currentCount}/{$maxCount}).");
                continue;
            }
            
            $this->command->info("Mengisi {$toFill} peserta umum baru untuk event: {$event->nama_event}");

            for ($i = 1; $i <= $toFill; $i++) {
                $name = $names[array_rand($names)];
                $qrCode = 'QR-' . strtoupper(Str::random(10));

                Registration::create([
                    'event_id' => $event->id,
                    'nama_tim' => $name, // Untuk umum nama_tim adalah nama peserta
                    'nama_kapten' => $name,
                    'email' => strtolower(str_replace(' ', '.', $name)) . rand(1, 99) . '@example.com',
                    'nomor_wa' => '08' . rand(11, 59) . rand(10000000, 99999999),
                    'qr_code' => $qrCode,
                    'status' => 1,
                    'payment_status' => 'settlement',
                    'attended_at' => (rand(0, 1) ? now() : null), // Acak ada yang sudah hadir
                ]);
            }
        }

        $this->command->info('Seeding pendaftaran umum berhasil diselesaikan!');
    }
}
