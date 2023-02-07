<?php
/**
 * Guilds
 *
 * @package   MyAAC
 * @author    Gesior <jerzyskalski@wp.pl>
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2019 MyAAC
 * @link      https://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');
$title = 'Guilds';

//show list of guilds
if(empty($action)) {
	require PAGES . 'guilds/list.php';
}
else {
	if(!ctype_alnum(str_replace(array('-', '_'), '', $action))) {
		error('Error: Action contains illegal characters.');
	}
	else if(file_exists(PAGES . 'guilds/' . $action . '.php')) {
		require PAGES . 'guilds/' . $action . '.php';
	}
	else {
		error('This page does not exists.');
	}
}
?>
