<?php

namespace App\Http\Controllers\Api\Coach;

use App\User;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CoachBankDetails;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class BankController extends Controller
{
    public function storeOrUpdateBank(Request $request, $bankId = NULL)
    {
        try {
            $validator = Validator::make($request->all(), [
                'bank_name' => 'required|string|max:255',
                'account_name' => 'required|string|max:255',
                'account_no' => 'required|string|max:255',
                'short_code' => 'required|string|max:255',
                'country' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed!',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $userId = Auth::user()->id;
            $user = User::findOrFail($userId);

            if ($bankId) {
                $coachBank = CoachBankDetails::findOrFail($bankId);
                $coachBank->bank_name = $request->input('bank_name');
                $coachBank->account_name = $request->input('account_name');
                $coachBank->account_number = $request->input('account_no');
                $coachBank->short_code = $request->input('short_code');
                $coachBank->country = $request->input('country');
                $coachBank->save();
            } else {
                $coachBank = new CoachBankDetails();
                $coachBank->user_id = $user->id;
                $coachBank->bank_name = $request->input('bank_name');
                $coachBank->account_name = $request->input('account_name');
                $coachBank->account_number = $request->input('account_no');
                $coachBank->short_code = $request->input('short_code');
                $coachBank->country = $request->input('country');
                $coachBank->save();
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Location saved successfully!',
                'location' => $coachBank
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong!',
            ], 500);
        }
    }
}
