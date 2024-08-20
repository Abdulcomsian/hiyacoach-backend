<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingPlan extends Model
{
    use HasFactory;

    protected $table = 'training_plans';

    protected $fillable = [
        'user_id',
        'coach_id',
        'file',
        'description'
    ];
}
