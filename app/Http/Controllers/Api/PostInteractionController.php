<?php

namespace App\Http\Controllers\Api;

use App\Models\Like;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Favorite;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PostInteractionController extends Controller
{
    public function likePost($postId)
    {
        $post = Post::findOrFail($postId);

        $like = Like::firstOrCreate([
            'user_id' => Auth::id(),
            'post_id' => $post->id
        ]);

        return response()->json(['message' => 'Post liked successfully', 'like' => $like], 201);
    }

    // Comment on a post
    public function commentOnPost(Request $request, $postId)
    {
        $request->validate([
            'comment' => 'required|string|max:255',
        ]);

        $post = Post::findOrFail($postId);

        $comment = Comment::create([
            'user_id' => Auth::id(),
            'post_id' => $post->id,
            'comment' => $request->comment,
        ]);

        return response()->json(['message' => 'Comment added successfully', 'comment' => $comment], 201);
    }

    // Favorite a post
    public function favoritePost($postId)
    {
        $post = Post::findOrFail($postId);

        $favorite = Favorite::firstOrCreate([
            'user_id' => Auth::id(),
            'post_id' => $post->id
        ]);

        return response()->json(['message' => 'Post favorited successfully', 'favorite' => $favorite], 201);
    }

    // Unlike a post
    public function unlikePost($postId)
    {
        $like = Like::where([
            'user_id' => Auth::id(),
            'post_id' => $postId
        ])->firstOrFail();

        $like->delete();

        return response()->json(['message' => 'Post unliked successfully'], 200);
    }

    // Unfavorite a post
    public function unfavoritePost($postId)
    {
        $favorite = Favorite::where([
            'user_id' => Auth::id(),
            'post_id' => $postId
        ])->firstOrFail();

        $favorite->delete();

        return response()->json(['message' => 'Post unfavorited successfully'], 200);
    }
}
