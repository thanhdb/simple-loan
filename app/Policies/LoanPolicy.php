<?php

namespace App\Policies;

use App\Models\Loan;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LoanPolicy
{
    use HandlesAuthorization;

    /**
     * Make sure admin can do anything
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function before($user, $ability)
    {
        if ($user->hasRole('admin')) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the model.
     * Only the user who owns the loan can view it
     * @param  \App\Models\User  $user
     * @param  \App\Models\Loan  $loan
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Loan $loan)
    {
        return $user->id === $loan->user_id;
    }

}