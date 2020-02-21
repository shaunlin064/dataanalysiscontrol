<?php

namespace App\Policies;

use App\SaleGroups;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Contracts\Auth\Access\Gate;

class SaleGroupsPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any sale groups.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the sale groups.
     *
     * @param  \App\User  $user
     * @param  \App\SaleGroups  $saleGroups
     * @return mixed
     */
    public function view(User $user, SaleGroups $saleGroups)
    {
        //
        if(!$user->isAdmin()){
            return true;
        }
        return app(Gate::class)->authorize('saleGroup.setting.view');
    }

    /**
     * Determine whether the user can create sale groups.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
        if(!$user->isAdmin()){
            return true;
        }
        return app(Gate::class)->authorize('saleGroup.setting.list');
    }

    /**
     * Determine whether the user can update the sale groups.
     *
     * @param  \App\User  $user
     * @param  \App\SaleGroups  $saleGroups
     * @return mixed
     */
    public function update(User $user, SaleGroups $saleGroups)
    {
        //
    }

    /**
     * Determine whether the user can delete the sale groups.
     *
     * @param  \App\User  $user
     * @param  \App\SaleGroups  $saleGroups
     * @return mixed
     */
    public function delete(User $user, SaleGroups $saleGroups)
    {
        //
    }

    /**
     * Determine whether the user can restore the sale groups.
     *
     * @param  \App\User  $user
     * @param  \App\SaleGroups  $saleGroups
     * @return mixed
     */
    public function restore(User $user, SaleGroups $saleGroups)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the sale groups.
     *
     * @param  \App\User  $user
     * @param  \App\SaleGroups  $saleGroups
     * @return mixed
     */
    public function forceDelete(User $user, SaleGroups $saleGroups)
    {
        //
    }
}
