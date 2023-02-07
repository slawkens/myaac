<?php
/**
 * Changelog
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2019 MyAAC
 * @link      https://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');
$title = 'Changelog';

$_page = (int)$_GET['page'] ?? 0;
$limit = 30;
$offset = $_page * $limit;
$next_page = false;

$canEdit = hasFlag(FLAG_CONTENT_NEWS) || superAdmin();

$changelogs = $db->query('SELECT * FROM `' . TABLE_PREFIX . 'changelog` ' . ($canEdit ? '' : 'WHERE `hidden` = 0').' ORDER BY `id` DESC LIMIT ' . ($limit + 1) . ' OFFSET ' . $offset)->fetchAll();

$i = 0;
foreach($changelogs as $key => &$log)
{
	if($i < $limit) {
		$log['type'] = getChangelogType($log['type']);
		$log['where'] = getChangelogWhere($log['where']);
	}
	else {
		unset($changelogs[$key]);
	}

	if ($i >= $limit)
		$next_page = true;

	$i++;
}

$twig->display('changelog.html.twig', array(
	'changelogs' => $changelogs,
	'page' => $_page,
	'next_page' => $next_page,
	'canEdit' => $canEdit,
));
