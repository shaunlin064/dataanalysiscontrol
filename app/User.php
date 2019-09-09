<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Role;

class User extends Authenticatable
{
    use Notifiable;

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
	public function role()
	{
		return $this->belongsToMany(Role::class);
	}
	
	public function hasRole($role){
		//如果包含某一個 就說明有這個角色
		
		if (is_string($role)) {
			return $this->role()->contains('name', $role);
		}
		
		//如果是Collection
		return !!$role->intersect($this->role)->count();//是否相同 ＞0
		
	}
	public function financialList(){
		return $this->hasMany(FinancialList::CLASS,'erp_user_id','erp_user_id');
	}
	
}
