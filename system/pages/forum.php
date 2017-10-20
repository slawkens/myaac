<?php
/**
 * Forum
 *
 * @package   MyAAC
 * @author    Gesior <jerzyskalski@wp.pl>
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2017 MyAAC
 * @version   0.6.4
 * @link      http://my-aac.org
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

$canEdit = hasFlag(FLAG_CONTENT_FORUM) || superAdmin();
if($canEdit)
{
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
			echo $twig->render('error_box.html.twig', array('errors' => $errors));
			$action = '';
		}
	}
	
	if(empty($action) || $action == 'edit_board') {
		$guilds = $db->query('SELECT `id`, `name` FROM `guilds`')->fetchAll();
		echo $twig->render('forum.add_board.html.twig', array(
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
if(empty($action))
{
	$info = $db->query("SELECT `section`, COUNT(`id`) AS 'threads', SUM(`replies`) AS 'replies' FROM `" . TABLE_PREFIX . "forum` WHERE `first_post` = `id` GROUP BY `section`")->fetchAll();
	
	$boards = array();
	foreach($info as $data)
		$counters[$data['section']] = array('threads' => $data['threads'], 'posts' => $data['replies'] + $data['threads']);
	foreach($sections as $id => $section)
	{
		$show = true;
		if(Forum::hasAccess($id)) {
			$last_post = $db->query("SELECT `players`.`name`, `" . TABLE_PREFIX . "forum`.`post_date` FROM `players`, `" . TABLE_PREFIX . "forum` WHERE `" . TABLE_PREFIX . "forum`.`section` = ".(int) $id." AND `players`.`id` = `" . TABLE_PREFIX . "forum`.`author_guid` ORDER BY `post_date` DESC LIMIT 1")->fetch();
			$boards[] = array(
				'id' => $id,
				'link' => getForumBoardLink($id),
				'name' => $section['name'],
				'description' => $section['description'],
				'hidden' => $section['hidden'],
				'posts' => isset($counters[$id]['posts']) ? $counters[$id]['posts'] : 0,
				'threads' => isset($counters[$id]['threads']) ? $counters[$id]['threads'] : 0,
				'last_post' => array(
					'name' => isset($last_post['name']) ? $last_post['name'] : null,
					'date' => isset($last_post['post_date']) ? $last_post['post_date'] : null,
					'player_link' => isset($last_post['name']) ? getPlayerLink($last_post['name']) : null,
				)
			);
		}
	}
	
	echo $twig->render('forum.boards.html.twig', array(
		'boards' => $boards,
		'canEdit' => $canEdit,
		'last' => count($sections)
	));
	
	return;
}


if($action == 'show_board' || $action == 'show_thread')
{
	require(PAGES . 'forum/' . $action . '.php');
	return;
}

if(!$logged)
{
	header('Location: ' . BASE_URL . '?subtopic=accountmanagement&redirect=' . BASE_URL . urlencode('?subtopic=forum'));
	return;
}

if(file_exists(PAGES . 'forum/' . $action . '.php')) {
	require(PAGES . 'forum/' . $action . '.php');
}

class Forum
{
	static public function canPost($account)
	{
		global $db, $config;

		if(!$account->isLoaded() || $account->isBanned())
			return false;

		if(self::isModerator())
			return true;

		return
			$db->query(
				'SELECT `id` FROM `players` WHERE `account_id` = ' . $db->quote($account->getId()) .
				' AND `level` >= ' . $db->quote($config['forum_level_required']) .
				' LIMIT 1')->rowCount() > 0;
	}

	static public function isModerator() {
		return hasFlag(FLAG_CONTENT_FORUM) || admin();
	}
	
	static public function add_post($thread_id, $section, $author_aid, $author_guid, $post_text, $post_topic, $smile)
	{
		global $db;
		$db->insert(TABLE_PREFIX . 'forum', array(
			'first_post' => $thread_id,
			'section' => $section,
			'author_aid' => $author_aid,
			'author_guid' => $author_guid,
			'post_text' => $post_text,
			'post_topic' => $post_topic,
			'post_smile' => $smile,
			'post_date' => time(),
			'post_ip' => $_SERVER['REMOTE_ADDR']
 		));
	}
	static public function add_board($name, $description, $access, $guild, &$errors)
	{
		global $db;
		if(isset($name[0]) && isset($description[0]))
		{
			$query = $db->select(TABLE_PREFIX . 'forum_boards', array('name' => $name));
			
			if($query === false)
			{
				$query =
					$db->query(
						'SELECT ' . $db->fieldName('ordering') .
						' FROM ' . $db->tableName(TABLE_PREFIX . 'forum_boards') .
						' ORDER BY ' . $db->fieldName('ordering') . ' DESC LIMIT 1'
					);
				
				$ordering = 0;
				if($query->rowCount() > 0) {
					$query = $query->fetch();
					$ordering = $query['ordering'] + 1;
				}
				$db->insert(TABLE_PREFIX . 'forum_boards', array('name' => $name, 'description' => $description, 'access' => $access, 'guild' => $guild, 'ordering' => $ordering));
			}
			else
				$errors[] = 'Forum board with this name already exists.';
		}
		else
			$errors[] = 'Please fill all inputs.';
		
		return !count($errors);
	}
	
	static public function get_board($id) {
		global $db;
		return $db->select(TABLE_PREFIX . 'forum_boards', array('id' => $id));
	}
	
	static public function update_board($id, $name, $access, $guild, $description) {
		global $db;
		$db->update(TABLE_PREFIX . 'forum_boards', array('name' => $name, 'description' => $description, 'access' => $access, 'guild' => $guild), array('id' => $id));
	}
	
	static public function delete_board($id, &$errors)
	{
		global $db;
		if(isset($id))
		{
			if(self::get_board($id) !== false)
				$db->delete(TABLE_PREFIX . 'forum_boards', array('id' => $id));
			else
				$errors[] = 'Forum board with id ' . $id . ' does not exists.';
		}
		else
			$errors[] = 'id not set';
		
		return !count($errors);
	}
	
	static public function toggleHidden_board($id, &$errors)
	{
		global $db;
		if(isset($id))
		{
			$query = self::get_board($id);
			if($query !== false)
				$db->update(TABLE_PREFIX . 'forum_boards', array('hidden' => ($query['hidden'] == 1 ? 0 : 1)), array('id' => $id));
			else
				$errors[] = 'Forum board with id ' . $id . ' does not exists.';
		}
		else
			$errors[] = 'id not set';
		
		return !count($errors);
	}
	
	static public function move_board($id, $i, &$errors)
	{
		global $db;
		$query = self::get_board($id);
		if($query !== false)
		{
			$ordering = $query['ordering'] + $i;
			$old_record = $db->select(TABLE_PREFIX . 'forum_boards', array('ordering' => $ordering));
			if($old_record !== false)
				$db->update(TABLE_PREFIX . 'forum_boards', array('ordering' => $query['ordering']), array('ordering' => $ordering));
			
			$db->update(TABLE_PREFIX . 'forum_boards', array('ordering' => $ordering), array('id' => $id));
		}
		else
			$errors[] = 'Forum board with id ' . $id . ' does not exists.';
		
		return !count($errors);
	}
	
	public static function parseSmiles($text)
	{
		$smileys = array(
			';D' => 1,
			':D' => 1,
			':cool:' => 2,
			';cool;' => 2,
			':ekk:' => 3,
			';ekk;' => 3,
			';o' => 4,
			';O' => 4,
			':o' => 4,
			':O' => 4,
			':(' => 5,
			';(' => 5,
			':mad:' => 6,
			';mad;' => 6,
			';rolleyes;' => 7,
			':rolleyes:' => 7,
			':)' => 8,
			';d' => 9,
			':d' => 9,
			';)' => 10
		);
		
		foreach($smileys as $search => $replace)
			$text = str_replace($search, '<img src="images/forum/smile/'.$replace.'.gif" alt="'. $search .'" title="' . $search . '" />', $text);
		
		return $text;
	}
	
	public static function parseBBCode($text, $smiles)
	{
		$rows = 0;
		while(stripos($text, '[code]') !== false && stripos($text, '[/code]') !== false )
		{
			$code = substr($text, stripos($text, '[code]')+6, stripos($text, '[/code]') - stripos($text, '[code]') - 6);
			if(!is_int($rows / 2)) { $bgcolor = 'ABED25'; } else { $bgcolor = '23ED25'; } $rows++;
			$text = str_ireplace('[code]'.$code.'[/code]', '<i>Code:</i><br /><table cellpadding="0" style="background-color: #'.$bgcolor.'; width: 480px; border-style: dotted; border-color: #CCCCCC; border-width: 2px"><tr><td>'.$code.'</td></tr></table>', $text);
		}
		$rows = 0;
		while(stripos($text, '[quote]') !== false && stripos($text, '[/quote]') !== false )
		{
			$quote = substr($text, stripos($text, '[quote]')+7, stripos($text, '[/quote]') - stripos($text, '[quote]') - 7);
			if(!is_int($rows / 2)) { $bgcolor = 'AAAAAA'; } else { $bgcolor = 'CCCCCC'; } $rows++;
			$text = str_ireplace('[quote]'.$quote.'[/quote]', '<table cellpadding="0" style="background-color: #'.$bgcolor.'; width: 480px; border-style: dotted; border-color: #007900; border-width: 2px"><tr><td>'.$quote.'</td></tr></table>', $text);
		}
		$rows = 0;
		while(stripos($text, '[url]') !== false && stripos($text, '[/url]') !== false )
		{
			$url = substr($text, stripos($text, '[url]')+5, stripos($text, '[/url]') - stripos($text, '[url]') - 5);
			$text = str_ireplace('[url]'.$url.'[/url]', '<a href="'.$url.'" target="_blank">'.$url.'</a>', $text);
		}
		
		$xhtml = false;
		$tags = array(
			'#\[b\](.*?)\[/b\]#si' => ($xhtml ? '<strong>\\1</strong>' : '<b>\\1</b>'),
			'#\[i\](.*?)\[/i\]#si' => ($xhtml ? '<em>\\1</em>' : '<i>\\1</i>'),
			'#\[u\](.*?)\[/u\]#si' => ($xhtml ? '<span style="text-decoration: underline;">\\1</span>' : '<u>\\1</u>'),
			'#\[s\](.*?)\[/s\]#si' => ($xhtml ? '<strike>\\1</strike>' : '<s>\\1</s>'),
			
			'#\[guild\](.*?)\[/guild\]#si' => urldecode(generateLink(getGuildLink('$1', false), '$1', true)),
			'#\[house\](.*?)\[/house\]#si' => urldecode(generateLink(getHouseLink('$1', false), '$1', true)),
			'#\[player\](.*?)\[/player\]#si' => urldecode(generateLink(getPlayerLink('$1', false), '$1', true)),
			// TODO: [poll] tag
			
			'#\[color=(.*?)\](.*?)\[/color\]#si' => ($xhtml ? '<span style="color: \\1;">\\2</span>' : '<font color="\\1">\\2</font>'),
			'#\[img\](.*?)\[/img\]#si' => ($xhtml ? '<img src="\\1" border="0" alt="" />' : '<img src="\\1" border="0" alt="">'),
			'#\[url=(.*?)\](.*?)\[/url\]#si' => '<a href="\\1" title="\\2">\\2</a>',
//		'#\[email\](.*?)\[/email\]#si' => '<a href="mailto:\\1" title="Email \\1">\\1</a>',
			'#\[code\](.*?)\[/code\]#si' => '<code>\\1</code>',
//		'#\[align=(.*?)\](.*?)\[/align\]#si' => ($xhtml ? '<div style="text-align: \\1;">\\2</div>' : '<div align="\\1">\\2</div>'),
//		'#\[br\]#si' => ($xhtml ? '<br style="clear: both;" />' : '<br>'),
		);
		
		foreach($tags as $search => $replace)
			$text = preg_replace($search, $replace, $text);
		
		return ($smiles == 0 ? Forum::parseSmiles($text) : $text);
	}
	
	public static function showPost($topic, $text, $smiles)
	{
		$text = nl2br($text);
		$post = '';
		if(!empty($topic))
			$post .= '<b>'.($smiles == 0 ? self::parseSmiles($topic) : $topic).'</b><hr />';
		$post .= self::parseBBCode($text, $smiles);
		return $post;
	}
	
	public static function hasAccess($board_id) {
		global $sections, $logged, $account_logged, $logged_access;
		if(!isset($sections[$board_id]))
			return false;
		
		$hasAccess = true;
		$section = $sections[$board_id];
		if($section['guild'] > 0) {
			if($logged) {
				$guild = new OTS_Guild();
				$guild->load($section['guild']);
				$status = false;
				if($guild->isLoaded()) {
					$account_players = $account_logged->getPlayers();
					foreach ($account_players as $player) {
						if($guild->hasMember($player)) {
							$status = true;
						}
					}
				}
				
				if (!$status) $hasAccess = false;
			}
			else {
				$hasAccess = false;
			}
		}
		
		if($section['access'] > 0) {
			if($logged_access < $section['access']) {
				$hasAccess = false;
			}
		}
		
		return $hasAccess;
	}
}
