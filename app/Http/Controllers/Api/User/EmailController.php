<?php

namespace App\Http\Controllers\Api\User;

use App\User;
use Exception;
use App\Mail\UserEmail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class EmailController extends Controller
{
    public function sendEmail(Request $request)
    {
        $validated = $request->validate([
            'to' => 'required|email',
            'subject' => 'required|string',
            'message' => 'required|string',
        ]);

        $userId = Auth::user()->id;
        $user = User::find($userId);

        try {
            Mail::to($validated['to'])->send(new UserEmail($validated['subject'], $validated['message'], $user));

            return response()->json([
                'status' => 'success',
                'message' => 'Email has been sent successfully.',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to send the email. Please try again.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
