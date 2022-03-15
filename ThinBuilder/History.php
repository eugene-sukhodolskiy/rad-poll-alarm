<?php

namespace Fury\Modules\ThinBuilder;

/**
 * class History
 * @author Eugene Sukhodolskiy
 * Date: 26.01.2020
 * Time: 1:39 AM
 * @version  0.1
 */

class History{
	protected $history = [];

	public function add(String $sql, $result){
		$this -> history[$sql] = $result;
	}

	public function get_all_history(){
		return $this -> history;
	}

	public function get(String $sql){
		if(isset($this -> history[$sql])){
			return $this -> history[$sql];
		}

		return NULL;
	}
}