<?php
/**
 * Forum base
 *
 * @package   MyAAC
 * @author    Gesior <jerzyskalski@wp.pl>
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2021 MyAAC
 * @link      https://my-aac.org
 */

use MyAAC\Forum;

defined('MYAAC') or die('Direct access not allowed!');
$title = 'Forum';

class_exists('MyAAC\Forum');

$forumSetting = setting('core.forum');
if(strtolower($forumSetting) != 'site') {
	if($forumSetting != '') {
		header('Location: ' . $forumSetting);
		exit;
	}

	echo 'Forum is disabled on this site.';
	return false;
}

$canEdit = Forum::isModerator();

$sections = array();
foreach(getForumBoards() as $section) {
	$sections[$section['id']] = array(
		'id' => $section['id'],
		'name' => $section['name'],
		'description' => $section['description'],
		'closed' => $section['closed'] == '1',
		'guild' => $section['guild'],
		'access' => $section['access']
	);

	if($canEdit) {
		$sections[$section['id']]['hide'] = $section['hide'];
	}
	else {
		$sections[$section['id']]['hide'] = 0;
	}
}

$number_of_rows = 0;
