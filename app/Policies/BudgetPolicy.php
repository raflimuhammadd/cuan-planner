<?php

namespace App\Policies;

use App\Models\Budget;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BudgetPolicy
{
    public function update(User $user, Budget $budget): Response {
        return $user->id === $budget->user_id
            ? Response::allow()
            : Response::denyAsNotFound('Anda tidak memiliki akses untuk mengubah anggaran ini.');
    }

    public function delete(User $user, Budget $budget): Response {
        return $user->id === $budget->user_id
            ? Response::allow()
            : Response::denyAsNotFound('Anda tidak memiliki akses untuk mengubah anggaran ini.');
    }

}
