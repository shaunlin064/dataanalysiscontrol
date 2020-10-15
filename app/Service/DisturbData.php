<?php
	/**
	 * Created by PhpStorm.
	 * User: shaun
	 * Date: 2020/10/15
	 * Time: 14:39
	 */
	
	
	namespace App\Service;
	
	
	use App\FinancialList;
	use Carbon\Carbon;
	use Faker\Factory;
	use Illuminate\Support\Facades\Hash;
	
	class DisturbDataService{
		
		public function user (  ) {
			// epr user name email
			$users = \App\User::all();
			$faker = Factory::create();
			$users->each(function($user) use($faker){
				$name = $faker->firstName;
				$email = $faker->email;
				while(\App\User::where('name' , $name)->orWhere('email' , $email)->exists()){
					$name = $faker->firstName;
					$email = $faker->email;
				}
				if($user->id == 129){
					$name = 'Admin';
				}
				$user->update([
					'name' => $name,
					'email' => $email,
					'password' => Hash::make('demo')
				]);
			});
		}
		
		public function customer ( $range = 'today' ) {
			
			// customer name
			if($range === 'today'){
				$date = new Carbon();
				$financial = \App\FinancialList::where('created_at','>',$date->format('Y-m-d 00:00:00'))->get();
			}else if($range === 'all'){
				$financial = \App\FinancialList::all();
			}
			$faker = Factory::create();
			$companies_ids = $financial->pluck('companies_id')->unique()->values();
			$client_ids = $financial->pluck('client_id')->unique()->values();
			$agency_ids = $financial->pluck('agency_id')->unique()->values();
			collect(['Companie' => $companies_ids,'Client' => $client_ids , 'Agency' => $agency_ids])->each(function($ids,$modelName) use($faker){
				switch($modelName){
					case 'Companie':
						$model = new \App\Companie();
						break;
					case 'Client':
						$model = new \App\Client();
						break;
					case 'Agency':
						$model = new \App\Agency();
						break;
				}
				$ids->each(function($id) use($faker,$model){
					if(!empty($id)){
						$name = $faker->company;
						while($model->where('name' , $name)->exists()){
							$name = $faker->company;
						}
						if($model->where('id',$id)->exists()){
							$model->find($id)->update([
								'name' => $name,
							]);
						}else{
							$model->create([
								'id' => $id,
								'name' => $name,
							]);
						}
					}
				});
			});
			
		}
		/**
		 *
		 * @param String $range 'today' or 'all'
		 * */
		public function campaign ($range = 'today') {
			//financial campaign name
			if($range === 'today'){
				$date = new Carbon();
				$financial = \App\FinancialList::where('created_at','>',$date->format('Y-m-d 00:00:00'))->get();
			}else if($range === 'all'){
				$financial = \App\FinancialList::all();
			}
			
			$campaignIds = $financial->pluck('campaign_id')->unique()->values();
			$totalCount = $campaignIds->count();
			$campaignNames = [];
			$faker = Factory::create('zh_TW');
			
			while(count($campaignNames) <  $totalCount){
				$fakerName = sprintf('%s_%s_%s',$faker->company,$faker->bs,$faker->dateTimeBetween('2018-01-01', 'now', 'CST')->format('Ym'));
				if(!in_array($fakerName,$campaignNames)){
					$campaignNames[] = $fakerName;
				}
			}
			$campaignIds->each(function($campaignId) use(&$campaignNames){
				FinancialList::where('campaign_id',$campaignId)->update([
					'campaign_name' => array_pop($campaignNames)
				]);
			});
		}
		/**
		 *
		 * @param String $range 'today' or 'all'
		 * */
		public function financialNumber ($range  = 'today'  ) {
			//financial campaign name
			if($range === 'today'){
				$date = new Carbon();
				$financial = \App\FinancialList::where('created_at','>',$date->format('Y-m-d 00:00:00'))->get();
			}else if($range === 'all'){
				$financial = \App\FinancialList::all();
			}
			//financial profit income cost
			$financial->shuffle()->chunk(100)->each(function($item,$key){
				$randomNumber = rand(1,3);
				$baseNumber = rand(10000,50000);
				$income = $baseNumber  * $randomNumber;
				$cost = round(($baseNumber * rand(1,99) / 100) *  $randomNumber);
				$profit = $income - $cost;
				$profit_percentage = round($profit / $income * 100);
				FinancialList::whereIn('id',$item->pluck('id'))->update(
					[
						'income' => $income,
						'cost' => $cost,
						'profit' => $profit,
						'profit_percentage' => $profit_percentage
					]
				);
			});
		}
	}