<?php

namespace App\Policies;

use App\Models\DietPlan;
use App\User;
use Illuminate\Auth\Access\Response;

class DietPlanPolicy
{
    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, DietPlan $dietPlan)
    {
        return $user->id === $dietPlan->coach_id;
    }

    /**
     * Determine whether the user can delete the diet plan.
     */
    public function delete(User $user, DietPlan $dietPlan)
    {
        return $user->id === $dietPlan->coach_id;
    }
}
