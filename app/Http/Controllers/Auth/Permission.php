<?php
	/**
	 * Created by PhpStorm.
	 * User: shaun
	 * Date: 2019-07-05
	 * Time: 14:47
	 */
	
	namespace App\Http\Controllers\Auth;
	
	
	use App\Http\Controllers\BaseController;
	
	class Permission extends BaseController
	{
		public $admin;
		public $menus;
		public function __construct ()
		{
			$this->admin = [15,17,157,172,179,187];
			$this->menus = [
			 [
				"title" => "責任額",
				"iconClassName" => "fa fa-street-view",
				"children" => [
				 [
					"title" => "設定",
					"iconClassName" => "fa fa-circle-o",
					"href" => "/bonus/setting/list",
				 ],
				 [
					"title" => "個人檢視",
					"iconClassName" => "fa fa-circle-o",
					"href" => "/bonus/setting/view/",
				 ]
				],
			 ],
			 [
				"title" => "獎金",
				"iconClassName" => "fa  fa-usd",
				"children" => [
				 [
					"title" => "全體列表",
					"iconClassName" => "fa fa-circle-o",
					"href" => "/bonus/review/list",
				 ],
				 [
					"title" => "個人檢視",
					"iconClassName" => "fa fa-circle-o",
					"href" => "/bonus/review/view/",
				 ]
				],
			 ],
			];
		}
	}
