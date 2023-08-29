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

defined('MYAAC') or die('Direct access not allowed!');
$title = 'Pages';
$use_datatable = true;

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
define('PAGE_TITLE_LIMIT', 30);
define('PAGE_NAME_LIMIT', 30);
define('PAGE_BODY_LIMIT', 65535); // maximum page body length

if (!empty($action)) {
	if ($action == 'delete' || $action == 'edit' || $action == 'hide')
		$id = $_REQUEST['id'];

	if (isset($_REQUEST['name']))
		$name = $_REQUEST['name'];

	if (isset($_REQUEST['title']))
		$p_title = $_REQUEST['title'];

	$php = isset($_REQUEST['php']) && $_REQUEST['php'] == 1;
	$enable_tinymce = isset($_REQUEST['enable_tinymce']) && $_REQUEST['enable_tinymce'] == 1;
	if ($php)
		$body = $_REQUEST['body'];
	else if (isset($_REQUEST['body'])) {
		//$body = $_REQUEST['body'];
		$body = html_entity_decode(stripslashes($_REQUEST['body']));
	}

	if (isset($_REQUEST['access']))
		$access = $_REQUEST['access'];

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
		if (isset($id) && !isset($_REQUEST['name'])) {
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
		Pages::toggleHidden($id, $errors, $status);
		success(($status == 1 ? 'Show' : 'Hide') . ' successful.');
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

$twig->display('admin.pages.form.html.twig', array(
	'action' => $action,
	'id' => $action == 'edit' ? $id : null,
	'name' => $name,
	'title' => $p_title,
	'php' => $php,
	'enable_tinymce' => $enable_tinymce,
	'body' => isset($body) ? escapeHtml($body) : '',
	'groups' => $groups->getGroups(),
	'access' => $access
));

$twig->display('admin.pages.html.twig', array(
	'pages' => $pages
));

class Pages
{
	static public function verify($name, $title, $body, $player_id, $php, $enable_tinymce, $access, &$errors)
	{
		if(!isset($title[0]) || !isset($body[0])) {
			$errors[] = 'Please fill all inputs.';
			return false;
		}
		if(strlen($name) > PAGE_NAME_LIMIT) {
			$errors[] = 'Page name cannot be longer than ' . PAGE_NAME_LIMIT . ' characters.';
			return false;
		}
		if(strlen($title) > PAGE_TITLE_LIMIT) {
			$errors[] = 'Page title cannot be longer than ' . PAGE_TITLE_LIMIT . ' characters.';
			return false;
		}
		if(strlen($body) > PAGE_BODY_LIMIT) {
			$errors[] = 'Page content cannot be longer than ' . PAGE_BODY_LIMIT . ' characters.';
			return false;
		}
		if(!isset($player_id) || $player_id == 0) {
			$errors[] = 'Player ID is wrong.';
			return false;
		}
		if(!isset($php) || ($php != 0 && $php != 1)) {
			$errors[] = 'Enable PHP is wrong.';
			return false;
		}
		if ($php == 1 && !getBoolean(setting('core.admin_pages_php_enable'))) {
			$errors[] = 'PHP pages disabled on this server. To enable go to Settings in Admin Panel and enable <strong>Enable PHP Pages</strong>.';
			return false;
		}
		if(!isset($enable_tinymce) || ($enable_tinymce != 0 && $enable_tinymce != 1)) {
			$errors[] = 'Enable TinyMCE is wrong.';
			return false;
		}
		if(!isset($access) || $access < 0 || $access > PHP_INT_MAX) {
			$errors[] = 'Access is wrong.';
			return false;
		}

		return true;
	}

	static public function get($id)
	{
		$row = ModelsPages::find($id);
		if ($row) {
			return $row->toArray();
		}

		return false;
	}

	static public function add($name, $title, $body, $player_id, $php, $enable_tinymce, $access, &$errors)
	{
		if(!self::verify($name, $title, $body, $player_id, $php, $enable_tinymce, $access, $errors)) {
			return false;
		}

		if (!ModelsPages::where('name', $name)->exists())
			ModelsPages::create([
				'name' => $name,
				'title' => $title,
				'body' => $body,
				'player_id' => $player_id,
				'php' => $php ? '1' : '0',
				'enable_tinymce' => $enable_tinymce ? '1' : '0',
				'access' => $access
			]);
		else
			$errors[] = 'Page with this link already exists.';

		return !count($errors);
	}

	static public function update($id, $name, $title, $body, $player_id, $php, $enable_tinymce, $access, &$errors)
	{
		if(!self::verify($name, $title, $body, $player_id, $php, $enable_tinymce, $access, $errors)) {
			return false;
		}

		ModelsPages::where('id', $id)->update([
			'name' => $name,
			'title' => $title,
			'body' => $body,
			'player_id' => $player_id,
			'php' => $php ? '1' : '0',
			'enable_tinymce' => $enable_tinymce ? '1' : '0',
			'access' => $access
		]);
		return true;
	}

	static public function delete($id, &$errors)
	{
		if (isset($id)) {
			$row = ModelsPages::find($id);
			if ($row) {
				$row->delete();
			}
			else
				$errors[] = 'Page with id ' . $id . ' does not exists.';
		} else
			$errors[] = 'id not set';

		return !count($errors);
	}

	static public function toggleHidden($id, &$errors, &$status)
	{
		if (isset($id)) {
			$row = ModelsPages::find($id);
			if ($row) {
				$row->hidden = $row->hidden == 1 ? 0 : 1;
				$row->save();
				$status = $row->hidden;
			}
			else {
				$errors[] = 'Page with id ' . $id . ' does not exists.';
			}
		} else
			$errors[] = 'id not set';

		return !count($errors);
	}
}
