<?php

namespace App\Http\Controllers\Api;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function profile()
    {
        try {
            $user = User::findOrFail(Auth::user()->id);

            if ($user->hasRole('coach')) {
                $user->posts = $user->posts()->get()->each(function ($post) {
                    $post->images = json_decode($post->images, true);
                    $post->images = array_map(function ($image) {
                        return url($image);
                    }, $post->images);
                });

                $user->posts_count = $user->posts()->count();
                $user->followers_count = $user->followers()->count();
                $user->following_count = $user->following()->count();
            }

            $user->profile_picture = $user->profile_picture ? url($user->profile_picture) : null;

            return response()->json([
                "status" => "success",
                "message" => "User retrieved successfully!",
                'data' => $user,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "status" => "error",
                "message" => "Something went wrong!",
                "error" => $e->getMessage(),
            ], 500);
        }
    }

    public function updateProfile(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'username' => 'sometimes|required|string|max:255|unique:users',
                'name' => 'sometimes|required|string|max:255',
                'email' => 'sometimes|required|string|email|max:255|unique:users',
                'phone_no' => 'sometimes|required|string|max:15',
                'dob' => 'sometimes|required|date',
                'password' => 'sometimes|required|string|min:8',
                'profile_picture' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors(),
                ], 422);
            }

            $user = User::find(Auth::user()->id);

            $user->username = $request->input('username') ?? $user->username;
            $user->name = $request->input('name') ?? $user->name;
            $user->phone_no = $request->input('phone_no') ?? $user->phone_no;
            $user->dob = $request->input('dob') ?? $user->dob;

            if ($request->filled('password')) {
                $user->password = Hash::make($request->input('password'));
            }

            if ($request->hasFile('profile_picture')) {
                if ($user->profile_picture && File::exists(public_path($user->profile_picture))) {
                    File::delete(public_path($user->profile_picture));
                }

                $directory = 'images/profile_pictures';
                $directoryPath = public_path($directory);

                if (!File::exists($directoryPath)) {
                    File::makeDirectory($directoryPath, 0755, true);
                }

                $file = $request->file('profile_picture');
                $fileName = time() . '.' . $file->getClientOriginalExtension();
                $file->move($directoryPath, $fileName);

                $user->profile_picture = $directory . '/' . $fileName;
            }

            $user->save();

            $user->profile_picture_url = $user->profile_picture ? url($user->profile_picture) : null;

            return response()->json([
                "status" => "success",
                'message' => 'Profile updated successfully!',
                'user' => $user,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "status" => "error",
                'message' => 'An error occurred while updating the profile.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
