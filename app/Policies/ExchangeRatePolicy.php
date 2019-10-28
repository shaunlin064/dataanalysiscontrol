<?php

namespace App\Policies;

use App\User;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Auth\Access\HandlesAuthorization;

class ExchangeRatePolicy
{
    use HandlesAuthorization;
    
    /**
     * Determine whether the user can view the exchange rate.
     *
     * @param  \App\User  $user
     * @param  \App\ExchangeRate  $exchangeRate
     * @return mixed
     */
    public function view(User $user)
    {
	    return app(Gate::class)->authorize('financial.exchangeRate.view');
    }
    
    /**
     * Determine whether the user can update the exchange rate.
     *
     * @param  \App\User  $user
     * @param  \App\ExchangeRate  $exchangeRate
     * @return mixed
     */
    public function update(User $user)
    {
        //
	    return app(Gate::class)->authorize('financial.exchangeRate.setting');
    }
    
}
