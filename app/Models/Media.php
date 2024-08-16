<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Media extends Model
{
    use HasFactory;

    protected $table = 'medias';
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'path',
        'type',
        'amount'
    ];

    public function coach()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
