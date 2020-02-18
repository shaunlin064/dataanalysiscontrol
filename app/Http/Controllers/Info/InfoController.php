<?php

namespace App\Http\Controllers\Info;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InfoController extends BaseController
{
    //
    public function __construct () {
        parent::__construct();
        $this->resources['jsPath'][] = '/plugins/fullcalendar/core/main.js';
        $this->resources['jsPath'][] = '/plugins/fullcalendar/interaction/main.js';
        $this->resources['jsPath'][] = '/plugins/fullcalendar/daygrid/main.js';
        $this->resources['jsPath'][] = '/plugins/fullcalendar/timeline/main.js';
        $this->resources['jsPath'][] = '/plugins/fullcalendar/resource-common/main.js';
        $this->resources['jsPath'][] = '/plugins/fullcalendar/resource-timeline/main.js';
        
        $this->resources['cssPath'][] = '/plugins/fullcalendar/core/main.css';
        $this->resources['cssPath'][] = '/plugins/fullcalendar/daygrid/main.css';
        $this->resources['cssPath'][] = '/plugins/fullcalendar/timeline/main.css';
        $this->resources['cssPath'][] = '/plugins/fullcalendar/resource-timeline/main.css';
    }
    
    public function scheduleList ()
    {
        $dateTime = new \DateTime();
        
        $timeLine =[
            'resource' => [
                ["id"=>'',"title"=>"更新收款資料","eventColor"=>"green"],
                ["id"=>'',"title"=>"更新財報資料","eventColor"=>"orange"],
                ["id"=>'',"title"=>"更新快取資料","eventColor"=>"red"],
                ["id"=>'',"title"=>"更新財報資料","eventColor"=>"orange"],
                ["id"=>'',"title"=>"更新快取資料","eventColor"=>"red"],
            ],
            'events' => [
                ['id' => '',  'resourceId' => '','title' => '收款更新','start' => $dateTime->format('Y-m-d 00:00:00') , 'end' => $dateTime->format('Y-m-d 01:00:00')],
                ['id' => '',  'resourceId' => '','title' => '財報更新','start' => $dateTime->format('Y-m-d 10:00:00') , 'end' => $dateTime->format('Y-m-d 11:00:00')],
                ['id' => '',  'resourceId' => '','title' => '快取更新','start' => $dateTime->format('Y-m-d 11:00:00') , 'end' => $dateTime->format('Y-m-d 12:00:00')],
                ['id' => '',  'resourceId' => '','title' => '財報更新','start' => $dateTime->format('Y-m-d 15:00:00') , 'end' => $dateTime->format('Y-m-d 16:00:00')],
                ['id' => '',  'resourceId' => '','title' => '快取更新','start' => $dateTime->format('Y-m-d 16:00:00') , 'end' => $dateTime->format('Y-m-d 17:00:00')]
            ]
        ];
        $rid = 1;
        foreach ($timeLine['resource'] as $key => $resource){
            $timeLine['resource'][$key]['id'] = $rid++;
        }
        $eid = 1;
        $rid_provide = 1 ;
        foreach ($timeLine['events'] as $key => $resource){
            $timeLine['events'][$key]['id'] = $eid++;
            $timeLine['events'][$key]['resourceId'] = $rid_provide++;
        }
        return view('info.scheduleList',['data' => $this->resources,'timeLine'=>$timeLine]);
    }
    
    public function updateList ()
    {
        return view('info.updateList',['data' => $this->resources]);
    }
}
