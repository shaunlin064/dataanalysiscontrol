<?php
    
    namespace App\Providers;
    
    use Illuminate\Auth\Access\Gate;
    use Illuminate\Contracts\Auth\Access\Gate as GateContract;
    use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
    use App\Permission;
    use Illuminate\Support\Facades\Auth;
    use phpDocumentor\Reflection\DocBlockFactory;
    
    class AuthServiceProvider extends ServiceProvider
    {
        /**
         * The policy mappings for the application.
         *
         * @var array
         */
        protected $policies = [
            'App\Bonus' => 'App\Policies\BonusPolicy',
            'App\ExchangeRate' => 'App\Policies\ExchangeRatePolicy',
            'App\FinancialList' => 'App\Policies\FinancialListPolicy',
            'App\Provide' => 'App\Policies\ProvidePolicy',
            'App\Role' => 'App\Policies\RolePolicy',
            'App\SaleGroups' => 'App\Policies\SaleGroupsPolicy',
        ];
        
        /**
         * Register any authentication / authorization services.
         *
         * @return void
         */
        public function boot (GateContract $gate)
        {
            
            $this->registerPolicies($gate);
            
            //$role->define('manager'.'SiteManager');
            
            foreach ($this->getPermissions() as $permission) {
                $gate->define($permission->name, function ($user) use ($permission) {
                    return $user->hasRole($permission->roles);
                });
            }
        }
        
        protected function getPermissions ()
        {
            return Permission::with('roles')->get();
        }
    }
