<?php

namespace App\Http\Controllers\Api\User;

use App\User;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function allCoaches()
    {
        try {
            $coaches = User::role('coach')
                ->with(['offerings.category', 'reviews'])
                ->get();

            $coaches->each(function ($coach) {
                $coach->profile_picture = url($coach->profile_picture);

                $averageRating = $coach->reviews->avg('rating');
                $reviewCount = $coach->reviews->count();

                $coach->rating_review = sprintf("%.1f(%d)", $averageRating ?? 0, $reviewCount);
            });

            return response()->json([
                'status' => 'success',
                'data' => $coaches,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function filterCoaches(Request $request)
    {
        try {
            $gender = $request->query('gender');
            $coachingStyle = $request->query('coaching_style');

            $query = User::role('coach')->with(['offerings.category', 'reviews']);

            if ($gender) {
                $query->where('gender', $gender);
            }

            if ($coachingStyle) {
                $query->whereHas('offerings', function ($q) use ($coachingStyle) {
                    $q->where('style', $coachingStyle);
                });
            }

            $coaches = $query->get();

            if ($coaches->isEmpty()) {
                return response()->json([
                    'status' => 'no_results',
                    'message' => 'No coaches found with the specified criteria.',
                ], 200);
            }

            $coaches->each(function ($coach) {
                $coach->profile_picture = url($coach->profile_picture);

                $averageRating = $coach->reviews->avg('rating');
                $reviewCount = $coach->reviews->count();

                $coach->rating_review = sprintf("%.1f(%d)", $averageRating ?? 0, $reviewCount);
            });

            return response()->json([
                'status' => 'success',
                'data' => $coaches,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function coachGallery($coachId, $type)
    {
        try {
            $coach = User::findOrFail($coachId);

            $coach->load(['offerings.category', 'reviews']);
            $coach->profile_picture = url($coach->profile_picture);
            $averageRating = $coach->reviews->avg('rating');
            $reviewCount = $coach->reviews->count();
            $coach->rating_review = sprintf("%.1f(%d)", $averageRating ?? 0, $reviewCount);

            if ($type == 'gallery') {
                $coach->load('posts');

                $coach->posts->each(function ($post) {
                    $post->images = json_decode($post->images, true);
                    if (is_array($post->images)) {
                        $post->images = array_map(fn($image) => url($image), $post->images);
                    }
                });
            }

            return response()->json([
                'status' => 'success',
                'data' => $coach,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
