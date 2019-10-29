<?php
	/**
	 * Created by PhpStorm.
	 * User: shaun
	 * Date: 2019-07-05
	 * Time: 14:47
	 */
	
	namespace App\Http\Controllers\Auth;
	
	use App\Http\Controllers\BaseController;
	use App\Menu;
	use Auth;
	use Route;
	use Str;
	
	class Permission extends BaseController
	{
		public $role;
		public $menus;
		public function __construct ()
		{
		
		}
		
	}
