<?php

namespace App\Policies;

use App\Models\Goal;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class GoalPolicy
{
    public function view(User $user, Goal $goal): Response
    {
        return $user->id === $goal->user_id
            ? Response::allow()
            : Response::denyAsNotFound('Anda tidak memiliki akses untuk melihat tujuan ini.');
    }


    public function create(User $user, Goal $goal): Response
    {
        return $user->id === $goal->user_id
            ? Response::allow()
            : Response::denyAsNotFound('Anda tidak memiliki akses untuk melihat tujuan ini.');
    }


    public function update(User $user, Goal $goal): Response
    {
        return $user->id === $goal->user_id
            ? Response::allow()
            : Response::denyAsNotFound('Anda tidak memiliki akses untuk melihat tujuan ini.');
    }

    public function delete(User $user, Goal $goal): Response
    {
        return $user->id === $goal->user_id
            ? Response::allow()
            : Response::denyAsNotFound('Anda tidak memiliki akses untuk melihat tujuan ini.');
    }
}
