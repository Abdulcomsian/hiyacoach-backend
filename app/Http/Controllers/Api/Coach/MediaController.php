<?php

namespace App\Http\Controllers\Api\Coach;

use Exception;
use App\Models\Media;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class MediaController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'required|mimes:mp4,mov,avi,flv|max:102400',
            'type' => 'required|in:free,paid',
            'amount' => 'nullable|numeric|min:0',
        ]);

        if ($request->hasFile('file')) {
            if ($request->file && File::exists(public_path($request->file))) {
                File::delete(public_path($request->file));
            }

            $directory = 'coach_videos/' . Auth::id();
            $directoryPath = public_path($directory);

            if (!File::exists($directoryPath)) {
                File::makeDirectory($directoryPath, 0755, true);
            }

            $file = $request->file('file');
            $fileName = time() . '.' . $file->getClientOriginalExtension();
            $file->move($directoryPath, $fileName);

            $path = $directory . '/' . $fileName;
        }

        $media = Media::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
            'path' => $path,
            'type' => $request->type,
            'amount' => $request->type === 'paid' ? $request->amount : null,
        ]);

        return response()->json(['message' => 'Video uploaded successfully', 'media' => $media], 201);
    }


    public function index()
    {
        $medias = Media::where('user_id', Auth::id())->get();

        $medias->each(function ($media) {
            $media->path = url($media->path);
        });

        return response()->json($medias);
    }

    public function show($mediaId)
    {
        try {
            $media = Media::findOrFail($mediaId);

            $this->authorize('view', $media);

            $media->path = url($media->path);

            return response()->json([
                'status' => 'success',
                'data' => $media,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Media not found or unauthorized.',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, Media $media)
    {
        $this->authorize('update', $media);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'nullable|mimes:mp4,mov,avi,flv|max:102400',
            'type' => 'required|in:free,paid',
            'amount' => 'nullable|numeric|min:0',
        ]);

        $media->update([
            'title' => $request->title,
            'description' => $request->description,
            'type' => $request->type,
            'amount' => $request->type === 'paid' ? $request->amount : null,
        ]);

        if ($request->hasFile('file')) {
            $oldFilePath = public_path($media->video_path);
            if (File::exists($oldFilePath)) {
                File::delete($oldFilePath);
            }

            $directory = 'coach_videos/' . Auth::id();
            $directoryPath = public_path($directory);

            if (!File::exists($directoryPath)) {
                File::makeDirectory($directoryPath, 0755, true);
            }

            $file = $request->file('file');
            $fileName = time() . '.' . $file->getClientOriginalExtension();
            $file->move($directoryPath, $fileName);

            $path = $directory . '/' . $fileName;
            $media->update(['path' => $path]);
        }

        return response()->json(['message' => 'Video updated successfully', 'media' => $media], 200);
    }


    public function destroy(Media $media)
    {
        $this->authorize('delete', $media);

        $filePath = public_path($media->path);

        if (File::exists($filePath)) {
            File::delete($filePath);
        }

        $media->delete();

        return response()->json(['message' => 'Video deleted successfully'], 200);
    }
}
