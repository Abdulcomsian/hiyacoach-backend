<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Setting extends Model
{
    use HasFactory;

    protected $table = 'settings';
    protected $fillable = [
        'user_id', 'push_notification', 'email_marketing', 'language', 'privacy_statement', 'general_terms_and_conditions',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
