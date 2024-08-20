<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CheckInPhoto extends Model
{
    use HasFactory;

    protected $table = 'check_in_photos';

    protected $fillable = [
        'user_id',
        'coach_id',
        'file',
        'description'
    ];
}
