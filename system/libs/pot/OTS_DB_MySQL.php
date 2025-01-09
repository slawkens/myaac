<?php

/**#@+
 * @version 0.0.1
 */

/**
 * @package POT
 * @version 0.1.3
 * @author Wrzasq <wrzasq@gmail.com>
 * @copyright 2007 (C) by Wrzasq
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt GNU Lesser General Public License, Version 3
 */

use MyAAC\Cache\Cache;

/**
 * MySQL connection interface.
 *
 * <p>
 * At all everything that you really need to read from this class documentation is list of parameters for driver's constructor.
 * </p>
 *
 * @package POT
 * @version 0.1.3
 */
class OTS_DB_MySQL extends OTS_Base_DB
{
	private $has_table_cache = array();
	private $has_column_cache = array();

	private $clearCacheAfter = false;
/**
 * Creates database connection.
 *
 * <p>
 * Connects to MySQL database on given arguments.
 * </p>
 *
 * <p>
 * List of parameters for this drivers:
 * </p>
 *
 * <ul>
 * <li><var>host</var> - database server.</li>
 * <li><var>port</var> - port (optional, also it is possible to use host:port in <var>host</var> parameter).</li>
 * <li><var>database</var> - database name.</li>
 * <li><var>user</var> - user login.</li>
 * <li><var>password</var> - user password.</li>
 * </ul>
 *
 * @version 0.0.6
 * @param array $params Connection parameters.
 * @throws PDOException On PDO operation error.
 */
	public function __construct($params)
	{
		$user = null;
		$password = null;
		$dns = array();

		// host:port support
		if( strpos(':', $params['host']) !== false)
		{
			$host = explode(':', $params['host'], 2);

			$params['host'] = $host[0];
			$params['port'] = $host[1];
		}

		if( isset($params['database']) )
		{
			$dns[] = 'dbname=' . $params['database'];
		}

		if( isset($params['user']) )
		{
			$user = $params['user'];
		}

		if( isset($params['password']) )
		{
			$password = $params['password'];
		}

		if( isset($params['prefix']) )
		{
			$this->prefix = $params['prefix'];
		}

		if( isset($params['log']) && $params['log'] )
		{
			$this->logged = true;
		}

		if( !isset($params['persistent']) ) {
			$params['persistent'] = false;
		}

		global $config;
		$cache = Cache::getInstance();
		if($cache->enabled()) {
			$tmp = null;
			$need_revalidation = true;
			if($cache->fetch('database_checksum', $tmp) && $tmp) {
				$tmp = unserialize($tmp);
				if(sha1($config['database_host'] . '.' . $config['database_name']) === $tmp) {
					$need_revalidation = false;
				}
			}

			if(!$need_revalidation) {
				$tmp = null;
				if($cache->fetch('database_tables', $tmp) && $tmp) {
					$this->has_table_cache = unserialize($tmp);
				}

				$tmp = null;
				if($cache->fetch('database_columns', $tmp) && $tmp) {
					$this->has_column_cache = unserialize($tmp);
				}
			}
		}

		$driverAttributes = []; // debugbar dont like persistent connection
		if (config('env') !== 'dev' && !getBoolean(config('enable_debugbar'))) {
			$driverAttributes[PDO::ATTR_PERSISTENT] = $params['persistent'];
		}

		if(isset($params['socket'][0])) {
			$dns[] = 'unix_socket=' . $params['socket'];

			parent::__construct('mysql:' . implode(';', $dns), $user, $password, $driverAttributes);

			return;
		}

		if( isset($params['host']) ) {
			$dns[] = 'host=' . $params['host'];
		}

		if( isset($params['port']) ) {
			$dns[] = 'port=' . $params['port'];
		}

		parent::__construct('mysql:' . implode(';', $dns), $user, $password, $driverAttributes);
	}

	public function __destruct()
	{
		global $config;

		$cache = Cache::getInstance();
		if($cache->enabled()) {
			if ($this->clearCacheAfter) {
				$cache->delete('database_tables');
				$cache->delete('database_columns');
				$cache->delete('database_checksum');
			}
			else {
				$cache->set('database_tables', serialize($this->has_table_cache), 3600);
				$cache->set('database_columns', serialize($this->has_column_cache), 3600);
				$cache->set('database_checksum', serialize(sha1($config['database_host'] . '.' . $config['database_name'])), 3600);
			}
		}

		if($this->logged) {
			$currentScript = $_SERVER['REQUEST_URI'] ?? $_SERVER['SCRIPT_FILENAME'];
			log_append('database.log', $currentScript . PHP_EOL . $this->getLog());
		}
	}

/**
 * Query-quoted field name.
 *
 * @param string $name Field name.
 * @return string Quoted name.
 */
	public function fieldName($name)
	{
		return '`' . $name . '`';
	}

/**
 * LIMIT/OFFSET clause for queries.
 *
 * @param int|bool $limit Limit of rows to be affected by query (false if no limit).
 * @param int|bool $offset Number of rows to be skipped before applying query effects (false if no offset).
 * @return string LIMIT/OFFSET SQL clause for query.
 */
	public function limit($limit = false, $offset = false)
	{
		// by default this is empty part
		$sql = '';

		if($limit !== false)
		{
			$sql = ' LIMIT ';

			// OFFSET has no effect if there is no LIMIT
			if($offset !== false)
			{
				$sql .= $offset . ', ';
			}

			$sql .= $limit;
		}

		return $sql;
	}

	public function hasTable($name) {
		if(isset($this->has_table_cache[$name])) {
			return $this->has_table_cache[$name];
		}

		return $this->hasTableInternal($name);
	}

	private function hasTableInternal($name) {
		global $config;
		return ($this->has_table_cache[$name] = $this->query('SELECT `TABLE_NAME` FROM `information_schema`.`tables` WHERE `TABLE_SCHEMA` = ' . $this->quote($config['database_name']) . ' AND `TABLE_NAME` = ' . $this->quote($name) . ' LIMIT 1;')->rowCount() > 0);
	}

	public function hasColumn($table, $column) {
		if(isset($this->has_column_cache[$table . '.' . $column])) {
			return $this->has_column_cache[$table . '.' . $column];
		}

		return $this->hasColumnInternal($table, $column);
	}

	private function hasColumnInternal($table, $column) {
		return $this->hasTable($table) && ($this->has_column_cache[$table . '.' . $column] = count($this->query('SHOW COLUMNS FROM `' . $table . "` LIKE '" . $column . "'")->fetchAll()) > 0);
	}

	public function revalidateCache() {
		foreach($this->has_table_cache as $key => $value) {
			$this->hasTableInternal($key);
		}

		foreach($this->has_column_cache as $key => $value) {
			$explode = explode('.', $key);
			if(!isset($this->has_table_cache[$explode[0]])) { // first check if table exist
				$this->hasTableInternal($explode[0]);
			}

			if($this->has_table_cache[$explode[0]]) {
				$this->hasColumnInternal($explode[0], $explode[1]);
			}
		}
	}

	public function setClearCacheAfter($clearCache)
	{
		$this->clearCacheAfter = $clearCache;
	}
}

/**#@-*/

?>
