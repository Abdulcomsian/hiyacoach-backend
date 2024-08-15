<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Like extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_id',
        'user_id',
    ];

    // A like belongs to a post
    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    // A like belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
