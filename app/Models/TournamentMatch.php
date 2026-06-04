<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TournamentMatch extends Model
{
    use HasFactory;

    protected $table = 'matches';

    protected $fillable = [
        'event_id',
        'round',
        'match_number',
        'team1_name',
        'team2_name',
        'team1_score',
        'team2_score',
    ];
}
