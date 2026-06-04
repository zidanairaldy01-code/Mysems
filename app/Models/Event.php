<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'nama_event',
        'type',
        'school_id',
        'foto_event',
        'tanggal_event',
        'jam_event',
        'nama_panitia',
        'harga_pendaftaran',
        'slot_tim',
        'lokasi',
        'deskripsi',
        'user_id',
        'status',
        'alasan_ditolak',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }

    public function getTypeNormalizedAttribute(): string
    {
        $type = Str::of($this->type ?? 'tournament')->trim()->lower();
        return $type->contains('umum') ? 'umum' : 'tournament';
    }

    public function getTypeLabelAttribute(): string
    {
        return $this->type_normalized === 'umum' ? 'Event Umum' : 'Turnamen Sekolah';
    }

    public function getTypeBadgeClassAttribute(): string
    {
        return $this->type_normalized === 'umum' ? 'bg-info text-dark' : 'bg-primary';
    }
}
