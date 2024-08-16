<?php

namespace App\Http\Controllers\Api\User;

use App\User;
use App\Models\Review;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request, User $coach)
    {
        $request->validate([
            'review' => 'required|string|max:1000',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $review = Review::create([
            'user_id' => $coach->id,
            'reviewer_id' => Auth::id(),
            'review' => $request->review,
            'rating' => $request->rating,
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $review,
        ], 201);
    }

    // Update a review
    public function update(Request $request, Review $review)
    {
        $this->authorize('update', $review);

        $request->validate([
            'review' => 'required|string|max:1000',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $review->update($request->only(['review', 'rating']));

        return response()->json([
            'status' => 'success',
            'data' => $review,
        ], 200);
    }

    // Delete a review
    public function destroy(Review $review)
    {
        $this->authorize('delete', $review);

        $review->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Review deleted successfully',
        ], 200);
    }
}
