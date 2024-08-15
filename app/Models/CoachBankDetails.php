<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoachBankDetails extends Model
{
    use HasFactory;

    protected $table = 'coach_bank_details';
    protected $fillable = [
        "user_id",
        'bank_name',
        'account_name',
        'account_number',
        'short_code',
        'country',
    ];
}
