<?php
	/**
	 * Created by PhpStorm.
	 * User: shaun
	 * Date: 2019-10-23
	 * Time: 10:57
	 */
	
	namespace App;
	
	
	trait HasRoles
	{
		public function roles()
		{
			return $this->belongsToMany(Role::class);
		}
		
		public function assignRole ($role) //$user->assignRole('manager');
		{
			if (is_string($role)) {
				return $this->roles()->save(
				 Role::whereName($role)->firstOrFail()
				);
			}
			return $this->roles()->save($role);
		}
		
		public function hasRole($role){
			//如果包含某一個 就說明有這個角色
			if (is_string($role)) {
				return $this->roles->contains('name', $role);
			}
			//如果是Collection
			if(class_basename($role) == 'Collection'){
				return !! $role->intersect($this->roles)->count();
			}
			
			// 如果是一般role model check
			return ! collect($role->toArray())->intersect($this->roles)->count();//是否相同 ＞0
		}
		
	}
