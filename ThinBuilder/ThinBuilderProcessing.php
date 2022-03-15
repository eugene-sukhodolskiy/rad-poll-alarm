<?php

namespace Fury\Modules\ThinBuilder;

/**
 * trait ThinBuilderProcessing
 * @author  Eugene Sukhodolskiy <e.sukhodolskiy@outlook.com>
 * @version 0.1
 * Update at 26.01.2020
 */

trait ThinBuilderProcessing{
	protected $pdo;
	protected $db_config;
	protected $history;
	protected $history_enabled;
	protected $driver;

	public function __construct($db_config, $driver = null, $history_enabled = true){
		$this -> db_config = $db_config;
		$this -> pdo = $this -> create_connect($this -> db_config);
		$this -> driver = $driver;
		$this -> history_enabled = $history_enabled;
		if($this -> history_enabled){
			$this -> create_history_instance();
		}
	}

	protected function create_history_instance(){
		$this -> history = new History();
	}

	protected function create_connect($db_conf){
		$dblib = "{$db_conf['dblib']}:host={$db_conf['host']};dbname={$db_conf['dbname']};charset={$db_conf['charset']}";
		$pdo = new \PDO($dblib, $db_conf['user'], $db_conf['password']);
		return $pdo;
	}

	protected function escape_string_in_arr($arr){
		$result = [];
		foreach ($arr as $key => $value) {
			if(!is_array($value)){
				$result[addslashes($key)] = addslashes($value);
			}else{
				$result[addslashes($key)] = $this -> escape_string_in_arr($value);
			}
		}
		return $result;
	}

	protected function select_data_preprocessing($fields, $where, $order_fields, $limit){
		// FIELDS PREPROCESSING
		if(count($fields)){
			$fields = $this -> escape_string_in_arr($fields);
			$fields = '`' . implode('`,`', $fields) . '`';
		}else{
			$fields = '*';
		}

		// ORDER PREPROCESSING
		if(count($order_fields)){
			$order_fields = $this -> escape_string_in_arr($order_fields);
			$order_fields = 'ORDER BY `' . implode('`,`', $order_fields) . '`';
		}else{
			$order_fields = '';
		}

		// WHERE PREPROCESSING
		$where = $this -> where_processing($where);

		// LIMIT PREPROCESSING
		if(count($limit)){
			$limit = $this -> escape_string_in_arr($limit);
			$limit = 'LIMIT ' . implode(',', $limit);
		}else{
			$limit = '';
		}

		return [$fields, $where, $order_fields, $limit];
	}

	protected function where_processing($where){
		if(!count($where)){
			return '';
		}

		$where = $this -> escape_string_in_arr($where);
		foreach ($where as $i => $w_item) {
			if(is_array($w_item)){
				if(count($w_item) === 2){
					$w_item = [$w_item[0], '=', $w_item[1]];
				}

				$w_item[0] = "`{$w_item[0]}`";
				if($w_item[1] != 'IN'){
					$w_item[2] = "'{$w_item[2]}'";
				}else{
					$w_item[2] = '(\'' . implode("','", $w_item[2]) . '\')';
				}

				$where[$i] = implode(' ', $w_item);
			}
		}

		$where = 'WHERE ' . implode(' ', $where);
		return $where;
	}
}