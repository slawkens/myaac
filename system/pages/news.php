<?php
/**
 * News
 *
 * @package   MyAAC
 * @author    Gesior <jerzyskalski@wp.pl>
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2017 MyAAC
 * @version   0.3.0
 * @link      http://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');

if(isset($_GET['archive']))
{
	$title = 'News Archive';

	$categories = array();
	foreach($db->query(
		'SELECT id, name, icon_id FROM ' . TABLE_PREFIX . 'news_categories WHERE hidden != 1') as $cat)
	{
		$categories[$cat['id']] = array(
			'name' => $cat['name'],
			'icon_id' => $cat['icon_id']
		);
	}

	// display big news by id
	if(isset($_GET['id']))
	{
		$field_name = 'date';
		if($_REQUEST['id'] < 100000)
			$field_name = 'id';

		$news = $db->query('SELECT * FROM '.$db->tableName(TABLE_PREFIX . 'news').' WHERE type = 1 AND hidden != 1 and `' . $field_name . '` = ' . (int)$_REQUEST['id']  . '');
		if($news->rowCount() == 1)
		{
			if(@file_exists($template_path . '/news.php'))
				require($template_path . '/news.php');
			else
				require(SYSTEM . 'templates/news.php');

			$news = $news->fetch();
			$author = '';
			$query = $db->query('SELECT name FROM players WHERE id = ' . $db->quote($news['player_id'] . ' LIMIT 1'));
			if($query->rowCount() > 0) {
				$query = $query->fetch();
				$author = $query['name'];
			}

			echo news_parse($news['title'], $news['body'], $news['date'], $categories[$news['category']]['icon_id'], $config['news_author'] ? $author : '', $news['comments'] != 0 ? getForumThreadLink($news['comments']) : NULL);
		}
		else
			echo 'This news doesn\'t exist or is hidden.<br>';

		//echo '<br /><a href="' . internalLayoutLink('news') . ($config['friendly_urls'] ? '/' : '') . 'archive' . '"><font size="2"><b>Back to Archive</b></font></a>';
	?>
	<center>
	<table cellspacing="0" cellpadding="0" border="0"><form method="post" action="<?php echo internalLayoutLink('news') . ($config['friendly_urls'] ? '' : '') . 'archive'; ?>"><tbody><tr><td>
		<input width="120" height="18" border="0" type="image" src="<?php echo $template_path; ?>/images/buttons/sbutton_back.gif" alt="Back" name="Back">
	</form></td></tr></tbody></table>
	</center>
	<?php
		return;
	}
	?>

	<table border="0" cellspacing="1" cellpadding="4" width="100%">
		<tr bgcolor="<?php echo $config['vdarkborder']; ?>">
			<td colspan="3" class="white"><b>News archives</b></td>
		</tr>
	<?php

	$i = 0;
	$news_DB = $db->query('SELECT * FROM '.$db->tableName(TABLE_PREFIX . 'news').' WHERE type = 1 AND hidden != 1 ORDER BY date DESC');
	foreach($news_DB as $news)
	{
		$link = internalLayoutLink('news');
		if($config['friendly_urls'])
			$link .= '/archive/' . $news['id'];
		else
			$link .= 'archive&id=' . $news['id'];

		echo '<tr BGCOLOR='. getStyle($i) .'><td width=4%><center><img src="'.$template_path.'/images/news/icon_' . $categories[$news['category']]['icon_id'] . '_small.gif"></center></td><td>'.date("j.n.Y", $news['date']).'</td><td><b><a href="' . $link.'">'.stripslashes($news['title']).'</a></b></td></tr>';

		$i++;
	}

	echo '</table>';
	return;
}

$title = 'Latest News';

$news_cached = false;
// some constants, used mainly by database (cannot by modified without schema changes)
define('TITLE_LIMIT', 100);
define('BODY_LIMIT', 65535); // maximum news body length

$canEdit = hasFlag(FLAG_CONTENT_NEWS) || superAdmin();
if($canEdit)
	echo '<script type="text/javascript" src="' . BASE_URL . 'tools/tiny_mce/tiny_mce.js"></script>';
if($canEdit)
{
	if(!empty($action))
	{
		$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : NULL;
		$p_title = isset($_REQUEST['title']) ? $_REQUEST['title'] : NULL;
		$body = isset($_REQUEST['body']) ? stripslashes($_REQUEST['body']) : NULL;
		$comments = isset($_REQUEST['comments']) ? $_REQUEST['comments'] : NULL;
		$type = isset($_REQUEST['type']) ? (int)$_REQUEST['type'] : NULL;
		$category = isset($_REQUEST['category']) ? (int)$_REQUEST['category'] : NULL;
		$player_id = isset($_REQUEST['player_id']) ? (int)$_REQUEST['player_id'] : NULL;

		$forum_section = isset($_REQUEST['forum_section']) ? $_REQUEST['forum_section'] : NULL;
		$errors = array();

		if($action == 'add') {
			if(isset($forum_section) && $forum_section != '-1') {
				$forum_add = Forum::add($p_title, $body, $forum_section, $player_id, $account_logged->getId(), $errors);
			}
			
			if(News::add($p_title, $body, $type, $category, $player_id, isset($forum_add) && $forum_add != 0 ? $forum_add : 0, $errors)) {
				$p_title = $body = $comments = '';
				$type = $category = $player_id = 0;
			}
		}
		else if($action == 'delete') {
			News::delete($id, $errors);
		}
		else if($action == 'edit')
		{
			if(isset($id) && !isset($p_title)) {
				$news = News::get($id);
				$p_title = $news['title'];
				$body = $news['body'];
				$comments = $news['comments'];
				$type = $news['type'];
				$category = $news['category'];
				$player_id = $news['player_id'];
			}
			else {
				if(News::update($id, $p_title, $body, $type, $category, $player_id, $comments, $errors)) {
					$action = $p_title = $body = $comments = '';
					$type = $category = $player_id = 0;
				}
			}
		}
		else if($action == 'hide') {
			News::toggleHidden($id, $errors);
		}

		if(!empty($errors))
			output_errors($errors);

		if($cache->enabled())
		{
			$cache->set('news_' . $template_name . '_' . NEWS, '', 120);
			$cache->set('news_' . $template_name . '_' . TICKET, '', 120);
		}
	}
}
else if($cache->enabled())
	$news_cached = News::getCached(NEWS);

if(!$news_cached)
{
	$categories = array();
	foreach($db->query(
		'SELECT id, name, icon_id FROM ' . TABLE_PREFIX . 'news_categories WHERE hidden != 1') as $cat)
	{
		$categories[$cat['id']] = array(
			'name' => $cat['name'],
			'icon_id' => $cat['icon_id']
		);
	}

	$tickers =
		$db->query(
			'SELECT * FROM ' . $db->tableName(TABLE_PREFIX . 'news') . ' WHERE ' . $db->fieldName('type') . ' = ' . TICKET .
			($canEdit ? '' : ' AND ' . $db->fieldName('hidden') . ' != 1') .
			' ORDER BY ' . $db->fieldName('date') . ' DESC' .
			' LIMIT ' . $config['news_ticker_limit']);

	if($tickers->rowCount() > 0)
	{
		$rows = 0;
		$tickers_to_add = '';
		foreach($tickers as $news)
		{
			$admin_options = '';
			if($canEdit)
			{
				$admin_options = '<a href="?subtopic=news&action=edit&id=' . $news['id'] . '" title="Edit">
					<img src="images/edit.png"/>
					Edit
				</a>
				<a id="delete" href="' . BASE_URL . '?subtopic=news&action=delete&id=' . $news['id'] . '" onclick="return confirm(\'Are you sure?\');" title="Delete">
					<img src="images/del.png"/>
					Delete
				</a>
				<a href="?subtopic=news&action=hide&id=' . $news['id'] . '" title="' . ($news['hidden'] != 1 ? 'Hide' : 'Show') . '">
					<img src="images/' . ($news['hidden'] != 1 ? 'success' : 'error') . '.png"/>
					' . ($news['hidden'] != 1 ? 'Hide' : 'Show') . '
				</a>';
			}
			$tickers_to_add .= '<div id="TickerEntry-'.$rows.'" class="Row" onclick=\'TickerAction("TickerEntry-'.$rows.'")\'>
			  <div class="' . (is_int($rows / 2) ? "Odd" : "Even") . '">
				<div class="NewsTickerIcon" style="background-image: url('.$template_path.'/images/news/icon_'.$categories[$news['category']]['icon_id'].'_small.gif);"></div>
				<div id="TickerEntry-'.$rows.'-Button" class="NewsTickerExtend" style="background-image: url('.$template_path.'/images/general/plus.gif);"></div>
				<div class="NewsTickerText">
				  <span class="NewsTickerDate">'.date("j M Y", $news['date']).' -</span>
				  <div id="TickerEntry-'.$rows.'-ShortText" class="NewsTickerShortText">';
			//if admin show button to delete (hide) ticker
			$tickers_to_add .= short_text(strip_tags($news['body']), 100).'</div>
				  <div id="TickerEntry-'.$rows.'-FullText" class="NewsTickerFullText">';
			//if admin show button to delete (hide) ticker
			$tickers_to_add .= $news['body'] . $admin_options . '</div>
				</div>
			  </div>
			</div>';
			$rows++;
		}
	}
}
else
	$tickers_to_add = News::getCached(TICKET);

if(isset($tickers_to_add[0]))
{
	//show table with tickers
	$news_content = '<div id="newsticker" class="Box">
		<div class="Corner-tl" style="background-image: url('.$template_path.'/images/content/corner-tl.gif);"></div>
		<div class="Corner-tr" style="background-image: url('.$template_path.'/images/content/corner-tr.gif);"></div>
		<div class="Border_1" style="background-image: url('.$template_path.'/images/content/border-1.gif);"></div>
		<div class="BorderTitleText" style="background-image: url('.$template_path.'/images/content/title-background-green.gif);"></div>
		<img class="Title" src="'.$template_path.'/images/header/headline-newsticker.gif" alt="Contentbox headline">
		<div class="Border_2">
			<div class="Border_3">
			<div class="BoxContent" style="background-image: url('.$template_path.'/images/content/scroll.gif);">';

			//add tickers list
	$news_content .= $tickers_to_add;
	//koniec
	$news_content .= '</div>
		  </div>
		</div>
		<div class="Border_1" style="background-image: url('.$template_path.'/images/content/border-1.gif);"></div>
		<div class="CornerWrapper-b"><div class="Corner-bl" style="background-image: url('.$template_path.'/images/content/corner-bl.gif);"></div></div>
		<div class="CornerWrapper-b"><div class="Corner-br" style="background-image: url('.$template_path.'/images/content/corner-br.gif);"></div></div>
	  </div>';

		if($cache->enabled() && !$news_cached && !$canEdit)
			$cache->set('news_' . $template_name . '_' . TICKET, $tickers_to_add, 120);
}

if(!$news_cached)
{
	ob_start();
	// newses
	if(@file_exists($template_path . '/news.php'))
		require($template_path . '/news.php');
	else
		require(SYSTEM . 'templates/news.php');

	if($canEdit)
	{
?>
		<script type="text/javascript">
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
		</script>
		<?php if($action != 'edit'): ?>
		<a id="news-button" href="#">Add news</a>
		<?php endif; ?>
		<form method="post" action="<?php echo getPageLink('news', ($action == 'edit' ? 'edit' : 'add')); ?>">
		<?php if($action == 'edit'): ?>
			<input type="hidden" name="id" value="<?php echo $id; ?>" />
		<?php endif; ?>
		<table id="news-edit" width="100%" border="0" cellspacing="1" cellpadding="4">
			<tr>
				<td colspan="2" bgcolor="<?php echo $config['vdarkborder']; ?>" class="white"><b><?php echo ($action == 'edit' ? 'Edit' : 'Add'); ?> news</b></td>
			</tr>

			<?php $rows = 0; ?>

			<tr bgcolor="<?php echo getStyle($rows++); ?>">
				<td><b>Title:</b></td>
				<td><input name="title" value="<?php echo (isset($p_title) ? $p_title : ''); ?>" size="50" maxlength="100"/></td>
			</tr>

			<tr bgcolor="<?php echo getStyle($rows++); ?>">
				<!--td>Description:</td-->
				<td colspan="2"><textarea name="body" maxlength="<?php echo BODY_LIMIT; ?>" class="tinymce"><?php echo (isset($body) ? $body : ''); ?></textarea></td>
			<tr/>

			<tr bgcolor="<?php echo getStyle($rows++); ?>">
				<td><b>Type:</b></td>
				<td>
					<select name="type">
						<option value="<?php echo NEWS; ?>" <?php echo (isset($type) && $type == NEWS ? 'selected="yes"' : ''); ?>>News</option>
						<option value="<?php echo TICKET; ?>" <?php echo (isset($type) && $type == TICKET ? 'selected="yes"' : ''); ?>>Ticket</option>
						<!--option value="<?php echo ARTICLE; ?>">Article</option-->
					</select>
				</td>
			</tr>

			<?php
				if($action == 'edit')
				{
					$player = $ots->createObject('Player');
					$player->load($player_id);
					if($player->isLoaded())
					{
			?>
			<tr bgcolor="<?php echo getStyle($rows++); ?>">
				<td width="180"><b>Author:</b></td>
				<td>
					<select name="original_id" disabled="disabled">
						<?php
							echo '<option value="' . $player->getId() . '">' . $player->getName() . '</option>';
						?>
					</select>
				</td>
			</tr>
			<?php
					}
				}
			?>

			<tr bgcolor="<?php echo getStyle($rows++); ?>">
				<td width="180"><b><?php echo ($action == 'edit' ? 'Modified by' : 'Author'); ?>:</b></td>
				<td>
					<select name="player_id">
						<?php
							$account_players = $account_logged->getPlayersList();
							$account_players->orderBy('group_id', POT::ORDER_DESC);
							$player_number = 0;
							foreach($account_players as $player)
							{
								echo '<option value="' . $player->getId() . '"';
								if(isset($player_id) && $player->getId() == $player_id)
									echo ' selected="selected"';
								echo '>' . $player->getName() . '</option>';
							}
						?>
					</select>
				</td>
			</tr>

			<tr bgcolor="<?php echo getStyle($rows++); ?>">
				<td><b>Category:</b></td>
				<td>
					<?php
					if(!isset($category))
						$category = 0;
					foreach($categories as $id => $cat): ?>
						<input type="radio" name="category" value="<?php echo $id; ?>" <?php echo (((isset($category) && $category == 0 && $id == 1) || (isset($category) && $category == $id)) ? 'checked="yes"' : ''); ?>/> <img src="images/news/icon_<?php echo $cat['icon_id']; ?>_small.gif" />
					<?php endforeach; ?>
				</td>
			</tr>
<?php
			if($action == ''):
?>
			<tr bgcolor="<?php echo getStyle($rows++); ?>">
				<td><b>Create forum thread in section:</b></td>
				<td>
					<select name="forum_section">
						<option value="-1">None</option>
					<?php
						foreach(getForumSections() as $section): ?>
						<option value="<?php echo $section['id']; ?>" <?php echo (isset($forum_section) && $forum_section == $section['id']) ? 'checked="yes"' : ''; ?>/><?php echo $section['name']; ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
<?php
			endif;
?>
			<tr bgcolor="<?php echo getStyle($rows++); ?>">
				<td align="right">
					<input type="submit" value="Submit"/>
				</td>
				<td align="left">
					<input type="button" onclick="window.location = '<?php echo getPageLink(PAGE); ?>';" value="Cancel"/>
				</td>
			</tr>
		</table>
		</form>

		<?php if($action != 'edit'): ?>
		<script type="text/javascript">
			$(document).ready(function() {
				$("#news-edit").hide();
			});

			$("#news-button").click(function() {
				$("#news-edit").toggle();
				return false;
			});
		</script>
		<?php endif; ?>
<?php
	}

	$newses =
		$db->query(
			'SELECT * FROM '.$db->tableName(TABLE_PREFIX . 'news').
			' WHERE type = ' . NEWS .
			($canEdit ? '' : ' AND hidden != 1') .
			' ORDER BY date' .
			' DESC LIMIT ' . $config['news_limit']);
	if($newses->rowCount() > 0)
	{
		foreach($newses as $news)
		{
			$author = '';
			$query = $db->query('SELECT name FROM players WHERE id = ' . $db->quote($news['player_id'] . ' LIMIT 1'));
			if($query->rowCount() > 0) {
				$query = $query->fetch();
				$author = $query['name'];
			}

			$admin_options = '';
			if($canEdit)
			{
				$admin_options = '<br/><br/><a href="?subtopic=news&action=edit&id=' . $news['id'] . '" title="Edit">
					<img src="images/edit.png"/>Edit
				</a>
				<a id="delete" href="' . BASE_URL . '?subtopic=news&action=delete&id=' . $news['id'] . '" onclick="return confirm(\'Are you sure?\');" title="Delete">
					<img src="images/del.png"/>Delete
				</a>
				<a href="?subtopic=news&action=hide&id=' . $news['id'] . '" title="' . ($news['hidden'] != 1 ? 'Hide' : 'Show') . '">
					<img src="images/' . ($news['hidden'] != 1 ? 'success' : 'error') . '.png"/>
					' . ($news['hidden'] != 1 ? 'Hide' : 'Show') . '
				</a>';
			}

			echo news_parse($news['title'], $news['body'] . $admin_options, $news['date'], $categories[$news['category']]['icon_id'], $config['news_author'] ? $author : '', $news['comments'] != 0 ? getForumThreadLink($news['comments']) : NULL);
		}
	}

	$tmp_content = ob_get_contents();
	ob_end_clean();

	if($cache->enabled() && !$canEdit)
		$cache->set('news_' . $template_name . '_' . NEWS, $tmp_content, 120);

	echo $tmp_content;
}
else
	echo $news_cached;

class News
{
	static public function verify($title, $body, &$errors)
	{
		if(!isset($title[0]) || !isset($body[0])) {
			$errors[] = 'Please fill all inputs.';
			return false;
		}

		if(strlen($title) > TITLE_LIMIT) {
			$errors[] = 'News title cannot be longer than ' . TITLE_LIMIT . ' characters.';
			return false;
		}
	
		if(strlen($body) > BODY_LIMIT) {
			$errors[] = 'News content cannot be longer than ' . BODY_LIMIT . ' characters.';
			return false;
		}
		
		return true;
	}

	static public function add($title, $body, $type, $category, $player_id, $comments, &$errors)
	{
		global $db;
		if(!News::verify($title, $body, $errors))
			return false;

		$db->insert(TABLE_PREFIX . 'news', array('title' => $title, 'body' => $body, 'type' => $type, 'date' => time(), 'category' => $category, 'player_id' => isset($player_id) ? $player_id : 0, 'comments' => $comments));
		return true;
	}

	static public function get($id) {
		global $db;
		return $db->select(TABLE_PREFIX . 'news', array('id' => $id));
	}

	static public function update($id, $title, $body, $type, $category, $player_id, $comments, &$errors)
	{
		global $db;
		if(!News::verify($title, $body, $errors))
			return false;

		$db->update(TABLE_PREFIX . 'news', array('title' => $title, 'body' => $body, 'type' => $type, 'category' => $category, 'last_modified_by' => isset($player_id) ? $player_id : 0, 'last_modified_date' => time(), 'comments' => $comments), array('id' => $id));
		return true;
	}

	static public function delete($id, &$errors)
	{
		global $db;
		if(isset($id))
		{
			if($db->select(TABLE_PREFIX . 'news', array('id' => $id)) !== false)
				$db->delete(TABLE_PREFIX . 'news', array('id' => $id));
			else
				$errors[] = 'News with id ' . $id . ' does not exists.';
		}
		else
			$errors[] = 'News id not set.';

		return !count($errors);
	}

	static public function toggleHidden($id, &$errors)
	{
		global $db;
		if(isset($id))
		{
			$query = $db->select(TABLE_PREFIX . 'news', array('id' => $id));
			if($query !== false)
				$db->update(TABLE_PREFIX . 'news', array('hidden' => ($query['hidden'] == 1 ? 0 : 1)), array('id' => $id));
			else
				$errors[] = 'News with id ' . $id . ' does not exists.';
		}
		else
			$errors[] = 'News id not set.';

		return !count($errors);
	}

	static public function getCached($type)
	{
		global $cache, $config, $template_name;
		if($cache->enabled())
		{
			$tmp = '';
			if($cache->fetch('news_' . $template_name . '_' . $type, $tmp) && isset($tmp[0])) {
				return $tmp;
			}
		}

		return false;
	}
}

class Forum
{
	static public function add($title, $body, $section_id, $player_id, $account_id, &$errors)
	{
		global $db;
		$thread_id = 0;
		if($db->insert(TABLE_PREFIX . 'forum', array('id' => 'null', 'first_post' => 0, 'last_post' => time(), 'section' => $section_id, 'replies' => 0, 'views' => 0, 'author_aid' => isset($account_id) ? $account_id : 0, 'author_guid' => isset($player_id) ? $player_id : 0, 'post_text' => $body, 'post_topic' => $title, 'post_smile' => 0, 'post_date' => time(), 'last_edit_aid' => 0, 'edit_date' => 0, 'post_ip' => $_SERVER['REMOTE_ADDR']))) {
			$thread_id = $db->lastInsertId();
			$db->query("UPDATE `" . TABLE_PREFIX . "forum` SET `first_post`=".(int) $thread_id." WHERE `id` = ".(int) $thread_id);
		}
		
		return $thread_id;
	}
}
?>
