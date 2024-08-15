<?php

namespace App\Http\Controllers\Api\Coach;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class FollowController extends Controller
{
    public function follow($userId)
    {
        try {
            $user = Auth::user();
            $user->follow($userId);

            return response()->json([
                'status' => 'success',
                'message' => 'User followed successfully!'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong!',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Unfollow a user
    public function unfollow($userId)
    {
        try {
            $user = Auth::user();
            $user->unfollow($userId);

            return response()->json([
                'status' => 'success',
                'message' => 'User unfollowed successfully!'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong!',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Check if the authenticated user is following another user
    public function isFollowing($userId)
    {
        try {
            $user = Auth::user();
            $isFollowing = $user->isFollowing($userId);

            return response()->json([
                'status' => 'success',
                'is_following' => $isFollowing
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong!',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
