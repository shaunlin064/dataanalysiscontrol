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
	    DB::table('campaign_project_managers')->truncate();
	    
	    \App\CampaignProjectManager::insert($results);
    }
}
