<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoachingStyle extends Model
{
    use HasFactory;

    protected $table = 'coaching_styles';

    protected $fillable = [
        'user_id',
        'style'
    ];
}
