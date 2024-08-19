<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function getSettings()
    {
        try {
            $user = auth()->user();

            $setting = Setting::where('user_id', $user->id)->first();

            if (!$setting) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Settings not found.',
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => $setting,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    public function createOrUpdate(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'push_notification' => 'required|boolean',
                'email_marketing' => 'required|boolean',
                'language' => 'required|string|max:5',
                'privacy_statement' => 'nullable|string',
                'general_terms_and_conditions' => 'nullable|string',
            ]);

            $user = auth()->user();

            $setting = Setting::updateOrCreate(
                ['user_id' => $user->id],
                $validatedData
            );

            return response()->json([
                'status' => 'success',
                'data' => $setting,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
