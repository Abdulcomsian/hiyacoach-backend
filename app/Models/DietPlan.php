<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DietPlan extends Model
{
    use HasFactory;

    protected $table = 'diet_plans';

    protected $fillable = [
        'user_id',
        'coach_id',
        'file',
        'description'
    ];
}
