<?php

namespace App\Http\Controllers\Api\Coach;

use Exception;
use App\Models\DietPlan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class DietPlanController extends Controller
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
                $file->move(public_path('diet_plans'), $fileName);

                $validatedData['file'] = 'diet_plans/' . $fileName;
            }

            $dietPlan = DietPlan::create([
                'user_id' => $userId,
                'coach_id' => Auth::id(),
                'file' => $validatedData['file'],
                'description' => $validatedData['description']
            ]);

            $dietPlan->file = url($dietPlan->file);

            return response()->json([
                'status' => 'success',
                'message' => 'Diet plan created successfully!',
                'data' => $dietPlan,
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

            $dietPlan = DietPlan::findOrFail($id);

            if (Gate::denies('update', $dietPlan)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You are not authorized to update this diet plan.',
                ], 403);
            }

            if ($request->hasFile('file')) {
                if (file_exists(public_path($dietPlan->file))) {
                    unlink(public_path($dietPlan->file));
                }

                $file = $request->file('file');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('diet_plans'), $fileName);

                $validatedData['file'] = 'diet_plans/' . $fileName;
            }

            $dietPlan->file = $validatedData['file'] ?? $dietPlan->file;
            $dietPlan->description = $validatedData['description'] ?? $dietPlan->description;
            $dietPlan->update();

            $dietPlan->file = url($dietPlan->file);

            return response()->json([
                'status' => 'success',
                'message' => 'Diet plan updated successfully!',
                'data' => $dietPlan,
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
            $dietPlan = DietPlan::findOrFail($id);

            if (Gate::denies('delete', $dietPlan)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You are not authorized to delete this diet plan.',
                ], 403);
            }

            if (file_exists(public_path($dietPlan->file))) {
                unlink(public_path($dietPlan->file));
            }

            $dietPlan->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Diet plan deleted successfully!',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete diet plan. Please try again.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
