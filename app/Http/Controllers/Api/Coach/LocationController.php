<?php

namespace App\Http\Controllers\Api\Coach;

use App\User;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CoachLocation;
use Illuminate\Support\Facades\Auth;

class LocationController extends Controller
{
    public function storeOrUpdateLocation(Request $request)
    {
        try {
            $this->validate($request, [
                "name" => 'required|string',
            ]);

            $userId = Auth::user()->id;
            $user = User::findOrFail($userId);

            if (CoachLocation::where("user_id", $userId)->exists()) {
                $location = CoachLocation::where("user_id", $userId)->first();
                $location->name = $request->input("name");
                $location->update();
            } else {
                $location = new CoachLocation();
                $location->user_id = $user->id;
                $location->name = $request->input("name");
                $location->save();
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Location saved successfully!',
                'location' => $location
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong!',
            ], 500);
        }
    }
}
