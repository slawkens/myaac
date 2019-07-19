<?php
/**
 * Commands
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2019 MyAAC
 * @link      https://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');
$title = 'Commands';

if($config['otserv_version'] == TFS_03):
?>
List of all your commands, you can check in game, by writing - <b>/commands</b>.<br/><br/>
<?php
endif;
?>
<!--
To get info ingame about specified command, you can write - <b>/man commandName</b> or <b>commandName man</b>. Example: <b>/man serverinfo</b>, <b>/man !sellhouse</b>, <b>!deathlist man</b>, <b>!buypremium man</b>.
<br/><br/>
-->

<?php
$canEdit = hasFlag(FLAG_CONTENT_COMMANDS) || superAdmin();
if($canEdit)
{
	if(!empty($action))
	{
		if($action == 'delete' || $action == 'edit' || $action == 'hide' || $action == 'moveup' || $action == 'movedown')
			$id = $_REQUEST['id'];

		if(isset($_REQUEST['words']))
			$words = $_REQUEST['words'];

		if(isset($_REQUEST['description']))
			$description = stripslashes($_REQUEST['description']);

		$errors = array();

		if($action == 'add') {
			if(Commands::add($words, $description, $errors))
				$words = $description = '';
		}
		else if($action == 'delete') {
			Commands::delete($id, $errors);
		}
		else if($action == 'edit')
		{
			if(isset($id) && !isset($words)) {
				$command = Commands::get($id);
				$words = $command['words'];
				$description = $command['description'];
			}
			else {
				Commands::update($id, $words, $description);
				$action = $words = $description = '';
			}
		}
		else if($action == 'hide') {
			Commands::toggleHidden($id, $errors);
		}
		else if($action == 'moveup') {
			Commands::move($id, -1, $errors);
		}
		else if($action == 'movedown') {
			Commands::move($id, 1, $errors);
		}

		if(!empty($errors))
			$twig->display('error_box.html.twig', array('errors' => $errors));
	}

	$twig->display('commands.form.html.twig', array(
		'link' => getLink('commands/' . ($action == 'edit' ? 'edit' : 'add')),
		'action' => $action,
		'id' => isset($id) ? $id : null,
		'words' => isset($words) ? $words : null,
		'description' => isset($description) ? $description : null
	));
}

$commands =
	$db->query('SELECT `id`, `words`, `description`' .
		($canEdit ? ', `hidden`, `ordering`' : '') .
		' FROM `' . TABLE_PREFIX . 'commands`' .
		(!$canEdit ? ' WHERE `hidden` != 1' : '') .
		' ORDER BY `ordering`;');

$last = $commands->rowCount();
$twig->display('commands.html.twig', array(
	'commands' => $commands,
	'last' => $last,
	'canEdit' => $canEdit
));

class Commands
{
	static public function add($words, $description, &$errors)
	{
		global $db;
		if(isset($words[0]) && isset($description[0]))
		{
			$query = $db->select(TABLE_PREFIX . 'commands', array('words' => $words));

			if($query === false)
			{
				$query =
					$db->query(
						'SELECT ' . $db->fieldName('ordering') .
						' FROM ' . $db->tableName(TABLE_PREFIX . 'commands') .
						' ORDER BY ' . $db->fieldName('ordering') . ' DESC LIMIT 1'
					);

				$ordering = 0;
				if($query->rowCount() > 0) {
					$query = $query->fetch();
					$ordering = $query['ordering'] + 1;
				}
				$db->insert(TABLE_PREFIX . 'commands', array('words' => $words, 'description' => $description, 'ordering' => $ordering));
			}
			else
				$errors[] = 'Command with this words already exists.';
		}
		else
			$errors[] = 'Please fill all inputs.';

		return !count($errors);
	}

	static public function get($id) {
		global $db;
		return $db->select(TABLE_PREFIX . 'commands', array('id' => $id));
	}

	static public function update($id, $words, $description) {
		global $db;
		$db->update(TABLE_PREFIX . 'commands', array('words' => $words, 'description' => $description), array('id' => $id));
	}

	static public function delete($id, &$errors)
	{
		global $db;
		if(isset($id))
		{
			if(self::get($id) !== false)
				$db->delete(TABLE_PREFIX . 'commands', array('id' => $id));
			else
				$errors[] = 'Command with id ' . $id . ' does not exists.';
		}
		else
			$errors[] = 'id not set';

		return !count($errors);
	}

	static public function toggleHidden($id, &$errors)
	{
		global $db;
		if(isset($id))
		{
			$query = self::get($id);
			if($query !== false)
				$db->update(TABLE_PREFIX . 'commands', array('hidden' => ($query['hidden'] == 1 ? 0 : 1)), array('id' => $id));
			else
				$errors[] = 'Command with id ' . $id . ' does not exists.';
		}
		else
			$errors[] = 'id not set';

		return !count($errors);
	}

	static public function move($id, $i, &$errors)
	{
		global $db;
		$query = self::get($id);
		if($query !== false)
		{
			$ordering = $query['ordering'] + $i;
			$old_record = $db->select(TABLE_PREFIX . 'commands', array('ordering' => $ordering));
			if($old_record !== false)
				$db->update(TABLE_PREFIX . 'commands', array('ordering' => $query['ordering']), array('ordering' => $ordering));

			$db->update(TABLE_PREFIX . 'commands', array('ordering' => $ordering), array('id' => $id));
		}
		else
			$errors[] = 'Command with id ' . $id . ' does not exists.';

		return !count($errors);
	}
}
?>