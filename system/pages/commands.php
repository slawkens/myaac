<?php
/**
 * Commands
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2017 MyAAC
 * @version   0.2.0
 * @link      http://my-aac.org
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
			output_errors($errors);
	}
?>
	<form method="post" action="<?php echo getPageLink('commands', ($action == 'edit' ? 'edit' : 'add')); ?>">
		<?php if($action == 'edit'): ?>
		<input type="hidden" name="id" value="<?php echo $id; ?>" />
		<?php endif; ?>
	<table width="100%" border="0" cellspacing="1" cellpadding="4">
		<tr>
			<td bgcolor="<?php echo $config['vdarkborder']; ?>" class="white"><b><?php echo ($action == 'edit' ? 'Edit' : 'Add'); ?> command</b></td>
		</tr>
		<tr>
			<td bgcolor="<?php echo $config['darkborder']; ?>">
				<table border="0" cellpadding="1">
					<tr>
						<td>Words:</td>
						<td><input name="words" value="<?php echo (isset($words) ? $words : ''); ?>" size="29" maxlength="29"/></td>
					<tr>
						<td>Description:</td>
						<td><textarea name="description" maxlength="300" cols="50" rows="5"><?php echo (isset($description) ? $description : ''); ?></textarea></td>
					<tr/>
					<tr>
						<td colspan="2" align="center"><input type="submit" value="Submit"/>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	</form>
<?php
}
?>

<table width="100%" border="0" cellspacing="1" cellpadding="4">
	<tr>
		<td bgcolor="<?php echo $config['vdarkborder']; ?>" class="white" width="150"><b>Words</b></td>
		<td bgcolor="<?php echo $config['vdarkborder']; ?>" class="white"><b>Description</b></td>
		<?php if($canEdit): ?>
		<td bgcolor="<?php echo $config['vdarkborder']; ?>" class="white"><b>Options</b></td>
		<?php endif; ?>
	</tr>
<?php

$commands =
	$db->query('SELECT ' . $db->fieldName('id') . ', ' . $db->fieldName('words') . ', ' . $db->fieldName('description') .
				($canEdit ? ', ' . $db->fieldName('hidden') . ', ' . $db->fieldName('ordering') : '') .
				' FROM ' . $db->tableName(TABLE_PREFIX . 'commands') .
				(!$canEdit ? ' WHERE ' . $db->fieldName('hidden') . ' != 1' : '') .
				' ORDER BY ' . $db->fieldName('ordering'));

$last = $commands->rowCount();
$i = 0;
foreach($commands as $command): ?>
	<tr bgcolor="<?php echo getStyle(++$i); ?>">
		<td><?php echo $command['words']; ?></td>
		<td><i><?php echo $command['description']; ?></i></td>
		<?php if($canEdit): ?>
		<td>
			<a href="?subtopic=commands&action=edit&id=<?php echo $command['id']; ?>" title="Edit">
				<img src="images/edit.png"/>Edit
			</a>
			<a id="delete" href="<?php echo BASE_URL; ?>?subtopic=commands&action=delete&id=<?php echo $command['id']; ?>" onclick="return confirm('Are you sure?');" title="Delete">
				<img src="images/del.png"/>Delete
			</a>
			<a href="?subtopic=commands&action=hide&id=<?php echo $command['id']; ?>" title="<?php echo ($command['hidden'] != 1 ? 'Hide' : 'Show'); ?>">
				<img src="images/<?php echo ($command['hidden'] != 1 ? 'success' : 'error'); ?>.png"/><?php echo ($command['hidden'] != 1 ? 'Hide' : 'Show'); ?>
			</a>
			<?php if($i != 1): ?>
			<a href="?subtopic=commands&action=moveup&id=<?php echo $command['id']; ?>" title="Move up">
				<img src="images/icons/arrow_up.gif"/>Move up
			</a>
			<?php endif; ?>
			<?php if($i != $last): ?>
			<a href="?subtopic=commands&action=movedown&id=<?php echo $command['id']; ?>" title="Move down">
				<img src="images/icons/arrow_down.gif"/>Move down
			</a>
			<?php endif; ?>
		</td>
		<?php endif; ?>
	</tr>
<?php endforeach; ?>
</table>

<?php
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
			if($db->select(TABLE_PREFIX . 'commands', array('id' => $id)) !== false)
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
			$query = $db->select(TABLE_PREFIX . 'commands', array('id' => $id));
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
		$query = $db->select(TABLE_PREFIX . 'commands', array('id' => $id));
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