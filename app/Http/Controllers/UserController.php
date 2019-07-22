<?php
	/**
	 * Created by PhpStorm.
	 * User: shaun
	 * Date: 2019-06-27
	 * Time: 17:22
	 */
	
	namespace App\Http\Controllers;
	use App\Http\Controllers\ApiController;
	use App\Http\Controllers\Controller;
	
	class UserController extends Controller
	{
		public $users;
		public $department;
		
		public function getErpUser ()
		{
			$apiObj = new ApiController();
			
			$data = 'token=';
			$data .= urlencode(env('API_TOKEN'));
			$url = env('API_GET_MEMBER_URL');
			
			$returnData = $apiObj->curlPost($data,$url,'form');
			
			$this->department = $returnData['data']['department'];
			foreach($returnData['data']['member'] as $key => $item){
				if(isset($this->department[$item['department_id']])){
					$returnData['data']['member'][$key]['department_name'] = $this->department[$item['department_id']]['name'];
				}else{
					$returnData['data']['member'][$key]['department_name'] = '';
				}
			}
			
			$this->users = $returnData['data']['member'];
			return $returnData;
		}
		public function sortUserData($select = null){
			
			$newTmp = [];
			if($this->users) {
				foreach ($this->users as $item) {
					switch($select) {
						case 'unsetOld':
							if(isset($this->department[$item['department_id']]['name'])){
								$newTmp[$item['department_id']]['data'][] = $item;
								$newTmp[$item['department_id']]['depName'] =  $this->department[$item['department_id']]['name'];
							}
							break;
						default:
							$newTmp[$item['department_id']]['data'][] = $item;
							$newTmp[$item['department_id']]['depName'] = isset($this->department[$item['department_id']]['name']) ? $this->department[$item['department_id']]['name'] : '';
					}
				};
			}
			
			return $newTmp;
		}
		
		
	}
