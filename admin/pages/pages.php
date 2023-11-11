<?php
/**
 * Pages
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2019 MyAAC
 * @link      https://my-aac.org
 */

use MyAAC\Models\Pages as ModelsPages;
use MyAAC\Admin\Pages;

defined('MYAAC') or die('Direct access not allowed!');
$title = 'Pages';
$use_datatable = true;

csrfProtect();

if (!hasFlag(FLAG_CONTENT_PAGES) && !superAdmin()) {
	echo 'Access denied.';
	return;
}

header('X-XSS-Protection:0');

$name = $p_title = null;
$groups = new OTS_Groups_List();

$php = false;
$enable_tinymce = true;
$access = 0;

// some constants, used mainly by database (cannot by modified without schema changes)
const PAGE_TITLE_LIMIT = 30;
const PAGE_NAME_LIMIT = 30;
const PAGE_BODY_LIMIT = 65535; // maximum page body length

if (!empty($action)) {
	if ($action == 'delete' || $action == 'edit' || $action == 'hide') {
		$id = $_POST['id'];
	}

	if (isset($_POST['name'])) {
		$name = $_POST['name'];
	}

	if (isset($_POST['title'])) {
		$p_title = $_POST['title'];
	}

	$php = isset($_POST['php']) && $_POST['php'] == 1;
	$enable_tinymce = isset($_POST['enable_tinymce']) && $_POST['enable_tinymce'] == 1;
	if ($php) {
		$body = $_POST['body'];
	}
	else if (isset($_POST['body'])) {
		//$body = $_POST['body'];
		$body = html_entity_decode(stripslashes($_POST['body']));
	}

	if (isset($_POST['access'])) {
		$access = $_POST['access'];
	}

	$errors = array();
	$player_id = 1;

	if ($action == 'new') {
		if (isset($p_title) && Pages::add($name, $p_title, $body, $player_id, $php, $enable_tinymce, $access, $errors)) {
			$name = $p_title = $body = '';
			$player_id = $access = 0;
			$php = false;
			$enable_tinymce = true;
			success('Added successful.');
		}
	} else if ($action == 'delete') {
		if (Pages::delete($id, $errors))
			success('Page with id ' . $id . ' has been deleted');
	} else if ($action == 'edit') {
		if (isset($id) && !isset($_POST['name'])) {
			$_page = Pages::get($id);
			$name = $_page['name'];
			$p_title = $_page['title'];
			$body = $_page['body'];
			$php = $_page['php'] == '1';
			$enable_tinymce = $_page['enable_tinymce'] == '1';
			$access = $_page['access'];
		} else {
			if(Pages::update($id, $name, $p_title, $body, $player_id, $php, $enable_tinymce, $access, $errors)) {
				$action = $name = $p_title = $body = '';
				$player_id = 1;
				$access = 0;
				$php = false;
				$enable_tinymce = true;
				success('Updated successful.');
			}
		}
	} else if ($action == 'hide') {
		if (Pages::toggleHidden($id, $errors, $status)) {
			success(($status == 0 ? 'Show' : 'Hide') . ' successful.');
		}
	}

	if (!empty($errors))
		error(implode(", ", $errors));
}

$pages = ModelsPages::all()->map(function ($e) {
	return [
		'link' => getFullLink($e->name, $e->name, true),
		'title' => substr($e->title, 0, 20),
		'php' => $e->php == '1',
		'id' => $e->id,
		'hidden' => $e->hidden
	];
})->toArray();

$twig->display('admin.pages.form.html.twig', [
	'action' => $action,
	'id' => $action == 'edit' ? $id : null,
	'name' => $name,
	'title' => $p_title,
	'php' => $php,
	'enable_tinymce' => $enable_tinymce,
	'body' => isset($body) ? escapeHtml($body) : '',
	'groups' => $groups->getGroups(),
	'access' => $access
]);

$twig->display('admin.pages.html.twig', [
	'pages' => $pages
]);
