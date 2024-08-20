<?php

namespace App\Http\Controllers\Api\User;

use App\User;
use Exception;
use App\Models\FAQ;
use App\Models\Booking;
use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

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

    public function genderCount()
    {
        try {
            $maleCoachCount = User::role('coach')->where('gender', 'male')->count();
            $femaleCoachCount = User::role('coach')->where('gender', 'female')->count();
            $otherCoachCount = User::role('coach')->where('gender', 'other')->count();

            return response()->json([
                'status' => 'success',
                'data' => [
                    $maleCoachCount,
                    $femaleCoachCount,
                    $otherCoachCount,
                ],
            ], 200);
        } catch (Exception $e) {
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

    public function savedPosts()
    {
        try {
            $savedPosts = Favorite::where('user_id', auth()->user()->id)->with('post')->get();

            $savedPosts->each(function ($favorite) {
                $post = $favorite->post;
                if ($post) {
                    $post->images = json_decode($post->images, true);
                    if (is_array($post->images)) {
                        $post->images = array_map(fn($image) => url($image), $post->images);
                    }
                }
            });

            return response()->json([
                'status' => 'success',
                'data' => $savedPosts,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    public function deleteAccount(Request $request)
    {
        try {
            $userId = Auth::user()->id;
            $user = User::find($userId);

            DB::transaction(function () use ($user) {
                $user->posts()->delete();
                $user->reviews()->delete();
                $user->likes()->delete();
                $user->favorites()->delete();
                $user->delete();
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Your account has been successfully deleted.',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete your account. Please try again.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function onlineCoachingHub()
    {
        try {
            $bookings = Booking::where('user_id', Auth::id())
                ->with('coach')
                ->get();

            $bookings->each(function ($booking) {
                if ($booking->coach) {
                    $booking->coach->profile_picture = url($booking->coach->profile_picture);
                }
            });

            $coaches = $bookings->pluck('coach');

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

    public function allFaqs()
    {
        try {
            $faqs = FAQ::latest()->get();

            return response()->json([
                'status' => 'success',
                'data' => $faqs,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function BookedSessions()
    {
        try {
            $bookings = Booking::where('user_id', Auth::id())
                ->with('offering.category')
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $bookings,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
