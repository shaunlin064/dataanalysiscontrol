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
                //'/adminLte_componets/datatables.net/css/jquery.dataTables.css',
                '/plugins/datatables.net-bs4/css/dataTables.bootstrap4.css',
                '/plugins/datatables.net-buttons-bs4/css/buttons.bootstrap4.css',
                '/plugins/datatables.net-select-bs4/css/select.bootstrap4.min.css',
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
                //'https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic',
                // global css
                '/css/global.css',
                //             owl
                '/adminLte_componets/OwlCarousel2-2.3.4/dist/assets/owl.carousel.css',
                '/adminLte_componets/OwlCarousel2-2.3.4/dist/assets/owl.theme.default.css',
                // intro.js
                '/adminLte_componets/intro.js/introjs.css',
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
                // datatable
                '/plugins/datatables.net/js/jquery.dataTables.min.js',
                '/plugins/datatables.net-bs/js/dataTables.bootstrap.min.js',
                
                '/plugins/datatables.net-buttons/js/dataTables.buttons.js',
                '/plugins/datatables.net-buttons/js/buttons.html5.js',
                '/plugins/datatables.net-buttons-bs4/js/buttons.bootstrap4.js',
                
                '/plugins/datatables.net-select-bs4/js/select.bootstrap4.min.js',
                //jszip
                '/plugins/jszip/dist/jszip.min.js',
                //pdfmake
                '/plugins/pdfmake/build/pdfmake.js',
                '/plugins/pdfmake/build/vfs_fonts.js',
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
                '/adminLte_componets/select2/dist/js/select2.full.min.js',
                //		<!-- AdminLTE for demo purposes -->
                '/js/demo.js',
                '/js/helper.js',
                '/adminLte_componets/OwlCarousel2-2.3.4/dist/owl.carousel.min.js',
                '/adminLte_componets/moment/moment.js',
                // intro.js
                '/adminLte_componets/intro.js/intro.js',
            ],
        ];
        
        public function __construct ()
        {
            if (env('RESOURCES_CACHE_OPEN')) {
                $time = time();
                $this->resources = collect($this->resources)->map(function ($v, $k) use ($time) {
                    return collect($v)->map(function ($v) use ($time) {
                        return $v .= sprintf('?v=%d', $time);
                    });
                });
            }
        }
        
    }

