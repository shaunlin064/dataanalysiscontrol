<?php
	/**
	 * Created by PhpStorm.
	 * User: shaun
	 * Date: 2019-06-17
	 * Time: 14:58
	 */
	
	namespace App\Http\Controllers;
	
	use App\Http\Controllers\UserController;
	use Illuminate\Http\Request;
	use Illuminate\Contracts\Session\Session;
	
	class BaseController extends Controller
	{
		//
		public $resources = [
		 'cssPath' => [
			 // <!-- Bootstrap 3.3.7 -->
			'/adminLte_componets/bootstrap/dist/css/bootstrap.min.css',
			 //	<!-- Font Awesome --
			'/adminLte_componets/font-awesome/css/font-awesome.min.css',
			 //	<!-- Ionicons --
			'/adminLte_componets/Ionicons/css/ionicons.min.css',
			 //	<!-- jvectormap --
			'/adminLte_componets/jvectormap/jquery-jvectormap.css',
			 //	<!-- Theme style --
			'/css/AdminLTE.css',
			 //	<!-- AdminLTE Skins. Choose a skin from the css/skins
			 //			 folder instead of downloading all of them to reduce the load. --
			'/css/skins/_all-skins.min.css',
			 //	<!-- Google Font -->
			'https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic',
		 ],
		 'jsPath' => [
			'/js/app.js',
			 //     <!-- jQuery 3 -->
			'/adminLte_componets/jquery/dist/jquery.js',
			 //		<!-- Bootstrap 3.3.7 -->
			'/adminLte_componets/bootstrap/dist/js/bootstrap.min.js',
			 //		<!-- ChartJS -->
//		  '/adminLte_componets/chart.js/Chart.js',
			 //		<!-- FastClick -->
			'/adminLte_componets/fastclick/lib/fastclick.js',
		  '/adminLte_componets/moment/moment.js',
			 //		<!-- AdminLTE App -->
			'/js/adminlte.js',
			 //		<!-- Sparkline -->
			'/adminLte_componets/jquery-sparkline/dist/jquery.sparkline.min.js',
			 //		<!-- jvectormap  -->
			'/adminLte_componets/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js',
			'/adminLte_componets/plugins/jvectormap/jquery-jvectormap-world-mill-en.js',
			 //		<!-- SlimScroll -->
			'/adminLte_componets/jquery-slimscroll/jquery.slimscroll.min.js',
			 
			 //		<!-- AdminLTE for demo purposes -->
			'/js/demo.js'
		 ],
		 'menus' => [
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
		 ]
		];
		
		public function __construct ()
		{
			
			$tmp = [
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
			$this->resources['menus'] = $tmp;
			
			$userObj = new UserController();
			$userObj->getErpUser();
			session(['department' => $userObj->department]);
			session(['users' => $userObj->users]);
			
		}
		
	}

