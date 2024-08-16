<?php

namespace App\Http\Controllers\Api;

use App\User;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Validator;

class UserAuthController extends Controller
{
    public function register(Request $request, $usertype)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255|unique:users',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone_no' => 'required|string|max:15',
            'dob' => 'required|date',
            'gender' => 'required|in:male,female,other',
            'password' => 'required|string|min:8',
            'referral_code' => 'nullable|string|exists:users,referral_code',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'username' => $request->username,
            'name' => $request->name,
            'email' => $request->email,
            'phone_no' => $request->phone_no,
            'dob' => $request->dob,
            'gender' => $request->gender,
            'password' => Hash::make($request->password),
        ]);
        if($usertype == 'user'){
            $user->assignRole('user');
        } else {
            $user->assignRole('coach');
        }

        if ($request->filled('referral_code')) {
            $referrer = User::where('referral_code', $request->referral_code)->first();
            if ($referrer) {
                $user->referred_by = $referrer->id;
                $user->save();
            }
        }

        event(new Registered($user));

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user,
        ], 201);
    }

    public function login(Request $request)
    {
        try {
            $loginUserData = $request->validate([
                'email' => 'required|string|email',
                'password' => 'required|min:8'
            ]);

            $user = User::where('email', $loginUserData['email'])->first();

            if (!$user || !Hash::check($loginUserData['password'], $user->password)) {
                return response()->json([
                    'message' => 'Invalid Credentials'
                ], 401);
            }

            if (!$user->hasVerifiedEmail()) {
                return response()->json(['message' => 'Please verify your email address.'], 403);
            }

            $token = $user->createToken($user->name . '-AuthToken')->plainTextToken;

            return response()->json([
                "status" => "success",
                'data' => $user,
                'access_token' => $token,
                'token_type' => 'Bearer',
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                "status" => "error",
                'message' => 'Login failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function logout()
    {
        $user = User::findOrFail(Auth::user()->id);
        $user->tokens()->delete();

        return response()->json([
            "status" => "success",
            "message" => "logged out"
        ]);
    }
}
