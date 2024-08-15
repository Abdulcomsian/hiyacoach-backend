<?php

namespace App\Http\Controllers\Api\Coach;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CoachingStyle;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class CoachingStyleController extends Controller
{
    public function storeOrUpdateStyle(Request $request, $styleId = null)
    {
        try {
            $validator = Validator::make($request->all(), [
                'style' => 'required|in:online,in_person,both'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed!',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $userId = Auth::user()->id;

            if ($styleId) {
                $coachingStyle = CoachingStyle::findOrFail($styleId);
                $coachingStyle->style = $request->input('style');
                $coachingStyle->save();
            } else {
                $coachingStyle = new CoachingStyle();
                $coachingStyle->user_id = $userId;
                $coachingStyle->style = $request->input('style');
                $coachingStyle->save();
            }

            return response()->json([
                'status' => 'success',
                'message' => $styleId ? 'Style updated successfully!' : 'Style created successfully!',
                'style' => $coachingStyle
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong!',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
