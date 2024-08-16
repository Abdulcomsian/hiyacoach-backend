<?php

namespace App\Policies;

use App\User;
use App\Models\Media;

class MediaPolicy
{
    /**
     * Determine if the user can update the media.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Media  $media
     * @return bool
     */
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

    /**
     * Determine if the user can delete the media.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Media  $media
     * @return bool
     */
    public function delete(User $user, Media $media)
    {
        // Check if the media belongs to the user
        return $user->id === $media->user_id;
    }
}
