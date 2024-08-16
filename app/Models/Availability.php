<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Availability extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'days',
        'time'
    ];

    // Define the relationship to the Coach model
    public function coach()
    {
        return $this->belongsTo(User::class);
    }
}
