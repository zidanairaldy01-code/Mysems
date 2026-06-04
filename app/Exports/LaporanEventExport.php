<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class LaporanEventExport
{
    public static function formatRows(Collection $events, array $columns): array
    {
        return $events->map(function ($event) use ($columns) {
            $row = [];

            foreach ($columns as $column) {
                $row[$column] = self::formatField($event, $column);
            }

            return $row;
        })->toArray();
    }

    public static function formatField($event, string $column): string
    {
        return match ($column) {
            'nama_event' => $event->nama_event,
            'type' => $event->type === 'umum' ? 'Umum' : 'Turnamen',
            'penyelenggara' => optional($event->user)->nama ?? '-',
            'nama_panitia' => $event->nama_panitia,
            'lokasi' => $event->lokasi,
            'tanggal_event' => optional($event->tanggal_event ? \Carbon\Carbon::parse($event->tanggal_event) : null)->format('d M Y') ?? '-',
            'jam_event' => $event->jam_event ? \Carbon\Carbon::parse($event->jam_event)->format('H:i') . ' WIB' : '-',
            'harga_pendaftaran' => $event->harga_pendaftaran == 0 ? 'Gratis' : 'Rp ' . number_format($event->harga_pendaftaran, 0, ',', '.'),
            'slot_tim' => $event->slot_tim . ' Tim',
            'status' => self::statusLabel($event->status),
            default => (string) data_get($event, $column, ''),
        };
    }

    public static function columnLabels(): array
    {
        return [
            'nama_event' => 'Nama Event',
            'type' => 'Tipe Event',
            'penyelenggara' => 'Penyelenggara',
            'nama_panitia' => 'Nama Panitia',
            'lokasi' => 'Lokasi',
            'tanggal_event' => 'Tanggal',
            'jam_event' => 'Jam',
            'harga_pendaftaran' => 'Harga Pendaftaran',
            'slot_tim' => 'Slot Tim',
            'status' => 'Status',
        ];
    }

    public static function statusLabel(int $status): string
    {
        return match ($status) {
            0 => 'Pending',
            1 => 'Diterima',
            2 => 'Selesai',
            3 => 'Ditolak',
            4 => 'Dihapus',
            default => 'Tidak Diketahui',
        };
    }
}
