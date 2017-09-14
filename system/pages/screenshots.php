<?php
/**
 * Screenshots
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2017 MyAAC
 * @version   0.4.2
 * @link      http://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');
$title = 'Screenshots';

$canEdit = hasFlag(FLAG_CONTENT_SCREENSHOTS) || superAdmin();
if($canEdit) {
	if(function_exists('imagecreatefrompng')) {
		if (!empty($action)) {
			if ($action == 'delete' || $action == 'edit' || $action == 'hide' || $action == 'moveup' || $action == 'movedown')
				$id = $_REQUEST['id'];
			
			if (isset($_REQUEST['comment']))
				$comment = stripslashes($_REQUEST['comment']);
			
			if (isset($_REQUEST['image']))
				$image = $_REQUEST['image'];
			
			if (isset($_REQUEST['author']))
				$author = $_REQUEST['author'];
			
			$errors = array();
			
			if ($action == 'add') {
				if (Screenshots::add($comment, $image, $author, $errors))
					$comment = $image = $author = '';
			} else if ($action == 'delete') {
				Screenshots::delete($id, $errors);
			} else if ($action == 'edit') {
				if (isset($id) && !isset($name)) {
					$screenshot = Screenshots::get($id);
					$comment = $screenshot['comment'];
					$image = $screenshot['image'];
					$author = $screenshot['author'];
				} else {
					Screenshots::update($id, $comment, $image, $author);
					$action = $comment = $image = $author = '';
				}
			} else if ($action == 'hide') {
				Screenshots::toggleHidden($id, $errors);
			} else if ($action == 'moveup') {
				Screenshots::move($id, -1, $errors);
			} else if ($action == 'movedown') {
				Screenshots::move($id, 1, $errors);
			}
			
			if (!empty($errors))
				echo $twig->render('error_box.html.twig', array('errors' => $errors));
		}
		
		echo $twig->render('screenshots.form.html.twig', array(
			'link' => getPageLink('screenshots', ($action == 'edit' ? 'edit' : 'add')),
			'action' => $action,
			'id' => isset($id) ? $id : null,
			'vdarkborder' => $config['vdarkborder'],
			'darkborder' => $config['darkborder'],
			'comment' => isset($comment) ? $comment : null,
			'image' => isset($image) ? $image : null,
			'author' => isset($author) ? $author : null
		));
	}
	else
		echo 'You cannot edit/add screenshots as it seems your PHP installation doesnt have GD support enabled. Visit <a href="http://be2.php.net/manual/en/image.installation.php">PHP Manual</a> for more info.';
}

if(isset($_GET['screenshot']))
{
	$screenshot = $db->query('SELECT * FROM `' . TABLE_PREFIX . 'screenshots`  WHERE `id` = ' . $db->quote($_GET['screenshot']) . ' ORDER by `ordering` LIMIT 1;');
	if($screenshot->rowCount() == 1)
		$screenshot = $screenshot->fetch();
	else
	{
		echo 'Screenshot with this name does not exists.';
		return;
	}

	$previous_screenshot = $db->query('SELECT * FROM `' . TABLE_PREFIX . 'screenshots` WHERE `id` = ' . $db->quote($screenshot['id'] - 1) . ' ORDER by `ordering`;');
	if($previous_screenshot->rowCount() == 1)
		$previous_screenshot = $previous_screenshot->fetch();
	else
		$previous_screenshot = NULL;

	$next_screenshot = $db->query('SELECT * FROM `' . TABLE_PREFIX . 'screenshots` WHERE `id` = ' . $db->quote($screenshot['id'] + 1) . ' ORDER by `ordering`;');
	if($next_screenshot->rowCount() == 1)
		$next_screenshot = $next_screenshot->fetch();
	else
		$next_screenshot = NULL;
	
	echo $twig->render('screenshots.get.html.twig', array(
		'previous' => $previous_screenshot ? $previous_screenshot['id'] : null,
		'next' => $next_screenshot ? $next_screenshot['id'] : null,
		'screenshot' => $screenshot
	));
	return;
}

$screenshots =
	$db->query('SELECT `id`, `comment`, `image`, `author`, `thumb`' .
		($canEdit ? ', `hidden`, `ordering`' : '') .
		' FROM `' . TABLE_PREFIX . 'screenshots`' .
		(!$canEdit ? ' WHERE `hidden` != 1' : '') .
		' ORDER BY `ordering`;');

$last = $screenshots->rowCount();
if(!$last)
{
?>
	There are no screenshots added to gallery yet.
<?php
	return;
}

echo $twig->render('screenshots.html.twig', array(
	'screenshots' => $screenshots,
	'last' => $last,
	'canEdit' => $canEdit
));

class Screenshots
{
	static public function add($comment, $image, $author, &$errors)
	{
		global $db;
		if(isset($comment[0]) && isset($image[0]) && isset($author[0]))
		{
			$query =
				$db->query(
					'SELECT `ordering`' .
					' FROM `' . TABLE_PREFIX . 'screenshots`' .
					' ORDER BY `ordering`' . ' DESC LIMIT 1'
				);
			
			$ordering = 0;
			if($query->rowCount() > 0) {
				$query = $query->fetch();
				$ordering = $query['ordering'] + 1;
			}
			
			$pathinfo = pathinfo($image);
			$extension = strtolower($pathinfo['extension']);
			$thumb_filename = 'images/screenshots/' . $pathinfo['filename'] . '_thumb.' . $extension;
			$filename = 'images/screenshots/' . $pathinfo['filename'] . '.' . $extension;
			if($db->insert(TABLE_PREFIX . 'screenshots', array(
				'comment' => $comment,
				'image' => $filename, 'author' => $author,
				'thumb' => $thumb_filename,
				'ordering' => $ordering))) {
				if(self::generateThumb($db->lastInsertId(), $image, $errors))
					self::resize($image, 650, 500, $filename, $errors);
			}
		}
		else
			$errors[] = 'Please fill all inputs.';
		
		return !count($errors);
	}
	
	static public function get($id) {
		global $db;
		return $db->select(TABLE_PREFIX . 'screenshots', array('id' => $id));
	}
	
	static public function update($id, $comment, $image, $author) {
		global $db;
		
		$pathinfo = pathinfo($image);
		$extension = strtolower($pathinfo['extension']);
		$filename = 'images/screenshots/' . $pathinfo['filename'] . '.' . $extension;
		
		if($db->update(TABLE_PREFIX . 'screenshots', array(
			'comment' => $comment,
			'image' => $filename, 'author' => $author),
			array('id' => $id)
		)) {
			if(self::generateThumb($id, $image, $errors))
				self::resize($image, 650, 500, $filename, $errors);
		}
	}
	
	static public function delete($id, &$errors)
	{
		global $db;
		if(isset($id))
		{
			if(self::get($id) !== false)
				$db->delete(TABLE_PREFIX . 'screenshots', array('id' => $id));
			else
				$errors[] = 'Screenshot with id ' . $id . ' does not exists.';
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
				$db->update(TABLE_PREFIX . 'screenshots', array('hidden' => ($query['hidden'] == 1 ? 0 : 1)), array('id' => $id));
			else
				$errors[] = 'Screenshot with id ' . $id . ' does not exists.';
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
			$old_record = $db->select(TABLE_PREFIX . 'screenshots', array('ordering' => $ordering));
			if($old_record !== false)
				$db->update(TABLE_PREFIX . 'screenshots', array('ordering' => $query['ordering']), array('ordering' => $ordering));
			
			$db->update(TABLE_PREFIX . 'screenshots', array('ordering' => $ordering), array('id' => $id));
		}
		else
			$errors[] = 'Screenshot with id ' . $id . ' does not exists.';
		
		return !count($errors);
	}
	
	static public function resize($file, $new_width, $new_height, $new_file, &$errors)
	{
		$pathinfo = pathinfo($file);
		$extension = strtolower($pathinfo['extension']);
		
		switch ($extension)
		{
			case 'gif': // GIF
				$image = imagecreatefromgif($file);
				break;
			case 'jpg': // JPEG
			case 'jpeg':
				$image = imagecreatefromjpeg($file);
				break;
			case 'png': // PNG
				$image = imagecreatefrompng($file);
				break;
			default:
				$errors[] = 'Unsupported file format.';
				return false;
		}
		
		$width = imagesx($image);
		$height = imagesy($image);
		
		// create a new temporary image
		$tmp_img = imagecreatetruecolor($new_width, $new_height);
		
		// copy and resize old image into new image
		imagecopyresized($tmp_img, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
		
		// save thumbnail into a file
		switch($extension)
		{
			case 'gif':
				imagegif($tmp_img, $new_file);
				break;
			
			case 'jpg':
			case 'jpeg':
				imagejpeg($tmp_img, $new_file);
				break;
			
			case 'png':
				imagepng($tmp_img, $new_file);
				break;
		}
		
		return true;
	}
	
	static public function generateThumb($id, $file, &$errors)
	{
		$pathinfo = pathinfo($file);
		$extension = strtolower($pathinfo['extension']);
		$thumb_filename = 'images/screenshots/' . $pathinfo['filename'] . '_thumb.' . $extension;
		
		if(!self::resize($file, 170, 110, $thumb_filename, $errors))
			return false;
		
		global $db;
		if(isset($id))
		{
			$query = self::get($id);
			if($query !== false)
				$db->update(TABLE_PREFIX . 'screenshots', array('thumb' => $thumb_filename), array('id' => $id));
			else
				$errors[] = 'Screenshot with id ' . $id . ' does not exists.';
		}
		else
			$errors[] = 'id not set';
		
		return !count($errors);
	}
}
?>
