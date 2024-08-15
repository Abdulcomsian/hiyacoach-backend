<?php

namespace App\Http\Controllers\Api;

use App\User;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    public function redirectToGoogle()
    {
        $redirectUrl = Socialite::driver('google')->stateless()->redirect()->getTargetUrl();
        return response()->json(['url' => $redirectUrl]);
    }

    public function handleGoogleCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
            $user = User::where('email', $googleUser->email)->first();

            if ($user) {
                $user->update([
                    'name' => $googleUser->name,
                ]);

                Auth::login($user);
            } else {
                $username = $this->generateUniqueUsername($googleUser->name);

                $user = User::create([
                    'username' => $username,
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'password' => Hash::make($googleUser->email),
                ]);
                if ($request->has('role')) {
                    $user->assignRole($request->input('role'));
                } else {
                    // Default role assignment if no role is provided
                    $user->assignRole('user');
                }
            }

            Auth::login($user, true);

            event(new Registered($user));

            if (!$user->hasVerifiedEmail()) {
                return response()->json(['message' => 'User Registered Successfully, Please verify your email address.'], 403);
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                "status" => "success",
                'message' => 'User logged in successfully',
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $user,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                "status" => "error",
                'message' => 'Failed to login with Google',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    private function generateUniqueUsername($name)
    {
        $baseUsername = strtolower(preg_replace('/\s+/', '_', $name));
        $username = $baseUsername;

        $counter = 1;

        while (User::where('username', $username)->exists()) {
            $username = $baseUsername . $counter;
            $counter++;
        }

        return $username;
    }
}
