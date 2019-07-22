<?php
	/**
	 * Created by PhpStorm.
	 * User: shaun
	 * Date: 2019-07-16
	 * Time: 10:18
	 */
	
	namespace App\Http\Controllers;
	
	use App\Http\Controllers\BaseController;
	use App\Menu;
	use App\MenuSub;
	use Illuminate\Http\Request;
	use Route;
	
	class MenuController extends BaseController
	{
		/**
		 * Display a listing of the resource.
		 *
		 * @return \Illuminate\Http\Response
		 */
		public function index (Request $request)
		{
			$data = Menu::all();
//			$data->map(function($v,$k){
//				$v->subMenu->map(function($v,$k){
//					$v->level2;
//				});
//				return $v;
//			});
//			foreach($data as $item){
//				foreach ($item->subMenu()->get() as $item2){
//					dd($item2->level2()->get());
//				}
//			}
			//		foreach($this->resources['menus'] as $keys => $items){
			//			$return = array_search('責任額',$items);
			////			dd(Route::currentRouteName(),Route('index'),$return,$keys,$this->resources['menus']);
			//		}
			
			//		dd($return,$this->resources['menus']);
			return view('index', ['data' => $this->resources,'menus' => $data]);
		}
		
		/**
		 * Show the form for creating a new resource.
		 *
		 * @return \Illuminate\Http\Response
		 */
		public function create (Request $request)
		{
			//
			
			foreach($request->all() as $item){
				
				$menu = Menu::create($item);
				$menu->subMenu()->createMany($item['subMenu']);
				$menu->push();
				
			}
			
			
//			$menu->name = '責任額';
//			$menu->icon = 'fa fa-street-view';
//			$menu->save();
//			$item = [
//			  ['menu_id' => $menu->id,
//				'name' => '設定',
//				'icon' => 'fa fa-circle-o',
//				'url' => '/bonus/setting/list'],
//			 ['menu_id' => $menu->id,
//				'name' => '檢視責任額',
//				'icon' => 'fa fa-circle-o',
//				'url' => '/bonus/setting/view/'],
//			];
//			$menu->subMenu()->createMany($item);
//		$menu->push();
			dd($menu);
		}
		
		public function firstCreate ()
		{
			
			$menus = [
							 [
								"name" => "責任額",
								"icon" => "fa fa-street-view",
								"subMenu" => [
								 [
									"name" => "設定",
									"icon" => "fa fa-circle-o",
									"url" => "bonus.setting.list",
								 ],
								 [
									"name" => "個人檢視",
									"icon" => "fa fa-circle-o",
									"url" => "bonus.setting.view",
								 ]
								],
							 ],
							 [
								"name" => "獎金",
								"icon" => "fa fa-usd",
								"subMenu" => [
								 [
									"name" => "全體列表",
									"icon" => "fa fa-circle-o",
									"url" => "bonus.review.list",
								 ],
								 [
									"name" => "個人檢視",
									"icon" => "fa fa-circle-o",
									"url" => "bonus.review.view",
								 ]
								],
							 ],
							];
			$request =  new Request($menus);
			$this->create($request);
		}
		/**
		 * Store a newly created resource in storage.
		 *
		 * @param \Illuminate\Http\Request $request
		 * @return \Illuminate\Http\Response
		 */
		public function store (Request $request)
		{
			//
		
			
		}
		
		/**
		 * Display the specified resource.
		 *
		 * @param int $id
		 * @return \Illuminate\Http\Response
		 */
		public function show ($id)
		{
			//
		}
		
		/**
		 * Show the form for editing the specified resource.
		 *
		 * @param int $id
		 * @return \Illuminate\Http\Response
		 */
		public function edit ($id)
		{
			//
		}
		
		/**
		 * Update the specified resource in storage.
		 *
		 * @param \Illuminate\Http\Request $request
		 * @param int $id
		 * @return \Illuminate\Http\Response
		 */
		public function update (Request $request, $id)
		{
			//
		}
		
		/**
		 * Remove the specified resource from storage.
		 *
		 * @param int $id
		 * @return \Illuminate\Http\Response
		 */
		public function destroy ($id)
		{
			//
		}
	}
