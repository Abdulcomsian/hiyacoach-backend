<?php

namespace App\Http\Controllers\Api\Coach;

use App\Models\Availability;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AvailabilityController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        $availability = Availability::create($request->all());

        return response()->json(['message' => 'Availability added successfully', 'availability' => $availability], 201);
    }

    // Update availability
    public function update(Request $request, Availability $availability)
    {
        $request->validate([
            'date' => 'sometimes|required|date',
            'start_time' => 'sometimes|required|date_format:H:i',
            'end_time' => 'sometimes|required|date_format:H:i|after:start_time',
        ]);

        $availability->update($request->only(['date', 'start_time', 'end_time']));

        return response()->json(['message' => 'Availability updated successfully', 'availability' => $availability], 200);
    }

    // View availability
    public function show($id)
    {
        $availability = Availability::with('coach')->findOrFail($id);
        return response()->json($availability);
    }

    // Delete availability
    public function destroy($id)
    {
        $availability = Availability::findOrFail($id);
        $availability->delete();

        return response()->json(['message' => 'Availability deleted successfully'], 200);
    }

    // List all availabilities for a specific coach
    public function index($coach_id)
    {
        $availabilities = Availability::where('coach_id', $coach_id)->get();
        return response()->json($availabilities);
    }
}
