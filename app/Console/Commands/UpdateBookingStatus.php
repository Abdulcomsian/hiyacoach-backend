<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Booking;
use Illuminate\Console\Command;

class UpdateBookingStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'booking:update-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the status of bookings where the date and time have passed';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();

        $updatedBookings = Booking::where('date', '<', $now->format('Y-m-d'))
            ->orWhere(function ($query) use ($now) {
                $query->where('date', '=', $now->format('Y-m-d'))
                    ->where('time', '<', $now->format('H:i:s'));
            })
            ->where('status', '!=', 'completed')
            ->update(['status' => 'completed']);

        $this->info("Updated {$updatedBookings} booking(s) to completed.");
    }
}
