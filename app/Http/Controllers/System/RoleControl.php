<?php
    /**
     * Created by PhpStorm.
     * User: shaun
     * Date: 2019/10/24
     * Time: 12:20 下午
     */
    
    namespace App\Http\Controllers\System;
    
    use App\Http\Controllers\BaseController;
    use App\Http\Controllers\UserController;
    use App\Permission;
    use App\Role;
    use App\User;
    use Illuminate\Http\Request;
    use Route;

    class RoleControl extends BaseController
    {
        protected $policyModel;
    
        public function __construct () {
        
            parent::__construct();
        
            $this->policyModel = new Role();
        }
        /*角色相關start*/
        public function roleList ()
        {
            $this->authorize('create',$this->policyModel);
            
            $roleList = Role::all();
    
            $columns = [
                    ['data'=> 'name'],
                    ['data'=> 'label'],
                    ['data'=> 'id','render' => '<a href="/system/roleEdit/${data}">
<button type="button" class="btn btn-primary btn-flat">編輯</button>'],
                ];
            
            return view( Route::currentRouteName() ,['data'=>$this->resources,'roleList' => $roleList,'columns' => $columns]);
        }
    
        public function roleAdd ()
        {
            $this->authorize('create',$this->policyModel);
            $permissionList = Permission::All();
            
            $columns = [
                ['data'=> 'id','render' => '<p class="hidden">${data}</p><input id="checkbox${data}" name="permission_ids[]" class="permission" type="checkbox" value=${data}>'],
                ['data'=> 'label'],
            ];

            return view( Route::currentRouteName() ,['data'=>$this->resources,'permissionList' => $permissionList,'columns' => $columns]);
        }
    
        public function roleEdit ($id)
        {
            $this->authorize('create',$this->policyModel);
            
            $role = Role::find($id);
            
            $permissionList = Permission::All();
    
            $columns = [
                ['data'=> 'id','render' => '<p class="hidden">${data}</p><input id="checkbox${data}" name="permission_ids[]" class="permission" type="checkbox" value=${data}>'],
                ['data'=> 'label'],
            ];
    
            return view( 'system.role.add' ,['data'=>$this->resources,'permissionList' => $permissionList,'columns' => $columns,'role' => $role,'type' => 'edit']);
        }
    
        public function rolePost (Request $request)
        {
            $this->authorize('create',$this->policyModel);
            
//            //update
            if($request->id){
                $role = Role::find($request->id);
                $role->update($request->all());
                $role->permissions()->detach();
            }else{
                $role = new Role($request->all());
                $role->save();
            }

            if( $request->permission_ids ){
            Permission::whereIn('id',$request->permission_ids)->get()
                ->each(function($permission) use($role){
                    $role->permissions()->attach($permission);
                });
            }
            
            return redirect()->route('system.role.edit',['id'=>$role->id]);
        }
        /*角色相關end*/
    
        /*user角色賦予start*/
        public function roleUserList ()
        {
            $this->authorize('create',$this->policyModel);
    
            $userList = $this->getRoleUserList();
    
            $columns = [
                ['data'=> 'name'],
                ['data'=> 'department'],
                ['data'=> 'roles_name'],
                ['data'=> 'id','render' => '<a href="/system/roleUserEdit/${data}">
<button type="button" class="btn btn-primary btn-flat">編輯</button>'],
            ];
            
            return view( Route::currentRouteName() ,['data'=>$this->resources,'userList' => $userList, 'columns' => $columns]);
        }
    
        public function roleUserEdit ($id)
        {
            $this->authorize('create',$this->policyModel);
            
            $user = User::find($id);
            $rolesList = Role::all();
    
            $columns = [
                ['data'=> 'id','render' => '<p class="hidden">${data}</p><input id="checkbox${data}" name="role_ids[]" class="role" type="checkbox" value=${data}>'],
                ['data'=> 'name'],
                ['data'=> 'label'],
            ];
            
            return view( 'system.role.user.edit' ,['data'=>$this->resources,'rolesList' => $rolesList,'columns' => $columns,'user' => $user,'type' => 'edit']);
        }
    
        public function roleUserPost (Request $request)
        {
            
            $this->authorize('create',$this->policyModel);
            
            $user = User::find($request->id);
    
            $user->roles()->detach();
    
            if( $request->role_ids ){
                Role::whereIn('id',$request->role_ids)->get()
                    ->each(function($role) use($user){
                        $user->roles()->attach($role);
                    });
            }
            
            return redirect()->route('system.role.user.edit',['id' => $user->id ]);
        }
        /*user角色賦予end*/
        /**
         * @return User[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
         */
        private function getRoleUserList ()
        {
            $userList = User::all()->map(function ($v, $k) {
                $v['name'] = ucfirst($v['name']);
                // get string roles
                $v['roles_name'] = $v->roles->map(function ($v, $k) {
                    return $v->label;
                })->implode(',');
                // get epr department Name
                $v['department'] = session('users')[$v->erp_user_id]['department_name'];
                // by use filter resign user
                $v['user_resign_date'] = session('users')[$v->erp_user_id]['user_resign_date'];
                return $v;
            })->filter(function ($v) {
                return $v['user_resign_date'] == '0000-00-00';
            })->values();
            return $userList;
        }
    
    }
