<?php

namespace App\Policies;

use App\Bonus;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Contracts\Auth\Access\Gate;

class BonusPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any bonuses.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user,$bonus)
    {
        if($user->hasRole('admin')){
            return true;
        }
        
        if($user->erp_user_id != $bonus->erp_user_id){
            return app(Gate::class)->authorize('bonus.setting.list');
        }
    
            return true;
    }

    /**
     * Determine whether the user can view the bonus.
     *
     * @param  \App\User  $user
     * @param  \App\Bonus  $bonus
     * @return mixed
     */
    public function view(User $user, Bonus $bonus)
    {
        //
    }

    /**
     * Determine whether the user can create bonuses.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
	    return app(Gate::class)->authorize('bonus.setting.list');
    }

    /**
     * Determine whether the user can update the bonus.
     *
     * @param  \App\User  $user
     * @param  \App\Bonus  $bonus
     * @return mixed
     */
    public function update(User $user, Bonus $bonus)
    {
        //
    }

    /**
     * Determine whether the user can delete the bonus.
     *
     * @param  \App\User  $user
     * @param  \App\Bonus  $bonus
     * @return mixed
     */
    public function delete(User $user, Bonus $bonus)
    {
        //
    }

    /**
     * Determine whether the user can restore the bonus.
     *
     * @param  \App\User  $user
     * @param  \App\Bonus  $bonus
     * @return mixed
     */
    public function restore(User $user, Bonus $bonus)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the bonus.
     *
     * @param  \App\User  $user
     * @param  \App\Bonus  $bonus
     * @return mixed
     */
    public function forceDelete(User $user, Bonus $bonus)
    {
        //
    }
}
