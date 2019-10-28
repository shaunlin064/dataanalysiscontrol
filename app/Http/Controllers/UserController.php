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
				
				
                    $newtmp =[];
                    $another = [];
                    //			16,14,25,34,33,32 業務部門 id 排序一下
                    foreach($newTmp as $key => $item){
                        switch($key){
                            case 16:
                                $newtmp[0] = $item;
                                break;
                            case 14:
                                $newtmp[1] = $item;
                                break;
                            case 25:
                                $newtmp[2] = $item;
                                break;
                            case 34:
                                $newtmp[3] = $item;
                                break;
                            case 33:
                                $newtmp[4] = $item;
                                break;
                            case 32:
                                $newtmp[5] = $item;
                                break;
                            default:
                                $another[] = $item;
                        }
                    }
                    ksort($newtmp);
                    $userLists = array_merge($newtmp,$another);
                }
			
			
			return $userLists;
		}
		
		static function isConvener($erp_id = null){
			
			$erp_id = $erp_id ?? auth()->user()->erp_id;
			$users = collect(session('users'));
			$convener = $users->firstWhere('id', $erp_id)['is_convener'];
			
			return $convener == 0 ? false : true;
		}
		
		static function getSameDepartmentUsers($erp_id = null){
			
			$erp_id = $erp_id ?? auth()->user()->erp_id;
			$users = collect(session('users'));
			$departmentId = $users->firstWhere('id', $erp_id)['department_id'];
			
			$sameDepartmentUsers = $users->where('department_id', $departmentId);
			
			return $sameDepartmentUsers->all();
		}
		
	}
