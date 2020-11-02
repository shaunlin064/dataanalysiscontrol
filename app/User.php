<?php

namespace App;

use App\Http\Controllers\UserController;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use Notifiable,HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','erp_user_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
	public function generateToken()
	{
		$this->api_token = str_random(60);
		$this->save();

		return $this->api_token;
	}

	public function financialList(){
		return $this->hasMany(FinancialList::CLASS,'erp_user_id','erp_user_id');
	}

	public function userGroups(){
		return $this->hasMany(SaleGroupsUsers::CLASS,'erp_user_id','erp_user_id')->orderBy('set_date','desc');
		//return $this->hasMany(SaleGroupsUsers::CLASS,'erp_user_id','erp_user_id')->groupBy('sale_groups_id');
	}

	public function isAdmin(){
		return $this->hasRole('admin');
	}

    public function isBusinessDirector ()
    {
        return $this->hasRole('business_director');
	}
    public function getUserGroupsName ()
    {
        return $this->userGroups->map(function ($v) {
            return $v->saleGroups->name;
        })->implode(',');
	}

    public function hasPermission(String $permission)
    {
        $check = false;
        if(auth()->user()->isAdmin()){
            return true;
        }

        $this->roles->each(function($role) use($permission,&$check){
            if($role->permissions->contains('name',$permission)){
                $check = true;
                return false;
            };
        });

        return $check;
    }

    public function menuCheck (Menu $menus)
    {
        $check = false;

        $menus->subMenu->each(function($v,$k) use(&$check){
            if($check == false){
                $check = $this->hasPermission($v->url);
            }
        });

        if(auth()->user()->isAdmin()){
            $check=true;
        }

        return $check;
    }
	
	public function syncUserDataFromErp (  ) {
		$erpUserDatas = new UserController();
		$erpUserDatas->getErpUser();
		
		$fillable = [
			'name', 'email', 'password','erp_user_id'
		];
		
		foreach ($erpUserDatas->users as $item){
			
			$userObj = User::where('erp_user_id',$item['id'])->first();
			$item['name'] = $item['account'];
			
			if(empty($userObj)){
				
				$item = collect($item);
				$item['password'] = Hash::make($item->get('password'));
				$item['erp_user_id'] = $item->get('id');
				$item->map(function($v,$k) use($fillable,&$item){
					if(!in_array($k,$fillable)){
						unset($item[$k]);
					}
				});
				User::create($item->toArray());
				
			}else{
				$userObj->name = $item['account'];
				$userObj->email = $item['email'];
				$userObj->save();
			}
		}
    }
}
