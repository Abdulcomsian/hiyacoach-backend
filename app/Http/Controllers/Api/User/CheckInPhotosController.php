<?php

namespace App\Http\Controllers\Api\User;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CheckInPhoto;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class CheckInPhotosController extends Controller
{
    public function store(Request $request, $coachId)
    {
        try {
            $validatedData = $request->validate([
                'file' => 'required|image|mimes:jpg,jpeg,png,gif|max:2048',
                'description' => 'nullable|string',
            ]);

            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('check_in_photos'), $fileName);

                $validatedData['file'] = 'check_in_photos/' . $fileName;
            }

            $checkInPhoto = CheckInPhoto::create([
                'user_id' => Auth::id(),
                'coach_id' => $coachId,
                'file' => $validatedData['file'],
                'description' => $validatedData['description']
            ]);

            $checkInPhoto->file = url($checkInPhoto->file);

            return response()->json([
                'status' => 'success',
                'message' => 'Diet plan created successfully!',
                'data' => $checkInPhoto,
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
                'file' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
                'description' => 'nullable|string',
            ]);

            $checkInPhoto = CheckInPhoto::findOrFail($id);

            if (Gate::denies('update', $checkInPhoto)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You are not authorized to delete this photo.',
                ], 403);
            }

            if ($request->hasFile('file')) {
                if (file_exists(public_path($checkInPhoto->file))) {
                    unlink(public_path($checkInPhoto->file));
                }

                $file = $request->file('file');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('check_in_photos'), $fileName);

                $validatedData['file'] = 'check_in_photos/' . $fileName;
            }

            $checkInPhoto->file = $validatedData['file'] ?? $checkInPhoto->file;
            $checkInPhoto->description = $validatedData['description'] ?? $checkInPhoto->description;
            $checkInPhoto->update();

            $checkInPhoto->file = url($checkInPhoto->file);

            return response()->json([
                'status' => 'success',
                'message' => 'Photo updated successfully!',
                'data' => $checkInPhoto,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update diet photo. Please try again.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $CheckInPhoto = CheckInPhoto::findOrFail($id);

            if (Gate::denies('delete', $CheckInPhoto)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You are not authorized to delete this photo.',
                ], 403);
            }

            if (Gate::denies('delete', $CheckInPhoto)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You are not authorized to delete this photo.',
                ], 403);
            }

            if (file_exists(public_path($CheckInPhoto->file))) {
                unlink(public_path($CheckInPhoto->file));
            }

            $CheckInPhoto->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Check In Photo deleted successfully!',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete Check In Photo. Please try again.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
