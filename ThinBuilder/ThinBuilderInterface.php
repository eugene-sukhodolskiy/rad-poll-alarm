<?php

namespace Fury\Modules\ThinBuilder;

interface ThinBuilderInterface{

	/**
	 * Send raw sql query and get result. Logging sql queries to history
	 *
	 * @method query
	 *
	 * @param  String $sql Raw sql string
	 * @param  String|string $fetch_func String name of handler function (fetch, fetchAll ...)
	 * @param  Int $fetch_func_param an example "PDO::FETCH_ASSOC"
	 *
	 * @return Result of query
	 */
	public function query(String $sql, String $fetch_func = '', Int $fetch_func_param = NULL);

	/**
	 * Select rows from db table
	 *
	 * @method select
	 *
	 * @param  String $tablename Name of the table we are working with
	 * @param  array $fields Needed fields
	 * @param  array $where Array with conditions
	 * @param  array $order_fields Fields of sort
	 * @param  String|string $order_sort Type of sort (ASC OR DESC)
	 * @param  array $limit Limits for selecting
	 *
	 * @return array Result of selecting
	 */
	public function select(String $tablename, $fields = [], $where = [], $order_fields = [], String $order_sort = 'DESC', $limit = []);

	/**
	 * Insert row to db table
	 *
	 * @method insert
	 *
	 * @param  String $tablename Name of the table we are working with
	 * @param  Array $data Data for inserting
	 *
	 * @return boolean Result of working
	 */
	public function insert(String $tablename, Array $data);

	/**
	 * Update data in table rows
	 *
	 * @method update
	 *
	 * @param  String $tablename Name of the table we are working with
	 * @param  Array $data New data for rows
	 * @param  array $where Array with conditions. What rows we want to change
	 *
	 * @return boolean Result of updating
	 */
	public function update(String $tablename, Array $data, $where = []);

	/**
	 * Delete selected rows from db table
	 *
	 * @method delete
	 *
	 * @param  String $tablename Name of the table we are working with
	 * @param  array $where Array with conditions. What rows we want to delete
	 *
	 * @return boolean Result of deleting
	 */
	public function delete(String $tablename, $where = []);

	/**
	 * Drop selected table from db
	 *
	 * @method drop
	 *
	 * @param  String $tablename Name of the table what we want to drop
	 *
	 * @return boolean Result of dropping
	 */
	public function drop(String $tablename);

	/**
	 * Truncate selected db table
	 *
	 * @method truncate
	 *
	 * @param  String $tablename Name of the table what we want to truncate
	 *
	 * @return boolean Result of truncating
	 */
	public function truncate(String $tablename);

	/**
	 * Create new table in to db
	 *
	 * @method create_table
	 *
	 * @param  String $tablename Name of new table
	 * @param  Array $fields Array with fields and fields parameters
	 * @param  String $primary_key Name of field what we want selecting like primary key
	 * @param  string $engine Name of db engine
	 *
	 * @return boolean Result of creating
	 */
	public function create_table(String $tablename, Array $fields, String $primary_key, $engine = 'InnoDB');

	/**
	 * Get table fields
	 *
	 * @method table_fields
	 *
	 * @param  String $tablename Name of the table we are working with
	 *
	 * @return Array list of fields
	 */
	public function table_fields(String $tablename);

	/**
	 * List of table from in database
	 *
	 * @method tables
	 *
	 * @return Array list of tables
	 */
	public function tables();

	/**
	 * Get count rows in selected db table
	 *
	 * @method count
	 *
	 * @param  String $tablename Selected db table
	 * @param  array $where Array with conditions
	 *
	 * @return Int Count rows in table
	 */
	public function count(String $tablename, $where = []);

	/**
	 * Get history object
	 *
	 * @method history
	 *
	 * @return History object
	 */
	public function history();
}