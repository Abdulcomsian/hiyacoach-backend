<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoachLocation extends Model
{
    use HasFactory;
    protected $table = 'coach_locations';
    protected $fillable = [
        'user_id',
        'name'
    ];
}
