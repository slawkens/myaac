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
defined('MYAAC') or die('Direct access not allowed!');
$title = 'Forum';

if(strtolower($config['forum']) != 'site')
{
	if($config['forum'] != '')
	{
		header('Location: ' . $config['forum']);
		exit;
	}

	echo 'Forum is disabled on this site.';
	return;
}

if(!$logged)
	echo  'You are not logged in. <a href="?subtopic=accountmanagement&redirect=' . BASE_URL . urlencode('?subtopic=forum') . '">Log in</a> to post on the forum.<br /><br />';

require_once LIBS . 'forum.php';

$sections = array();
foreach(getForumBoards() as $section)
{
	$sections[$section['id']] = array(
		'id' => $section['id'],
		'name' => $section['name'],
		'description' => $section['description'],
		'closed' => $section['closed'] == '1',
		'guild' => $section['guild'],
		'access' => $section['access']
	);

	if($canEdit) {
		$sections[$section['id']]['hidden'] = $section['hidden'];
	}
	else {
		$sections[$section['id']]['hidden'] = 0;
	}
}

$number_of_rows = 0;
