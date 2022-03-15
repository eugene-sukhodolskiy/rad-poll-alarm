<?php

namespace Fury\Modules\ThinBuilder;

/**
 * interface DriverInterface
 * @author  Eugene Sukhodolskiy <e.sukhodolskiy@outlook.com>
 * @version  0.1
 * Date: 29.01.2020
 */

interface DriverInterface{
	/**
	 * Method for generated event about ready sql string to query
	 *
	 * @method event_ready_sql
	 *
	 * @param  String $sql Ready sql string
	 *
	 */
	public function event_ready_sql(String $sql);

	/**
	 * Generating event about query to db. Called after query
	 *
	 * @method event_query
	 *
	 * @param  String $sql SQL string
	 * @param  [type] $result Result of query
	 */
	public function event_query(String $sql, $result);
}