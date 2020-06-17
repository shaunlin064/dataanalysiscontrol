<?php
    ini_set('max_execution_time', 1200);
    ini_set('memory_limit','1024M');
    /*
    |--------------------------------------------------------------------------
    | Web Routes
    |--------------------------------------------------------------------------
    |
    | Here is where you can register web routes for your application. These
    | routes are loaded by the RouteServiceProvider within a group which
    | contains the "web" middleware group. Now create something great!
    |
    */

    use App\ExchangeRate;
    use App\FinancialList;
    use App\Http\Controllers\FinancialController;
    use App\Http\Controllers\MenuController;
    use App\PermissionsClass;
    use App\SaleGroups;
    use Illuminate\Support\Facades\Artisan;
    use Illuminate\Support\Facades\Cache;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Http\Request;
    use Maatwebsite\Excel\Facades\Excel;
    use SebastianBergmann\Comparator\Factory;
    use SebastianBergmann\Comparator\ComparisonFailure;
    use App\Mail\CronTab;

    /*
     * 登入系統
     */
    Route::get('/info', function () {
        phpinfo();
    });

    Route::get('/cacheflush', function () {
        dd(Cache::store('memcached')->flush(),Cache::store('file')->flush());
    });

    Route::group(['namespace' => '\App\Http\Controllers\Auth'], function () {
        Route::get('/login', 'AuthCustomerController@index')->name('auth.index');
        Route::post('/login', 'AuthCustomerController@login')->name('auth.login');
        Route::any('/logout', 'AuthCustomerController@logout')->name('auth.logout');
    });

Route::group(['middleware' => ['auth', 'GetMenuList']], function () {
        /*
         * index home
         */

        Route::get('/', '\App\Http\Controllers\Bonus\ReviewController@view')->name('index');

        //bonus start
        Route::group(['namespace' => '\App\Http\Controllers\Bonus'], function () {
            //setting
            Route::prefix('bonus/setting')->group(function () {
                Route::get('/list', 'SettingController@list')->name('bonus.setting.list');
                Route::get('/add', 'SettingController@add')->name('bonus.setting.add');
                Route::get('/edit/{bonus?}', 'SettingController@edit')->name('bonus.setting.edit');
                Route::get('/view/{id?}', 'SettingController@view')->name('bonus.setting.view');
                Route::any('/save', 'SettingController@save')->name('bonus.setting.save');
                Route::post('/update', 'SettingController@update')->name('bonus.setting.update');
            });
            /*
             * bonus/review  ::TODO改名為 financial 比較符合現況
             */
            Route::prefix('bonus/review')->group(function () {
                //review
                Route::get('/view', 'ReviewController@view')->name('bonus.review.view');
                Route::any('/getdata', 'ReviewController@getdata')->name('bonus.review.getdata');
                Route::any('/getAjaxData', 'ReviewController@getAjaxData')->name('bonus.review.getAjaxData');
            });
        });

        // financial
        Route::group(['namespace' => '\App\Http\Controllers'], function () {
            Route::prefix('financial')->group(function () {
                Route::group(['namespace' => '\App\Http\Controllers\Financial'], function () {
                    /*匯率設定*/
                    Route::get('/exchangeRateSetting', 'ExchangeRatesController@setting')->name('financial.exchangeRate.setting');
                    Route::get('/exchangeRateSetting/view', 'ExchangeRatesController@view')->name('financial.exchangeRate.view');
                    Route::post('/add', 'ExchangeRatesController@add')->name('financial.exchangeRate.add');
                    Route::any('/exchangeRateSetting/getAjaxData', 'ExchangeRatesController@getAjaxData')->name('financial.exchangeRate.getAjaxData');
                    /*獎金發放*/
                    Route::prefix('/provide')->group(function () {
                        Route::get('/list', 'ProvideController@list')->name('financial.provide.list');
                        Route::get('/view', 'ProvideController@view')->name('bonus.provide.view');
                        Route::post('/getAjaxProvideData', 'ProvideController@getAjaxProvideData')->name('financial.provide.getAjaxProvideData');
                        Route::any('/getAllSelectId', 'ProvideController@getAllSelectId')->name('financial.provide.getAllSelectId');
                        Route::any('/getAjaxData', 'ProvideController@getAjaxData')->name('financial.provide.getAjaxData');
                        Route::any('/ajaxCalculatFinancialBonus', 'ProvideController@ajaxCalculatFinancialBonus')->name('financial.provide.ajaxCalculatFinancialBonus');
                        Route::post('/post', 'ProvideController@post')->name('financial.provide.post');
                    });
                });
            });
        });

        //sale groups setting
        Route::group(['namespace' => '\App\Http\Controllers'], function () {
            Route::prefix('saleGroup/setting')->group(function () {
                Route::group(['namespace' => '\App\Http\Controllers\SaleGroup'], function () {
                    Route::get('/list', 'SaleGroupController@list')->name('saleGroup.setting.list');
                    Route::get('/add', 'SaleGroupController@add')->name('saleGroup.setting.add');
                    Route::get('/edit/{saleGroup?}', 'SaleGroupController@edit')->name('saleGroup.setting.edit');
                    Route::get('/view/{id?}', 'SaleGroupController@view')->name('saleGroup.setting.view');
                    Route::post('/post', 'SaleGroupController@post')->name('saleGroup.setting.post');
                });
            });
        });

        //info
        Route::group(['namespace' => '\App\Http\Controllers'], function () {
            Route::prefix('info')->group(function () {
                Route::group(['namespace' => '\App\Http\Controllers\Info'], function () {
                    Route::get('/scheduleList', 'InfoController@scheduleList')->name('info.scheduleList');
                    Route::get('/updateList', 'InfoController@updateList')->name('info.updateList');

                    Route::post('/articlesPost', 'InfoController@articlesPost')->name('info.articlesPost');

                });
            });
        });
        /*
         * system
         */
        Route::group(['namespace' => '\App\Http\Controllers\System'], function () {
            Route::prefix('system')->group(function () {
                Route::get('/', 'SystemController@index')->name('system.index');
                /*權限設定*/
                Route::get('/permissionList', 'PermissionController@list')->name('system.permission.list');
                Route::post('/permissionGetList', 'PermissionController@getList')->name('system.permission.getList');

                Route::post('/permissionAddAjaxPost', 'PermissionController@permissionAddAjaxPost')->name('system.permission.add.ajaxPost');
                Route::post('/permissionEditAjaxPost/{permission}', 'PermissionController@permissionEditAjaxPost')->name('system.permission.edit.ajaxPost');
                Route::post('/permissionDeleteAjaxPost/{permission}', 'PermissionController@permissionDeleteAjaxPost')->name('system.permission.ajaxPost');

                Route::post('/permissionClassAddAjaxPost', 'PermissionController@permissionClassAddAjaxPost')->name('system.permission.class.add.ajaxPost');
                Route::any('/permissionClassDeleteAjaxPost/{permissionsClass}', 'PermissionController@permissionClassDeleteAjaxPost')->name('system.permission.class.delete.ajaxPost');
                Route::any('/permissionClassEditAjaxPost/{permissionsClass}', 'PermissionController@permissionClassEditAjaxPost')->name('system.permission.class.edit.ajaxPost');


                /*權限角色設定*/
                Route::get('/roleList', 'RoleControl@roleList')->name('system.role.list');
                Route::get('/roleAdd', 'RoleControl@roleAdd')->name('system.role.add');
                Route::get('/roleEdit/{role?}', 'RoleControl@roleEdit')->name('system.role.edit');
                Route::post('/rolePost', 'RoleControl@rolePost')->name('system.role.post');
                Route::post('/roleDelete/{role}', 'RoleControl@roleDelete')->name('system.role.delete');
                /*使用者角色*/
                Route::get('/roleUserList', 'RoleControl@roleUserList')->name('system.role.user.list');
                Route::get('/roleUserEdit/{user?}', 'RoleControl@roleUserEdit')->name('system.role.user.edit');
                Route::post('/roleUserPost', 'RoleControl@roleUserPost')->name('system.role.user.post');

                Route::prefix('menu')->group(function () {
                    Route::Get('/list', 'MenuController@list')->name('system.menu.list');
                    Route::Post('/menuPost', 'MenuController@menuPost')->name('system.menu.post');
                    Route::Post('/menuDelete', 'MenuController@menuDelete')->name('system.menu.delete');
                    Route::Post('/menuSubPost', 'MenuController@menuSubPost')->name('system.menuSub.post');
                    Route::Post('/menuSubDelete', 'MenuController@menuSubDelete')->name('system.menuSub.delete');

                });
            });
        });

        /*
         * adminlte page
         */
        Route::group(['namespace' => '\App\Http\Controllers'], function () {
            Route::prefix('adminlte')->group(function () {
                Route::group(['namespace' => '\App\Http\Controllers\Adminlte'], function () {
                    Route::get('/icons', 'AdminlteController@icon')->name('admin.icons');
                });
            });
        });
    });
