<?php
	/**
	 * Created by PhpStorm.
	 * User: shaun
	 * Date: 2019-06-27
	 * Time: 16:59
	 */
	
	namespace App\Http\Controllers;
	
	
	class ApiController
	{
		
		public function curlPost ($Jsondata,$url,$contentType='json')
		{
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_POST, true);
			if($contentType == 'form'){
				curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
			}elseif($contentType == 'json'){
				curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
			}
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $Jsondata);
			$output = curl_exec($ch);
			
			$output = json_decode($output, true);
			
			curl_close($ch);
			
			return $output;
		}
		
		
	}
