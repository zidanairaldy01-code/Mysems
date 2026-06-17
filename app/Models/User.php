<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama',
        'email',
        'password',
        'role',
        'status',
        'hp',
        'alamat',
        'foto',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function registrations()
    {
        return $this->hasMany(Registration::class, 'user_id');
    }

    public function getEventParticipationStatusAttribute(): string
    {
        $registrations = $this->registrations;

        if ($registrations->isEmpty()) {
            return 'Belum Mengikuti Event';
        }

        // Cek apakah ada event yang didaftar oleh user ini yang belum selesai
        $hasUnfinishedEvent = $registrations->contains(function ($reg) {
            $event = $reg->event;
            return $event && $event->status != 2 && $event->status != 4 && $event->tanggal_event >= date('Y-m-d');
        });

        if ($hasUnfinishedEvent) {
            return 'Belum Mengikuti Event';
        }

        return 'Sudah Mengikuti Event';
    }
}
