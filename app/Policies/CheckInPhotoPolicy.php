<?php

namespace App\Policies;

use App\Models\CheckInPhoto;
use App\User;
use Illuminate\Auth\Access\Response;

class CheckInPhotoPolicy
{
    public function update(User $user, CheckInPhoto $checkInPhoto)
    {
        return $user->id === $checkInPhoto->user_id;
    }

    /**
     * Determine whether the user can delete the diet plan.
     */
    public function delete(User $user, CheckInPhoto $checkInPhoto)
    {
        return $user->id === $checkInPhoto->user_id;
    }
}
