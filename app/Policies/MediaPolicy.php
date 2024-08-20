<?php

namespace App\Policies;

use App\User;
use App\Models\Media;

class MediaPolicy
{
    public function update(User $user, Media $media)
    {
        // Check if the media belongs to the user
        return $user->id === $media->user_id;
    }

    public function view(User $user, Media $media)
    {
        // Check if the media belongs to the user
        return $user->id === $media->user_id;
    }

    public function delete(User $user, Media $media)
    {
        // Check if the media belongs to the user
        return $user->id === $media->user_id;
    }
}
