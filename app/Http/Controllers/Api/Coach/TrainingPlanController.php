<?php

namespace App\Http\Controllers\Api\Coach;

use Exception;
use App\Models\TrainingPlan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class TrainingPlanController extends Controller
{
    public function store(Request $request, $userId)
    {
        try {
            $validatedData = $request->validate([
                'file' => 'required|mimes:pdf|max:2048',
                'description' => 'nullable|string',
            ]);

            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('training_plans'), $fileName);

                $validatedData['file'] = 'training_plans/' . $fileName;
            }

            $trainingPlan = TrainingPlan::create([
                'user_id' => $userId,
                'coach_id' => Auth::id(),
                'file' => $validatedData['file'],
                'description' => $validatedData['description']
            ]);

            $trainingPlan->file = url($trainingPlan->file);

            return response()->json([
                'status' => 'success',
                'message' => 'Diet plan created successfully!',
                'data' => $trainingPlan,
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create diet plan. Please try again.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validatedData = $request->validate([
                'file' => 'nullable|mimes:pdf|max:2048',
                'description' => 'nullable|string',
            ]);

            $trainingPlan = TrainingPlan::findOrFail($id);

            if (Gate::denies('update', $trainingPlan)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You are not authorized to delete this diet plan.',
                ], 403);
            }

            if ($request->hasFile('file')) {
                if (file_exists(public_path($trainingPlan->file))) {
                    unlink(public_path($trainingPlan->file));
                }

                $file = $request->file('file');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('training_plans'), $fileName);

                $validatedData['file'] = 'training_plans/' . $fileName;
            }

            $trainingPlan->file = $validatedData['file'] ?? $trainingPlan->file;
            $trainingPlan->description = $validatedData['description'] ?? $trainingPlan->description;
            $trainingPlan->update();

            $trainingPlan->file = url($trainingPlan->file);

            return response()->json([
                'status' => 'success',
                'message' => 'Diet plan updated successfully!',
                'data' => $trainingPlan,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update diet plan. Please try again.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $trainingPlan = TrainingPlan::findOrFail($id);

            if (Gate::denies('update', $trainingPlan)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You are not authorized to delete this training plan.',
                ], 403);
            }

            if (file_exists(public_path($trainingPlan->file))) {
                unlink(public_path($trainingPlan->file));
            }

            $trainingPlan->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Training plan deleted successfully!',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete training plan. Please try again.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
