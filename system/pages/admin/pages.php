<?php
/**
 * Pages
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2017 MyAAC
 * @version   0.4.3
 * @link      http://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');
$title = 'Pages';

if(!hasFlag(FLAG_CONTENT_PAGES) && !superAdmin())
{
	echo 'Access denied.';
	return;
}

$name = $p_title = '';
$groups = new OTS_Groups_List();

$php = false;
$access = 0;

if(!empty($action))
{
	if($action == 'delete' || $action == 'edit' || $action == 'hide')
	$id = $_REQUEST['id'];

	if(isset($_REQUEST['name']))
		$name = $_REQUEST['name'];

	if(isset($_REQUEST['title']))
		$p_title = $_REQUEST['title'];

	$php = isset($_REQUEST['php']);
	//if($php)
	//	$body = $_REQUEST['body'];
	//else
	if(isset($_REQUEST['body']))
		$body = html_entity_decode(stripslashes($_REQUEST['body']));

	if(isset($_REQUEST['access']))
		$access = $_REQUEST['access'];

	$errors = array();
	$player_id = 1;

	if($action == 'add') {
		if(Pages::add($name, $p_title, $body, $player_id, $php, $access, $errors))
		{
			$name = $p_title = $body = '';
			$player_id = $access = 0;
			$php = false;
		}
	}
	else if($action == 'delete') {
		Pages::delete($id, $errors);
	}
	else if($action == 'edit')
	{
		if(isset($id) && !isset($_REQUEST['name'])) {
			$_page = Pages::get($id);
			$name = $_page['name'];
			$p_title = $_page['title'];
			$body = $_page['body'];
			$php = $_page['php'] == '1';
			$access = $_page['access'];
		}
		else {
			Pages::update($id, $name, $p_title, $body, $player_id, $php, $access);
			$action = $name = $p_title = $body = '';
			$player_id = 1;
			$access = 0;
			$php = false;
		}
	}
	else if($action == 'hide') {
		Pages::toggleHidden($id, $errors);
	}

	if(!empty($errors))
		echo $twig->render('error_box.html.twig', array('errors' => $errors));
}
?>
		<?php
		$use_tinymce = false;
		//if($action != 'edit' || !$php)
		//	$use_tinymce = true;

		if($use_tinymce): ?>
		<script type="text/javascript" src="tools/tiny_mce/jquery.tinymce.js"></script>
		<script type="text/javascript">
			$(function() {
				$('#news-body').tinymce({
					script_url : 'tools/tiny_mce/tiny_mce.js',
					forced_root_block : false,

					theme : "advanced",
					plugins: "safari,advimage,emotions,insertdatetime,preview,wordcount",

					theme_advanced_buttons3_add : "emotions,insertdate,inserttime,preview,|,forecolor,backcolor",

					theme_advanced_toolbar_location : "top",
					theme_advanced_toolbar_align : "left",
					theme_advanced_statusbar_location : "bottom",
					theme_advanced_resizing : true,
				});

				<?php /*if($action != 'edit'): ?>
				$("#page-edit-table").hide();
				$("#page-button").click(function() {
					$("#page-edit-table").toggle();
					return false;
				});
				<?php endif; */ ?>
			});
		</script>
		<!--script type="text/javascript">
			tinyMCE.init({
				forced_root_block : false,

				mode : "textareas",
				theme : "advanced",
				plugins: "safari,advimage,emotions,insertdatetime,preview,wordcount",

				theme_advanced_buttons3_add : "emotions,insertdate,inserttime,preview,|,forecolor,backcolor",

				theme_advanced_toolbar_location : "top",
				theme_advanced_toolbar_align : "left",
				theme_advanced_statusbar_location : "bottom",
				theme_advanced_resizing : true,
			});
		</script-->
		<?php endif; ?>
	<form method="post" action="?p=pages&action=<?php echo ($action == 'edit' ? 'edit' : 'add'); ?>">
	<?php if($action == 'edit'): ?>
		<input type="hidden" name="id" value="<?php echo $id; ?>" />
	<?php endif; ?>
	<table class="table" id="page-edit-table" width="100%" border="0" cellspacing="1" cellpadding="4">
		<tr>
			<th><b><?php echo ($action == 'edit' ? 'Edit' : 'Add'); ?> page</b></th>
		</tr>
		<tr>
			<td>
				<table border="0" cellpadding="1">
					<tr>
						<td>Link/name:</td>
						<td><input name="name" value="<?php echo $name; ?>" size="29" maxlength="29"/></td>
					</tr>
					<tr>
						<td>Title:</td>
						<td><input name="title" value="<?php echo $p_title; ?>" size="29" maxlength="29"/></td>
					</tr>
					<tr>
						<td>PHP:</td>
						<td><input type="checkbox" id="news-checkbox" name="php" title="Check if page should be executed as PHP" value="1" <?php if($php) echo 'checked="true"'; ?>/></td>
					</tr>
					<tr>
						<td>Content:</td>
						<td>
							<textarea id="news-body" name="body" maxlength="65000" <?php /*if($use_tinymce) echo 'class="tinymce"';*/ ?> cols="50" rows="5"><?php echo htmlentities(isset($body) ? $body : '', ENT_COMPAT, 'UTF-8'); ?></textarea>
							<?php if($use_tinymce): ?>
							<br/>
							<a href="javascript:;" onmousedown="$('#news-body').tinymce().hide();">[Hide]</a>
							<a href="javascript:;" onmousedown="$('#news-body').tinymce().show();">[Show]</a>
							<?php endif; ?>
						</td>
					<tr/>
					<tr>
						<td>Access:</td>
						<td>
							<select name="access">
								<?php foreach($groups->getGroups() as $id => $group): ?>
									<option value="<?php echo $group->getAccess(); ?>" <?php echo ($access == $group->getAccess() ? 'selected' : ''); ?>><?php echo $group->getName(); ?></option>
								<?php endforeach; ?>
							</select>
						</td>
					</tr>
					<tr>
						<td align="right"><input type="submit" class="button" value="Save"/></td>
						<td align="left">
							<input type="button" onclick="window.location = '<?php echo getLink(PAGE) . ($config['friendly_urls'] ? '?' : '&'); ?>p=pages';" class="button" value="Cancel"/>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	</form>
<table class="table" width="100%" cellspacing="1" cellpadding="4">
	<tr>
		<th><b>Name</b></th>
		<th><b>Title</b></th>
		<th><b>Options</b></th>
	</tr>
<?php

$pages =
	$db->query('SELECT * FROM ' . $db->tableName(TABLE_PREFIX . 'pages'));

$i = 0;
foreach($pages as $_page): ?>
	<tr>
		<td><?php echo getFullLink($_page['name'], $_page['name']); ?></td>
		<td><i><?php echo substr($_page['title'], 0, 20); ?></i></td>
		<td>
			<a href="?p=pages&action=edit&id=<?php echo $_page['id']; ?>" class="ico" title="Edit">
				<img src="<?php echo BASE_URL; ?>images/edit.png"/>
				Edit
			</a>
			<a href="<?php echo ADMIN_URL; ?>?p=pages&action=delete&id=<?php echo $_page['id']; ?>" class="ico" onclick="return confirm('Are you sure?');" title="Delete">
				<img src="<?php echo BASE_URL; ?>images/del.png"/>
				Delete
			</a>
			<a href="?p=pages&action=hide&id=<?php echo $_page['id']; ?>" class="ico" title="<?php echo ($_page['hidden'] != 1 ? 'Hide' : 'Show'); ?>">
				<img src="<?php echo BASE_URL; ?>images/<?php echo ($_page['hidden'] != 1 ? 'success' : 'error'); ?>.png"/>
				<?php echo ($_page['hidden'] != 1 ? 'Hide' : 'Show'); ?>
			</a>
		</td>
	</tr>
<?php endforeach; ?>
</table>

<?php
class Pages
{
	static public function get($id)
	{
		global $db;
		$query = $db->select(TABLE_PREFIX . 'pages', array('id' => $id));
		if($query !== false)
			return $query;

		return false;
	}

	static public function add($name, $title, $body, $player_id, $php, $access, &$errors)
	{
		global $db;
		if(isset($name[0]) && isset($title[0]) && isset($body[0]) && $player_id != 0)
		{
			$query = $db->select(TABLE_PREFIX . 'pages', array('name' => $name));
			if($query === false)
				$db->insert(TABLE_PREFIX . 'pages', array('name' => $name, 'title' => $title, 'body' => $body, 'player_id' => $player_id, 'php' => $php, 'access' => $access));
			else
				$errors[] = 'Page with this words already exists.';
		}
		else
			$errors[] = 'Please fill all inputs.';

		return !count($errors);
	}

	static public function update($id, $name, $title, $body, $player_id, $php, $access) {
		global $db;
		$db->update(TABLE_PREFIX . 'pages', array('name' => $name, 'title' => $title, 'body' => $body, 'player_id' => $player_id, 'php' => $php ? '1' : '0', 'access' => $access), array('id' => $id));
	}

	static public function delete($id, &$errors)
	{
		global $db;
		if(isset($id))
		{
			if($db->select(TABLE_PREFIX . 'pages', array('id' => $id)) !== false)
				$db->delete(TABLE_PREFIX . 'pages', array('id' => $id));
			else
				$errors[] = 'Page with id ' . $id . ' does not exists.';
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
			$query = $db->select(TABLE_PREFIX . 'pages', array('id' => $id));
			if($query !== false)
				$db->update(TABLE_PREFIX . 'pages', array('hidden' => ($query['hidden'] == 1 ? 0 : 1)), array('id' => $id));
			else
				$errors[] = 'Page with id ' . $id . ' does not exists.';
		}
		else
			$errors[] = 'id not set';

		return !count($errors);
	}
}
?>