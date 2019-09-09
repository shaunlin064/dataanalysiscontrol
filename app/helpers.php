<?php
	/**
	 * Created by PhpStorm.
	 * User: shaun
	 * Date: 2019-08-15
	 * Time: 14:34
	 */
	function isDateSimple($str){
		
		if(!preg_match("/^[0-9]{4}(\-|\/)[0-9]{2}(\\1)[0-9]{2}$/", $str) && !preg_match("/^[0-9]{2}(\-|\/)[0-9]{2}$/", $str)){
			return false;
		}
		return true;
	}

