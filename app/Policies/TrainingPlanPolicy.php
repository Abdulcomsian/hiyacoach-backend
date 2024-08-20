<?php

namespace App\Policies;

use App\Models\TrainingPlan;
use App\User;
use Illuminate\Auth\Access\Response;

class TrainingPlanPolicy
{
    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TrainingPlan $trainingPlan)
    {
        return $user->id === $trainingPlan->coach_id;
    }

    /**
     * Determine whether the user can delete the diet plan.
     */
    public function delete(User $user, TrainingPlan $trainingPlan)
    {
        return $user->id === $trainingPlan->coach_id;
    }
}
