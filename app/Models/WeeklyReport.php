<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeeklyReport extends Model
{
    use HasFactory;

    protected $table = 'weekly_reports';

    protected $fillable = [
        'user_id',
        'coach_id',
        'date',
        'start_time',
        'end_time',
        'description'
    ];
}
