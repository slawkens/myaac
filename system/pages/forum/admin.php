<?php
/**
 * Forum admin
 *
 * @package   MyAAC
 * @author    Gesior <jerzyskalski@wp.pl>
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2021 MyAAC
 * @link      https://my-aac.org
 */
defined('MYAAC') or exit('Direct access not allowed!');

$canEdit = Forum::isModerator();
if($canEdit) {
	$groups = new OTS_Groups_List();

	if(!empty($action))
	{
		if($action == 'delete_board' || $action == 'edit_board' || $action == 'hide_board' || $action == 'moveup_board' || $action == 'movedown_board')
			$id = $_REQUEST['id'];

		if(isset($_REQUEST['access']))
			$access = $_REQUEST['access'];

		if(isset($_REQUEST['guild']))
			$guild = $_REQUEST['guild'];

		if(isset($_REQUEST['name']))
			$name = $_REQUEST['name'];

		if(isset($_REQUEST['description']))
			$description = stripslashes($_REQUEST['description']);

		$errors = array();

		if($action == 'add_board') {
			if(Forum::add_board($name, $description, $access, $guild, $errors))
				$action = $name = $description = '';
		}
		else if($action == 'delete_board') {
			Forum::delete_board($id, $errors);
			$action = '';
		}
		else if($action == 'edit_board')
		{
			if(isset($id) && !isset($name)) {
				$board = Forum::get_board($id);
				$name = $board['name'];
				$access = $board['access'];
				$guild = $board['guild'];
				$description = $board['description'];
			}
			else {
				Forum::update_board($id, $name, $access, $guild, $description);
				$action = $name = $description = '';
				$access = $guild = 0;
			}
		}
		else if($action == 'hide_board') {
			Forum::toggleHidden_board($id, $errors);
			$action = '';
		}
		else if($action == 'moveup_board') {
			Forum::move_board($id, -1, $errors);
			$action = '';
		}
		else if($action == 'movedown_board') {
			Forum::move_board($id, 1, $errors);
			$action = '';
		}

		if(!empty($errors)) {
			$twig->display('error_box.html.twig', array('errors' => $errors));
			$action = '';
		}
	}

	if(empty($action) || $action == 'edit_board') {
		$guilds = $db->query('SELECT `id`, `name` FROM `guilds`')->fetchAll();
		$twig->display('forum.add_board.html.twig', array(
			'link' => getLink('forum', ($action == 'edit_board' ? 'edit_board' : 'add_board')),
			'action' => $action,
			'id' => isset($id) ? $id : null,
			'name' => isset($name) ? $name : null,
			'description' => isset($description) ? $description : null,
			'access' => isset($access) ? $access : 0,
			'guild' => isset($guild) ? $guild : null,
			'groups' => $groups,
			'guilds' => $guilds
		));

		if($action == 'edit_board')
			$action = '';
	}
}
