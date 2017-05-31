<?php
/**
 * Custom pages loader
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2017 MyAAC
 * @version   0.2.3
 * @link      http://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');

$query = $db->query('SELECT `title`, `body`, `php` FROM `' . TABLE_PREFIX . 'pages` WHERE `name` LIKE ' . $db->quote($page) . ' AND `hidden` != 1');
if($query->rowCount() > 0) // found page
{
	$query = $query->fetch();
	$title = $query['title'];

	if($query['php'] == '1') // execute it as php code
		eval($query['body']);
	else
		echo $query['body']; // plain html

	return true;
}

return false;
?>
