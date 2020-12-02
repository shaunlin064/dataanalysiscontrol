<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

class CampaignProjectManager extends Model
{
    use HasFactory;
	
    protected $fillable = ['campaign_id','erp_user_id','type'];
    
    public $timestamps = false;
    
	public function financialList () {
		return $this->belongsTo(FinancialList::Class,'campaign_id','campaign_id');
    }
	
	public function User () {
		$this->belongsTo(User::Class,'erp_user_id','erp_user_id');
    }
	
	/**
	 * @param $campaignIds //campaign_id Array
	 * @return array
	 */
	public function getErpProjectManagerData ($campaignIds) {
		$data = [
			'token'  => env('API_TOKEN'),
			'action' => 'getProjectManager',
			'data'   => [
				'campaign_ids' => $campaignIds
			]
		];
		$results = Http::post(env('API_GET_CAMPAIGN_PROJECT_URL'), $data)->json();
		
		if(isset($results['status']) && $results['status'] == 2){
			return $results;
		}
		
		return $this->keyChangeErpData($results)->toArray();
	}
	
	public function keyChangeErpData ( $items ) {
		
		return collect($items)->map(function($v,$k){
			$v['erp_user_id'] = $v['user_id'];
			unset($v['user_id']);
			unset($v['created_at']);
			unset($v['updated_at']);
			unset($v['id']);
			return $v;
		});
	}
}
