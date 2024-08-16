<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Review extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'reviewer_id', 'review', 'rating'];

    public function coach()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relationship to the reviewer (user)
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
}
