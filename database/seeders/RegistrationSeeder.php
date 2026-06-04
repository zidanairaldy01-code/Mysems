<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Registration;
use App\Models\Event;
use Illuminate\Support\Str;

class RegistrationSeeder extends Seeder
{
    /**
     * Jalankan seeder untuk mengisi data pendaftaran dummy.
     */
    public function run(): void
    {
        // Daftar nama tim yang lebih realistis
        $teamNames = [
            'Garuda', 'Elang Laut', 'Harimau Malaya', 'Banteng Muda', 'Singa Atlas',
            'Srigala Langit', 'Naga Hitam', 'Badak Perkasa', 'Komodo Esports', 'Hiu Kencana',
            'Rajawali Putih', 'Gajah Mada', 'Maung Bandung', 'Kancil Sakti', 'Kera Ngalam',
            'Paus Biru', 'Serigala Gurun', 'Macan Kemayoran', 'Beruang Madu', 'Jaguar'
        ];

        // Ambil event turnamen yang aktif (status 1)
        $events = Event::where('status', 1)
            ->where(function($q) {
                $q->where('type', 'tournament')->orWhereNull('type');
            })
            ->get();

        if ($events->isEmpty()) {
            $this->command->info('Tidak ada event aktif. Silakan buat event terlebih dahulu.');
            return;
        }

        foreach ($events as $event) {
            // Hitung tim yang sudah terdaftar
            $currentCount = Registration::where('event_id', $event->id)->count();
            $maxCount = $event->slot_tim ?? 8;
            $needed = $maxCount - $currentCount;

            if ($needed <= 0) {
                $this->command->warn("Event: [{$event->nama_event}] sudah penuh ({$currentCount}/{$maxCount}). Melewati...");
                continue;
            }
            
            $this->command->info("Mengisi {$needed} pendaftar baru untuk event: {$event->nama_event} (Slot: {$maxCount})");

            for ($i = 1; $i <= $needed; $i++) {
                // Ambil nama tim dari daftar secara acak dan tambahkan suffix agar unik
                $baseName = $teamNames[array_rand($teamNames)];
                $finalTeamName = $baseName . ' ' . Str::upper(Str::random(3));

                Registration::create([
                    'event_id' => $event->id,
                    'nama_tim' => $finalTeamName,
                    'nama_kapten' => 'Kapten ' . ($currentCount + $i),
                    'nomor_wa' => '0812' . rand(10000000, 99999999),
                    'status' => 1, // Diterima
                    'payment_status' => 'settlement', // Lunas
                ]);
            }
        }

        $this->command->info('Seeding pendaftaran berhasil diselesaikan!');
    }
}
