<?php

namespace App\Policies;

use App\Provide;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Contracts\Auth\Access\Gate;

class ProvidePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any provides.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }
    
		/**
		 * Determine whether the user can view the provide.
		 *
		 * @param  \App\User  $user
		 * @param  \App\Provide  $provide
		 * @return mixed
		 */
		public function view(User $user, Provide $provide)
		{
			//
            if(!$user->isAdmin()){
                return true;
            }
			return app(Gate::class)->authorize('financial.provide.view');
		}
	
    /**
     * Determine whether the user can view setting the provide.
     *
     * @param  \App\User  $user
     * @param  \App\Provide  $provide
     * @return mixed
     */
    public function viewSetting(User $user, Provide $provide)
    {
        //
        if(!$user->isAdmin()){
            return true;
        }
	    return app(Gate::class)->authorize('financial.provide.list');
    }

    /**
     * Determine whether the user can create provides.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
	    return app(Gate::class)->authorize('financial.provide.list');
    }

    /**
     * Determine whether the user can update the provide.
     *
     * @param  \App\User  $user
     * @param  \App\Provide  $provide
     * @return mixed
     */
    public function update(User $user, Provide $provide)
    {
        //
    }

    /**
     * Determine whether the user can delete the provide.
     *
     * @param  \App\User  $user
     * @param  \App\Provide  $provide
     * @return mixed
     */
    public function delete(User $user, Provide $provide)
    {
        //
    }

    /**
     * Determine whether the user can restore the provide.
     *
     * @param  \App\User  $user
     * @param  \App\Provide  $provide
     * @return mixed
     */
    public function restore(User $user, Provide $provide)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the provide.
     *
     * @param  \App\User  $user
     * @param  \App\Provide  $provide
     * @return mixed
     */
    public function forceDelete(User $user, Provide $provide)
    {
        //
    }
}
