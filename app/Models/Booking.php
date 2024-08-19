<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Booking extends Model
{
    use HasFactory;

    protected $table = 'bookings';
    protected $fillable = [
        'user_id',
        'coach_id',
        'offering_id',
        'date',
        'time',
        'amount',
        'payment_status',
        'status',
    ];


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * The coach associated with the booking.
     */
    public function coach()
    {
        return $this->belongsTo(User::class, 'coach_id');
    }

    /**
     * The offering associated with the booking.
     */
    public function offering()
    {
        return $this->belongsTo(CoachOffering::class, 'offering_id');
    }
}
