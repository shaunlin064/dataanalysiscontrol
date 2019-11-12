<?php

namespace App\Policies;

use App\FinancialList;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class FinancialListPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any financial lists.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
    
    }

    /**
     * Determine whether the user can view the financial list.
     *
     * @param  \App\User  $user
     * @param  \App\FinancialList  $financialList
     * @return mixed
     */
    public function view(User $user, FinancialList $financialList)
    {
        //
    }

    /**
     * Determine whether the user can create financial lists.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the financial list.
     *
     * @param  \App\User  $user
     * @param  \App\FinancialList  $financialList
     * @return mixed
     */
    public function update(User $user, FinancialList $financialList)
    {
        //
    }

    /**
     * Determine whether the user can delete the financial list.
     *
     * @param  \App\User  $user
     * @param  \App\FinancialList  $financialList
     * @return mixed
     */
    public function delete(User $user, FinancialList $financialList)
    {
        //
    }

    /**
     * Determine whether the user can restore the financial list.
     *
     * @param  \App\User  $user
     * @param  \App\FinancialList  $financialList
     * @return mixed
     */
    public function restore(User $user, FinancialList $financialList)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the financial list.
     *
     * @param  \App\User  $user
     * @param  \App\FinancialList  $financialList
     * @return mixed
     */
    public function forceDelete(User $user, FinancialList $financialList)
    {
        //
    }
}
