<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\BaseController;
use App\Permission;
use App\PermissionsClass;
use Illuminate\Http\Request;

class PermissionController extends BaseController
{
    //
    private $permissions;
    private $permissionsClass;
    
    public function __construct ()
    {
        parent::__construct();
        
        $this->permissions = new Permission();
        $this->permissionsClass = new PermissionsClass();
        
    }
    
    public function list ()
    {
        return view('system.permission.list',array_merge($this->getPermissionData(),['data' => $this->resources]));
    }
    
    /**
     * @param Request $request
     * @return array
     */
    public function permissionAddAjaxPost (Request $request)
    {
        try {
            $this->permissions->create(['name'=>'請輸入權限','label'=>'請輸入描述','permissions_class_id'=>1]);
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    
        return $this->getPermissionData();
    }
    
    /**
     * @param Request $request
     * @param Permission $permission
     * @return array
     */
    public function permissionEditajaxPost (Request $request, Permission $permission)
    {
        $request = $request->all();
        unset($request['_token']);
        
        $data = [$request['name'] => $request['val']];
        
        try {
            $permission->update($data);
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
        
        return $this->getPermissionData();
    }
    
    /**
     * @param Permission $permission
     * @return array
     * @throws \Exception
     */
    public function permissionDeleteAjaxPost (Permission $permission)
    {
        try {
            $permission->delete();
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    
        return $this->getPermissionData();
    }
    
    /**
     * @param Request $request
     * @return array
     */
    public function permissionClassAddAjaxPost (Request $request)
    {
        try {
            $this->permissionsClass->create(['name'=>'請輸入名稱','region'=>'請輸入']);
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
        
        return $this->getPermissionData();
    }
    
    /**
     * @param Request $request
     * @param PermissionsClass $permissionsClass
     * @return array
     */
    public function permissionClassEditAjaxPost (Request $request, PermissionsClass $permissionsClass)
    {
        
        $request = $request->all();
        unset($request['_token']);
        
        $data = [$request['name'] => $request['val']];
        
        try {
            $permissionsClass->update($data);
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
        
        return $this->getPermissionData();
    }
    
    /**
     * @param PermissionsClass $permissionsClass
     * @return array
     * @throws \Exception
     */
    public function permissionClassDeleteAjaxPost (PermissionsClass $permissionsClass)
    {
        try {
            $permissionsClass->delete();
            
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    
        return $this->getPermissionData();
    }
    
    /**
     * @return array
     */
    private function getPermissionData (): array
    {
        $permissionData = $this->permissions::where('id', '<>', 0)->with('permissionClass')->get();
        $permissionClassData = $this->permissionsClass::all();
        
        return ['row' => $permissionData, 'permissionClassData' => $permissionClassData];
    }
}
