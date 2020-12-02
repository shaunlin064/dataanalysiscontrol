<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CampaignProjectMangersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
	    $project = new \App\CampaignProjectManager();
	    $results = $project->getErpProjectManagerData(['all']);
	
	    if(isset($results['status']) && $results['status'] == 2){
		    echo 'erp api fail CampaignProjectMangersSeeder';
		    die;
	    }
	    
	    DB::table('campaign_project_managers')->truncate();
	    \App\CampaignProjectManager::insert($results);
    }
}
