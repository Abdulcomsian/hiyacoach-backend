<?php

namespace App\Http\Controllers\Api\Coach;

use App\Models\Availability;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Support\Facades\Auth;

class AvailabilityController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|string|max:255',
            'days' => 'required_if:type,custom|array',
            'days.*' => 'in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'time' => 'required|date_format:H:i',
        ]);

        $days = $request->type === 'custom' ? $request->days : ($request->type === 'monday_to_friday' ? ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'] : null);

        $existingAvailability = Availability::where('user_id', Auth::id())
                    ->where('type', $request->type)
                    ->first();

                if ($existingAvailability) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Availability already exists in this type.',
                    ], 400);
                }

        $availability = Availability::create([
            'user_id' => Auth::id(),
            'type' => $request->type,
            'days' => $days ? json_encode($days) : null,
            'time' => $request->time,
        ]);

        return response()->json(['message' => 'Availability added successfully', 'availability' => $availability], 201);
    }

    // Update availability
    public function update(Request $request, Availability $availability)
    {
        $request->validate([
            'type' => 'required|string|max:255',
            'days' => 'required_if:type,custom|array',
            'days.*' => 'in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'time' => 'required|date_format:H:i',
        ]);

        $days = $request->type === 'custom' ? $request->days : ($request->type === 'monday_to_friday' ? ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'] : null);

        $availability->type = $request->type;
        $availability->days = $days ? json_encode($days) : null;
        $availability->time = $request->time;
        $availability->save();

        return response()->json(['message' => 'Availability updated successfully', 'availability' => $availability], 200);
    }

    // View availability
    public function show($id)
    {
        try {
            $availability = Availability::where('user_id', Auth::id())
                ->with('coach')
                ->findOrFail($id);

            if ($availability->days) {
                $availability->days = json_decode($availability->days, true);
            }
            return response()->json([
                'status' => 'success',
                'data' => $availability,
            ], 200);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 404);
        }
    }

    // Delete availability
    public function destroy($id)
    {
        try {
            $availability = Availability::where('user_id', Auth::id())->findOrFail($id);
            $availability->delete();

            return response()->json(['status' => 'success', 'message' => 'Availability deleted successfully'], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // List all availabilities for a specific coach
    public function index()
    {
        try {
            $availabilities = Availability::where('user_id', Auth::id())->get();

            $availabilities->each(function ($availability) {
                if ($availability->days) {
                    $availability->days = json_decode($availability->days, true);
                }
            });

            return response()->json([
                'status' => 'success',
                'data' => $availabilities,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
