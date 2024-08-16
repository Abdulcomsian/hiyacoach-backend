<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CoachOffering extends Model
{
    use HasFactory;

    protected $table = 'coach_offerings';
    protected $fillable = [
        'user_id',
        'category_id',
        'style',
        'price'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->hasOne(Category::class, 'id','category_id');
    }
}
