<?php
/**
 * Database connection
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2019 MyAAC
 * @link      https://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');

	try {
		$ots->connect(array(
				'host' => $config['database_host'],
				'user' => $config['database_user'],
				'password' => $config['database_password'],
				'database' => $config['database_name'],
				'log' => $config['database_log'],
				'socket' => @$config['database_socket'],
				'persistent' => @$config['database_persistent']
			)
		);

		$db = POT::getInstance()->getDBHandle();
	}
	catch(PDOException $error) {
		if(isset($cache) && $cache->enabled()) {
			$cache->delete('config_lua');
		}

		if(defined('MYAAC_INSTALL')) {
			return; // installer will take care of this
		}

		throw new RuntimeException('ERROR: Cannot connect to MySQL database.<br/>' .
			'Possible reasons:' .
			'<ul>' .
				'<li>MySQL is not configured propertly in <i>config.php or env vars</i>.</li>' .
				'<li>MySQL server is not running.</li>' .
			'</ul>' . $error->getMessage());
	}