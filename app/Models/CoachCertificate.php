<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoachCertificate extends Model
{
    use HasFactory;

    protected $table = 'coach_certificates';

    protected $fillable = [
        'user_id',
        'file'
    ];
}
