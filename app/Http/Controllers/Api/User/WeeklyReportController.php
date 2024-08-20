<?php

namespace App\Http\Controllers\Api\User;

use App\Models\WeeklyReport;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class WeeklyReportController extends Controller
{
    public function store(Request $request, $coachId)
    {
        try {
            $validatedData = $request->validate([
                'date' => 'required|date',
                'start_time' => 'required|date_format:H:i',
                'end_time' => 'required|date_format:H:i|after:start_time',
                'description' => 'nullable|string',
            ]);

            $weeklyReport = WeeklyReport::create([
                'user_id' => Auth::id(),
                'coach_id' => $coachId,
                'date' => $validatedData['date'],
                'start_time' => $validatedData['start_time'],
                'end_time' => $validatedData['end_time'],
                'description' => $validatedData['description'],
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Weekly report created successfully!',
                'data' => $weeklyReport,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create weekly report. Please try again.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validatedData = $request->validate([
                'date' => 'nullable|date',
                'start_time' => 'nullable|date_format:H:i',
                'end_time' => 'nullable|date_format:H:i|after:start_time',
                'description' => 'nullable|string',
            ]);

            $weeklyReport = WeeklyReport::findOrFail($id);

            if (Gate::denies('update', $weeklyReport)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You are not authorized to delete this report.',
                ], 403);
            }

            if ($request->has('date')) {
                $weeklyReport->date = $validatedData['date'];
            }

            if ($request->has('start_time')) {
                $weeklyReport->start_time = $validatedData['start_time'];
            }

            if ($request->has('end_time')) {
                $weeklyReport->end_time = $validatedData['end_time'];
            }

            if ($request->has('description')) {
                $weeklyReport->description = $validatedData['description'];
            }

            $weeklyReport->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Weekly report updated successfully!',
                'data' => $weeklyReport,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update weekly report. Please try again.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $weeklyReport = WeeklyReport::findOrFail($id);

            if (Gate::denies('delete', $weeklyReport)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You are not authorized to delete this report.',
                ], 403);
            }

            $weeklyReport->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Weekly report deleted successfully!',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete weekly report. Please try again.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
