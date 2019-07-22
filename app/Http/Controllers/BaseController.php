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
	use Route;
	use Gate;
	use Illuminate\Support\Facades\Input;
	
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
			 //			<!-- DataTables -->
		  '/adminLte_componets/datatables.net-bs/css/dataTables.bootstrap.min.css',
			 //		<!-- daterange picker -->
		  '/adminLte_componets/bootstrap-daterangepicker/daterangepicker.css',
			 //		<!-- bootstrap datepicker -->
		  '/adminLte_componets/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css',
			 //			<!-- iCheck for checkboxes and radio inputs -->
		  '/adminLte_componets/plugins/iCheck/all.css',
			 //			<!-- Bootstrap Color Picker -->
		  '/adminLte_componets/bootstrap-colorpicker/dist/css/bootstrap-colorpicker.min.css',
			 //			<!-- Bootstrap time Picker -->
		  '/adminLte_componets/plugins/timepicker/bootstrap-timepicker.min.css',
			 //			<!-- Select2 -->
		  '/adminLte_componets/select2/dist/css/select2.min.css',
			 //	<!-- Theme style --
			'/css/AdminLTE.css',
			 //	<!-- AdminLTE Skins. Choose a skin from the css/skins
			 //			 folder instead of downloading all of them to reduce the load. --
			'/css/skins/_all-skins.min.css',
			 //	<!-- Google Font -->
			'https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic',
			 // global css
			 '/css/global.css'
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
			 '/adminLte_componets/datatables.net/js/jquery.dataTables.min.js',
		  '/adminLte_componets/datatables.net-bs/js/dataTables.bootstrap.min.js',
		  '/adminLte_componets/fastclick/lib/fastclick.js',
		  '/adminLte_componets/select2/dist/js/select2.full.min.js',
		  '/adminLte_componets/plugins/input-mask/jquery.inputmask.js',
		  '/adminLte_componets/plugins/input-mask/jquery.inputmask.extensions.js',
		  '/adminLte_componets/moment/min/moment.min.js',
		  '/adminLte_componets/bootstrap-daterangepicker/daterangepicker.js',
		  '/adminLte_componets/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js',
		  '/adminLte_componets/bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js',
		  '/adminLte_componets/plugins/timepicker/bootstrap-timepicker.min.js',
		  '/adminLte_componets/plugins/iCheck/icheck.min.js',
			 //		<!-- AdminLTE for demo purposes -->
			'/js/demo.js'
		 ],
		];
		
		public function __construct ()
		{
		
		}
		
	}

