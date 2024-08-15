<?php

namespace App\Http\Controllers\Api\Coach;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class PostController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $post = new Post();
        $post->user_id = Auth::id();
        $post->title = $request->title;
        $post->content = $request->content;
        $images = [];

        if ($request->hasFile('images')) {
            $files = $request->file('images');
            $directory = 'images/posts';
            $directoryPath = public_path($directory);

            if (!File::exists($directoryPath)) {
                File::makeDirectory($directoryPath, 0755, true);
            }

            foreach ($files as $file) {
                if ($file->isValid()) {
                    $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->move($directoryPath, $fileName);

                    $images[] = $directory . '/' . $fileName;
                }
            }

            $post->images = json_encode($images, JSON_UNESCAPED_SLASHES);
        }

        $post->save();

        return response()->json(['message' => 'Post created successfully', 'post' => $post], 201);
    }

    public function index()
    {
        $posts = Post::with('coach')->where('user_id', Auth::id())->get();

        $posts->each(function ($post) {
            $post->images = json_decode($post->images, true) ?? [];

            $post->images = array_map(function ($image) {
                return asset($image);
            }, $post->images);

            if ($post->coach) {
                $post->coach->profile_picture = asset($post->coach->profile_picture);
            }
        });

        return response()->json($posts);
    }


    public function show($id)
    {
        $post = Post::findOrFail($id);

        $images = json_decode($post->images, true);

        $post->images = array_map(function ($image) {
            return asset($image);
        }, $images);

        return response()->json(['post' => $post]);
    }

    public function update(Request $request, Post $post)
    {
        $this->authorize('update', $post);

        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'content' => 'sometimes|required|string',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_published' => 'sometimes|boolean',
        ]);


        $post->update($request->only(['title', 'content', 'is_published']));

        if ($request->hasFile('images')) {
            $files = $request->file('images');
            $directory = 'images/posts';
            $directoryPath = public_path($directory);

            if ($post->images && File::exists(public_path($post->images))) {
                File::delete(public_path($post->images));
            }

            if (!File::exists($directoryPath)) {
                File::makeDirectory($directoryPath, 0755, true);
            }

            foreach ($files as $file) {
                if ($file->isValid()) {
                    $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->move($directoryPath, $fileName);

                    $images[] = $directory . '/' . $fileName;
                }
            }

            $post->images = json_encode($images, JSON_UNESCAPED_SLASHES);
        }

        $post->update();

        return response()->json(['message' => 'Post updated successfully', 'post' => $post], 201);
    }

    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);
        $post->delete();

        return response()->json(['message' => 'Post deleted successfully'], 201);
    }
}
