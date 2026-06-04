<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Registration extends Model
{
    protected $fillable = [
        'event_id',
        'user_id',
        'nama_tim',
        'nama_kapten',
        'anggota_tim',
        'nomor_wa',
        'email',
        'qr_code',
        'status',
        'snap_token',
        'redirect_url',
        'payment_status',
        'attended_at',
        'order_id',
    ];

    protected $casts = [
        'attended_at' => 'datetime',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
