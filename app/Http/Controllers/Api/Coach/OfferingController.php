<?php

namespace App\Http\Controllers\Api\Coach;

use App\User;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CoachOffering;
use Illuminate\Support\Facades\Auth;

class OfferingController extends Controller
{
    public function storeOrUpdateOffering(Request $request, $offeringId = NULL)
    {
        try {
            $this->validate($request, [
                'category_id' => 'required|exists:categories,id',
                'price' => 'required|numeric',
            ]);

            $userId = Auth::user()->id;
            $user = User::findOrFail($userId);

            if ($offeringId) {
                $coachOffering = CoachOffering::findOrFail($offeringId);
                $coachOffering->category_id = $request->category_id;
                $coachOffering->price = $request->input("price");
                $coachOffering->update();
            } else {
                $coachOffering = new CoachOffering();
                $coachOffering->user_id = $user->id;
                $coachOffering->category_id = $request->category_id;
                $coachOffering->price = $request->input("price");
                $coachOffering->save();
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Location saved successfully!',
                'location' => $coachOffering
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong!',
            ], 500);
        }
    }
}
