<?php

/**
 * Class: ThinBuilder
 * @author Eugene Sukhodolskiy <e.sukhodolskiy@outlook.com>
 * @version 0.1
 * Date: 22.01.2020
 * Update At: 26.01.2020
 */

namespace Fury\Modules\ThinBuilder;

class ThinBuilder implements ThinBuilderInterface{

	use ThinBuilderProcessing;

	public function query(String $sql, String $fetch_func = '', Int $fetch_func_param = NULL){
		if($this -> driver){
			$this -> driver -> event_ready_sql($sql);
		}

		$result = $fetch_func ? $this -> pdo -> query($sql) -> $fetch_func($fetch_func_param) : $this -> pdo -> query($sql);

		if($this -> history_enabled){
			$this -> history -> add($sql, $result);
		}

		if($this -> driver){
			$this -> driver -> event_query($sql, $result);
		}

		return $result;
	}

	// $where = [ [], 'AND', [], 'OR', [] ]
	public function select(String $tablename, $fields = [], $where = [], $order_fields = [], String $order_sort = 'DESC', $limit = []){
		list($fields, $where, $order_fields, $limit) = $this -> select_data_preprocessing($fields, $where, $order_fields, $limit);

		if($order_fields != ''){
			$order_fields .= " {$order_sort}";
		}

		$sql = "SELECT {$fields} FROM `{$tablename}` {$where} {$order_fields} {$limit}";

		return $this -> query($sql, 'fetchAll', \PDO::FETCH_ASSOC);
	}

	public function insert(String $tablename, Array $data){
		$tablename = addslashes($tablename);
		$data = $this -> escape_string_in_arr($data);

		$fields = '`' . implode('`,`', array_keys($data)) . '`';
		$values = "'" . implode("','", array_values($data)) . "'";
		$sql = "INSERT INTO `{$tablename}` ({$fields}) VALUES ($values)";

		if($this -> query($sql)){
			$id = $this -> pdo -> lastInsertId();
			$this -> history -> add($sql, $id);
			return $id;
		}

		return false;
	}

	public function update(String $tablename, Array $data, $where = []){
		$where = $this -> where_processing($where);
		$data = $this -> escape_string_in_arr($data);
		$tablename = addslashes($tablename);

		$pdata = [];
		foreach ($data as $field => $value) {
			$pdata[] = "`{$field}`='{$value}'";
		}

		$sql = "UPDATE `{$tablename}` SET " . implode(',', $pdata) . " {$where}";
		return $this -> query($sql);
	}

	public function delete(String $tablename, $where = []){
		$tablename = addslashes($tablename);
		$where = $this -> where_processing($where);

		$sql = "DELETE FROM `{$tablename}` {$where}";
		return $this -> query($sql);
	}

	public function drop(String $tablename){
		$tablename = addslashes($tablename);
		$sql = "DROP TABLE `{$tablename}`";
		return $this -> query($sql);
	}

	public function truncate(String $tablename){
		$tablename = addslashes($tablename);
		$sql = "TRUNCATE TABLE `{$tablename}`";
		return $this -> query($sql);
	}

	public function create_table(String $tablename, Array $fields, String $primary_key, $engine = 'InnoDB'){
		/* $fields = [
			'id' => [
				'type' => 'INT',
				'length' => 11,
				'default' => 'NOT NULL',
				'auto_increment' => true,
				'can_be_null' => true
			],
			'option_key' => [
				'type' => 'VARCHAR',
				'length' => 255,
				'default' => 'NOT NULL'
				'auto_increment' => false,
				'can_be_null' => false
			],
		] */
		 
		$tablename = addslashes($tablename);
		$fields = $this -> escape_string_in_arr($fields);
		$primary_key = addslashes($primary_key);
		$engine = addslashes($engine);

		$fields_str_arr = [];
		foreach ($fields as $name => $options) {
			$length = (isset($options['length']) and !is_null($options['length'])) ? "({$options['length']})" : '';

			if(isset($options['default'])){
				$default = ($options['default'] == 'NULL' or $options['default'] == 'CURRENT_TIMESTAMP') ? "DEFAULT {$options['default']}" : "DEFAULT '{$options['default']}'";
			}else{
				$default = '';
			}

			$auto_increment = (isset($options['auto_increment']) and $options['auto_increment']) ? 'AUTO_INCREMENT' : '';
			$can_be_null = (isset($options['can_be_null']) and $options['can_be_null']) ? 'NULL' : 'NOT NULL';
			
			$fields_str_arr[] = "`{$name}` {$options['type']}{$length} {$can_be_null} {$default} {$auto_increment}";
		}

		$fields_string = implode(', ', $fields_str_arr);
		$sql = "CREATE TABLE IF NOT EXISTS `{$tablename}` ({$fields_string}, PRIMARY KEY (`{$primary_key}`)) ENGINE = {$engine}";

		return $this -> query($sql);
	}

	public function table_fields(String $tablename){
		$tablename = addslashes($tablename);

		$sql = "SHOW COLUMNS FROM `{$tablename}`";
		$result = $this -> query($sql, 'fetchAll', \PDO::FETCH_NUM);
		$fields = [];
		foreach ($result as $raw_field) {
			list($type, $length) = explode('(', $raw_field[1]);
			$fields[$raw_field[0]] = ['type' => $type];
			$length = intval($length);
			if($length){
				$fields[$raw_field[0]]['length'] = $length;
			}
		}

		return $fields;
	}

	public function tables(){
		$sql = 'SHOW TABLES';
		$result = $this -> query($sql, 'fetchAll', \PDO::FETCH_ASSOC);
		return array_map(function($val){
			$k = array_keys($val);
			return $val[$k[0]];
		}, $result);
	}

	public function count(String $tablename, $where = []){
		$tablename = addslashes($tablename);
		$where = $this -> where_processing($where);
		$sql = "SELECT COUNT(*) FROM `{$tablename}` {$where}";
		$result = $this -> query($sql, 'fetch', \PDO::FETCH_ASSOC);
		return intval($result['COUNT(*)']);
	}

	public function history(){
		return $this -> history;
	}
}