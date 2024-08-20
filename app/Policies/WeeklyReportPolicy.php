<?php

namespace App\Policies;

use App\Models\WeeklyReport;
use App\User;
use Illuminate\Auth\Access\Response;

class WeeklyReportPolicy
{

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, WeeklyReport $weeklyReport): bool
    {
        return $user->id === $weeklyReport->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, WeeklyReport $weeklyReport): bool
    {
        return $user->id === $weeklyReport->user_id;
    }
}
