<?php

namespace App\Http\Controllers\Api\User;

use App\User;
use Exception;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class TrainingController extends Controller
{
    public function getTraining($coachId)
    {
        try {
            $coach = User::findOrFail($coachId);

            $trainings = $coach->offerings()->with('category')->get();

            $availabilities = $coach->availabilities()->get();

            $availabilities->each(function ($availability) {
                if ($availability->days) {
                    $availability->days = json_decode($availability->days, true);
                }
            });

            return response()->json([
                'status' => 'success',
                'data' => [
                    'trainings' => $trainings,
                    'availabilities' => $availabilities,
                ],
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve training details.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function bookTraining(Request $request, $coachId)
    {
        $validated = $request->validate([
            'offering_id' => 'required|exists:coach_offerings,id',
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
            'amount' => 'required|numeric|min:0',
            'payment_status' => 'required|in:pending,completed,canceled',
            'status' => 'required|in:scheduled,completed,canceled'
        ]);

        try {
            $existingBooking = Booking::where('user_id', Auth::id())
                ->where('coach_id', $coachId)
                ->where('date', $validated['date'])
                ->first();

            if ($existingBooking) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Booking already exists for this date and coach.',
                ], 400);
            }
            DB::transaction(function () use ($validated, $coachId) {

                Booking::create([
                    'user_id' => Auth::user()->id,
                    'coach_id' => $coachId,
                    'offering_id' => $validated['offering_id'],
                    'date' => $validated['date'],
                    'time' => $validated['time'],
                    'amount' => $validated['amount'],
                    'payment_status' => $validated['payment_status'],
                    'status' => $validated['status']
                ]);
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Training has been successfully booked.',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to book the training. Please try again.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getUpcomingSessions()
    {
        try {
            $userId = Auth::id();

            $upcomingSessions = Booking::where('user_id', $userId)
                ->where('date', '>', now()->format('Y-m-d'))
                ->orWhere(function ($query) {
                    $query->where('date', '=', now()->format('Y-m-d'))
                        ->where('time', '>', now()->format('H:i'));
                })
                ->with('coach', 'offering')
                ->orderBy('date', 'asc')
                ->orderBy('time', 'asc')
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $upcomingSessions
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve upcoming sessions.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getPastSessions()
    {
        try {
            $userId = Auth::id();

            $upcomingSessions = Booking::where('user_id', $userId)
                ->where('date', '<', now()->format('Y-m-d'))
                ->orWhere(function ($query) {
                    $query->where('date', '=', now()->format('Y-m-d'))
                        ->where('time', '<', now()->format('H:i'));
                })
                ->with('coach', 'offering')
                ->orderBy('date', 'asc')
                ->orderBy('time', 'asc')
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $upcomingSessions
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve upcoming sessions.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
