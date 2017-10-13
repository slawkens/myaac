<?php
/**
 * Forum
 *
 * @package   MyAAC
 * @author    Gesior <jerzyskalski@wp.pl>
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2017 MyAAC
 * @version   0.5.1
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

function parseSmiles($text)
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

function parseBBCode($text, $smiles)
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

	return ($smiles == 0 ? parseSmiles($text) : $text);
}

function showPost($topic, $text, $smiles)
{
	$text = nl2br($text);
	$post = '';
	if(!empty($topic))
		$post .= '<b>'.($smiles == 0 ? parseSmiles($topic) : $topic).'</b><hr />';
	$post .= parseBBCode($text, $smiles);
	return $post;
}
if(!$logged)
	echo  'You are not logged in. <a href="?subtopic=accountmanagement&redirect=' . BASE_URL . urlencode('?subtopic=forum') . '">Log in</a> to post on the forum.<br /><br />';

$canEdit = hasFlag(FLAG_CONTENT_FORUM) || superAdmin();
if($canEdit)
{
	if(!empty($action))
	{
		if($action == 'delete_board' || $action == 'edit_board' || $action == 'hide_board' || $action == 'moveup_board' || $action == 'movedown_board')
			$id = $_REQUEST['id'];
		
		if(isset($_REQUEST['name']))
			$name = $_REQUEST['name'];
		
		if(isset($_REQUEST['description']))
			$description = stripslashes($_REQUEST['description']);
		
		$errors = array();
		
		if($action == 'add_board') {
			if(Forum::add_board($name, $description, $errors))
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
				$description = $board['description'];
			}
			else {
				Forum::update_board($id, $name, $description);
				$action = $name = $description = '';
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
		echo $twig->render('forum.add_board.html.twig', array(
			'link' => getLink('forum', ($action == 'edit_board' ? 'edit_board' : 'add_board')),
			'action' => $action,
			'id' => isset($id) ? $id : null,
			'name' => isset($name) ? $name : null,
			'description' => isset($description) ? $description : null
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
		'closed' => $section['closed'] == '1'
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
	
	echo $twig->render('forum.boards.html.twig', array(
		'boards' => $boards,
		'canEdit' => $canEdit,
		'last' => count($sections)
	));
	
	return;
}

$links_to_pages = '';
if($action == 'show_board')
{
	$section_id = (int) $_REQUEST['id'];
	$_page = (int) (isset($_REQUEST['page']) ? $_REQUEST['page'] : 0);
	$threads_count = $db->query("SELECT COUNT(`" . TABLE_PREFIX . "forum`.`id`) AS threads_count FROM `players`, `" . TABLE_PREFIX . "forum` WHERE `players`.`id` = `" . TABLE_PREFIX . "forum`.`author_guid` AND `" . TABLE_PREFIX . "forum`.`section` = ".(int) $section_id." AND `" . TABLE_PREFIX . "forum`.`first_post` = `" . TABLE_PREFIX . "forum`.`id`")->fetch();
	for($i = 0; $i < $threads_count['threads_count'] / $config['forum_threads_per_page']; $i++)
	{
		if($i != $_page)
			$links_to_pages .= '<a href="' . getForumBoardLink($section_id, $i) . '">'.($i + 1).'</a> ';
		else
			$links_to_pages .= '<b>'.($i + 1).' </b>';
	}
	echo '<a href="' . getLink('forum') . '">Boards</a> >> <b>'.$sections[$section_id]['name'].'</b>';
	if(!$sections[$section_id]['closed'] || Forum::isModerator())
	{
		echo '<br /><br />
		<a href="?subtopic=forum&action=new_thread&section_id='.$section_id.'"><img src="images/forum/topic.gif" border="0" /></a>';
	}

	echo '<br /><br />Page: '.$links_to_pages.'<br />';
	$last_threads = $db->query("SELECT `players`.`id` as `player_id`, `players`.`name`, `" . TABLE_PREFIX . "forum`.`post_text`, `" . TABLE_PREFIX . "forum`.`post_topic`, `" . TABLE_PREFIX . "forum`.`id`, `" . TABLE_PREFIX . "forum`.`last_post`, `" . TABLE_PREFIX . "forum`.`replies`, `" . TABLE_PREFIX . "forum`.`views`, `" . TABLE_PREFIX . "forum`.`post_date` FROM `players`, `" . TABLE_PREFIX . "forum` WHERE `players`.`id` = `" . TABLE_PREFIX . "forum`.`author_guid` AND `" . TABLE_PREFIX . "forum`.`section` = ".(int) $section_id." AND `" . TABLE_PREFIX . "forum`.`first_post` = `" . TABLE_PREFIX . "forum`.`id` ORDER BY `" . TABLE_PREFIX . "forum`.`last_post` DESC LIMIT ".$config['forum_threads_per_page']." OFFSET ".($_page * $config['forum_threads_per_page']))->fetchAll();
	if(isset($last_threads[0]))
	{
		echo '<table width="100%"><tr bgcolor="'.$config['vdarkborder'].'" align="center"><td><font color="white" size="1"><b>Thread</b></font></td><td><font color="white" size="1"><b>Thread Starter</b></font></td><td><font color="white" size="1"><b>Replies</b></font></td><td><font color="white" size="1"><b>Views</b></font></td><td><font color="white" size="1"><b>Last Post</b></font></td></tr>';
		
		$player = new OTS_Player();
		foreach($last_threads as $thread)
		{
			echo '<tr bgcolor="' . getStyle($number_of_rows++) . '"><td>';
			if(Forum::isModerator())
			{
				echo '<a href="?subtopic=forum&action=move_thread&id='.$thread['id'].'"\')"><span style="color:darkgreen">[MOVE]</span></a>';
				echo '<a href="?subtopic=forum&action=remove_post&id='.$thread['id'].'" onclick="return confirm(\'Are you sure you want remove thread > '.$thread['post_topic'].' <?\')"><font color="red">[REMOVE]</font></a>  ';
			}
			
			$player->load($thread['player_id']);
			if(!$player->isLoaded()) {
				error('Forum error: Player not loaded.');
				die();
			}

			$player_account = $player->getAccount();
			$canEditForum = $player_account->hasFlag(FLAG_CONTENT_FORUM) || $player_account->isAdmin();
			
			echo '<a href="' . getForumThreadLink($thread['id']) . '">'.($canEditForum ? $thread['post_topic'] : htmlspecialchars($thread['post_topic'])) . '</a><br /><small>'.($canEditForum ? substr(strip_tags($thread['post_text']), 0, 50) : htmlspecialchars(substr($thread['post_text'], 0, 50))).'...</small></td><td>' . getPlayerLink($thread['name']) . '</td><td>'.(int) $thread['replies'].'</td><td>'.(int) $thread['views'].'</td><td>';
			if($thread['last_post'] > 0)
			{
				$last_post = $db->query("SELECT `players`.`name`, `" . TABLE_PREFIX . "forum`.`post_date` FROM `players`, `" . TABLE_PREFIX . "forum` WHERE `" . TABLE_PREFIX . "forum`.`first_post` = ".(int) $thread['id']." AND `players`.`id` = `" . TABLE_PREFIX . "forum`.`author_guid` ORDER BY `post_date` DESC LIMIT 1")->fetch();
				if(isset($last_post['name']))
					echo date('d.m.y H:i:s', $last_post['post_date']).'<br />by ' . getPlayerLink($last_post['name']);
				else
					echo 'No posts.';
			}
			else
				echo date('d.m.y H:i:s', $thread['post_date']).'<br />by ' . getPlayerLink($thread['name']);
			echo '</td></tr>';
		}
		echo '</table>';
		if(!$sections[$section_id]['closed'] || Forum::isModerator())
			echo '<br /><a href="?subtopic=forum&action=new_thread&section_id='.$section_id.'"><img src="images/forum/topic.gif" border="0" /></a>';
	}
	else
		echo '<h3>No threads in this board.</h3>';
	return;
}
if($action == 'show_thread')
{
	$thread_id = (int) $_REQUEST['id'];
	$_page = (int) (isset($_REQUEST['page']) ? $_REQUEST['page'] : 0);
	$thread_name = $db->query("SELECT `players`.`name`, `" . TABLE_PREFIX . "forum`.`post_topic` FROM `players`, `" . TABLE_PREFIX . "forum` WHERE `" . TABLE_PREFIX . "forum`.`first_post` = ".(int) $thread_id." AND `" . TABLE_PREFIX . "forum`.`id` = `" . TABLE_PREFIX . "forum`.`first_post` AND `players`.`id` = `" . TABLE_PREFIX . "forum`.`author_guid` LIMIT 1")->fetch();
	if(!empty($thread_name['name']))
	{
		$posts_count = $db->query("SELECT COUNT(`" . TABLE_PREFIX . "forum`.`id`) AS posts_count FROM `players`, `" . TABLE_PREFIX . "forum` WHERE `players`.`id` = `" . TABLE_PREFIX . "forum`.`author_guid` AND `" . TABLE_PREFIX . "forum`.`first_post` = ".(int) $thread_id)->fetch();
		for($i = 0; $i < $posts_count['posts_count'] / $config['forum_threads_per_page']; $i++)
		{
			if($i != $_page)
				$links_to_pages .= '<a href="' . getForumThreadLink($thread_id, $i) . '">'.($i + 1).'</a> ';
			else
				$links_to_pages .= '<b>'.($i + 1).' </b>';
		}
		$threads = $db->query("SELECT `players`.`id` as `player_id`, `players`.`name`, `players`.`account_id`, `players`.`vocation`" . (fieldExist('promotion', 'players') ? ", `players`.`promotion`" : "") . ", `players`.`level`, `" . TABLE_PREFIX . "forum`.`id`,`" . TABLE_PREFIX . "forum`.`first_post`, `" . TABLE_PREFIX . "forum`.`section`,`" . TABLE_PREFIX . "forum`.`post_text`, `" . TABLE_PREFIX . "forum`.`post_topic`, `" . TABLE_PREFIX . "forum`.`post_date`, `" . TABLE_PREFIX . "forum`.`post_smile`, `" . TABLE_PREFIX . "forum`.`author_aid`, `" . TABLE_PREFIX . "forum`.`author_guid`, `" . TABLE_PREFIX . "forum`.`last_edit_aid`, `" . TABLE_PREFIX . "forum`.`edit_date` FROM `players`, `" . TABLE_PREFIX . "forum` WHERE `players`.`id` = `" . TABLE_PREFIX . "forum`.`author_guid` AND `" . TABLE_PREFIX . "forum`.`first_post` = ".(int) $thread_id." ORDER BY `" . TABLE_PREFIX . "forum`.`post_date` LIMIT ".$config['forum_posts_per_page']." OFFSET ".($_page * $config['forum_posts_per_page']))->fetchAll();
		if(isset($threads[0]['name']))
			$db->query("UPDATE `" . TABLE_PREFIX . "forum` SET `views`=`views`+1 WHERE `id` = ".(int) $thread_id);
		echo '<a href="' . getLink('forum') . '">Boards</a> >> <a href="' . getForumBoardLink($threads[0]['section']) . '">'.$sections[$threads[0]['section']]['name'].'</a> >> <b>'.$thread_name['post_topic'].'</b>';
		echo '<br /><br /><a href="?subtopic=forum&action=new_post&thread_id='.$thread_id.'"><img src="images/forum/post.gif" border="0" /></a><br /><br />Page: '.$links_to_pages.'<br /><table width="100%"><tr bgcolor="'.$config['lightborder'].'" width="100%"><td colspan="2"><font size="4"><b>'.htmlspecialchars($thread_name['post_topic']).'</b></font><font size="1"><br />by ' . getPlayerLink($thread_name['name']) . '</font></td></tr><tr bgcolor="'.$config['vdarkborder'].'"><td width="200"><font color="white" size="1"><b>Author</b></font></td><td>&nbsp;</td></tr>';
		$player = $ots->createObject('Player');
		foreach($threads as $thread)
		{
			$player->load($thread['player_id']);
			if(!$player->isLoaded()) {
				error('Forum error: Player not loaded.');
				die();
			}
		
			echo '<tr bgcolor="' . getStyle($number_of_rows++) . '"><td valign="top">' . getPlayerLink($thread['name']) . '<br /><br /><font size="1">Profession: '.$config['vocations'][$player->getVocation()].'<br />Level: '.$thread['level'].'<br />';
	
			$rank = $player->getRank();
			if($rank->isLoaded())
			{
				$guild = $rank->getGuild();
				if($guild->isLoaded())
					echo $rank->getName().' of <a href="'.getGuildLink($guild->getName(), false).'">'.$guild->getName().'</a><br />';
			}
			$player_account = $player->getAccount();
			$canEditForum = $player_account->hasFlag(FLAG_CONTENT_FORUM) || $player_account->isAdmin();
			
			$posts = $db->query("SELECT COUNT(`id`) AS 'posts' FROM `" . TABLE_PREFIX . "forum` WHERE `author_aid`=".(int) $thread['account_id'])->fetch();
			echo '<br />Posts: '.(int) $posts['posts'].'<br /></font></td><td valign="top">'.showPost(($canEditForum ? $thread['post_topic'] : htmlspecialchars($thread['post_topic'])), ($canEditForum ? $thread['post_text'] : htmlspecialchars($thread['post_text'])), $thread['post_smile']).'</td></tr>
			<tr bgcolor="'.getStyle($number_of_rows++).'"><td><font size="1">'.date('d.m.y H:i:s', $thread['post_date']);
			if($thread['edit_date'] > 0)
			{
				if($thread['last_edit_aid'] != $thread['author_aid'])
					echo '<br />Edited by moderator';
				else
					echo '<br />Edited by '.$thread['name'];
				echo '<br />on '.date('d.m.y H:i:s', $thread['edit_date']);
			}
			echo '</font></td><td>';
			if(Forum::isModerator())
				if($thread['first_post'] != $thread['id'])
					echo '<a href="?subtopic=forum&action=remove_post&id='.$thread['id'].'" onclick="return confirm(\'Are you sure you want remove post of '.$thread['name'].'?\')"><font color="red">REMOVE POST</font></a>';
				else
				{
					echo '<a href="?subtopic=forum&action=move_thread&id='.$thread['id'].'"\')"><span style="color:darkgreen">[MOVE]</span></a>';
					echo '<br/><a href="?subtopic=forum&action=remove_post&id='.$thread['id'].'" onclick="return confirm(\'Are you sure you want remove thread > '.$thread['post_topic'].' <?\')"><font color="red">REMOVE THREAD</font></a>';
				}
			if($logged && ($thread['account_id'] == $account_logged->getId() || Forum::isModerator()))
				echo '<br/><a href="?subtopic=forum&action=edit_post&id='.$thread['id'].'">EDIT POST</a>';
			if($logged)
				echo '<br/><a href="?subtopic=forum&action=new_post&thread_id='.$thread_id.'&quote='.$thread['id'].'">Quote</a>';
			echo '</td></tr>';
		}
		echo '</table><br /><a href="?subtopic=forum&action=new_post&thread_id='.$thread_id.'"><img src="images/forum/post.gif" border="0" /></a>';
	}
	else
		echo 'Thread with this ID does not exits.';

	return;
}

if(!$logged)
{
	header('Location: ' . BASE_URL . '?subtopic=accountmanagement&redirect=' . BASE_URL . urlencode('?subtopic=forum'));
	return;
}

if($action == 'remove_post')
{
	if(Forum::isModerator())
	{
		$id = (int) $_REQUEST['id'];
		$post = $db->query("SELECT `id`, `first_post`, `section` FROM `" . TABLE_PREFIX . "forum` WHERE `id` = ".$id." LIMIT 1")->fetch();
		if($post['id'] == $id)
		{
			if($post['id'] == $post['first_post'])
			{
				$db->query("DELETE FROM `" . TABLE_PREFIX . "forum` WHERE `first_post` = ".$post['id']);
				header('Location: ' . getForumBoardLink($post['section']));
			}
			else
			{
				$post_page = $db->query("SELECT COUNT(`" . TABLE_PREFIX . "forum`.`id`) AS posts_count FROM `players`, `" . TABLE_PREFIX . "forum` WHERE `players`.`id` = `" . TABLE_PREFIX . "forum`.`author_guid` AND `" . TABLE_PREFIX . "forum`.`id` < ".$id." AND `" . TABLE_PREFIX . "forum`.`first_post` = ".(int) $post['first_post'])->fetch();
				$_page = (int) ceil($post_page['posts_count'] / $config['forum_threads_per_page']) - 1;
				$db->query("DELETE FROM `" . TABLE_PREFIX . "forum` WHERE `id` = ".$post['id']);
				header('Location: ' . getForumThreadLink($post['first_post'], (int) $_page));
			}
		}
		else
			echo 'Post with ID ' . $id . ' does not exist.';
	}
	else
		echo 'You are not logged in or you are not moderator.';
}
if($action == 'new_post')
{
	if(Forum::canPost($account_logged))
	{
		$players_from_account = $db->query("SELECT `players`.`name`, `players`.`id` FROM `players` WHERE `players`.`account_id` = ".(int) $account_logged->getId())->fetchAll();
		$thread_id = (int) $_REQUEST['thread_id'];
		$thread = $db->query("SELECT `" . TABLE_PREFIX . "forum`.`post_topic`, `" . TABLE_PREFIX . "forum`.`id`, `" . TABLE_PREFIX . "forum`.`section` FROM `" . TABLE_PREFIX . "forum` WHERE `" . TABLE_PREFIX . "forum`.`id` = ".(int) $thread_id." AND `" . TABLE_PREFIX . "forum`.`first_post` = ".(int) $thread_id." LIMIT 1")->fetch();
		echo '<a href="' . getLink('forum') . '">Boards</a> >> <a href="' . getForumBoardLink($thread['section']) . '">'.$sections[$thread['section']]['name'].'</a> >> <a href="' . getForumThreadLink($thread_id) . '">'.$thread['post_topic'].'</a> >> <b>Post new reply</b><br /><h3>'.$thread['post_topic'].'</h3>';
		if(isset($thread['id']))
		{
			$quote = isset($_REQUEST['quote']) ? (int) $_REQUEST['quote'] : NULL;
			$text = isset($_REQUEST['text']) ? stripslashes(trim($_REQUEST['text'])) : NULL;
			$char_id = (int) (isset($_REQUEST['char_id']) ? $_REQUEST['char_id'] : 0);
			$post_topic = isset($_REQUEST['topic']) ? stripslashes(trim($_REQUEST['topic'])) : '';
			$smile = (int) (isset($_REQUEST['smile']) ? $_REQUEST['smile'] : 0);
			$saved = false;
			if(isset($_REQUEST['quote']))
			{
				$quoted_post = $db->query("SELECT `players`.`name`, `" . TABLE_PREFIX . "forum`.`post_text`, `" . TABLE_PREFIX . "forum`.`post_date` FROM `players`, `" . TABLE_PREFIX . "forum` WHERE `players`.`id` = `" . TABLE_PREFIX . "forum`.`author_guid` AND `" . TABLE_PREFIX . "forum`.`id` = ".(int) $quote)->fetchAll();
				if(isset($quoted_post[0]['name']))
					$text = '[i]Originally posted by '.$quoted_post[0]['name'].' on '.date('d.m.y H:i:s', $quoted_post[0]['post_date']).':[/i][quote]'.$quoted_post[0]['post_text'].'[/quote]';
			}
			elseif(isset($_REQUEST['save']))
			{
				$lenght = 0;
				for($i = 0; $i < strlen($text); $i++)
				{
					if(ord($text[$i]) >= 33 && ord($text[$i]) <= 126)
						$lenght++;
				}
				if($lenght < 1 || strlen($text) > 15000)
					$errors[] = 'Too short or too long post (short: '.$lenght.' long: '.strlen($text).' letters). Minimum 1 letter, maximum 15000 letters.';
				if($char_id == 0)
					$errors[] = 'Please select a character.';
				$player_on_account = false;
				if(count($errors) == 0)
				{
					foreach($players_from_account as $player)
						if($char_id == $player['id'])
							$player_on_account = true;
					if(!$player_on_account)
						$errors[] = 'Player with selected ID '.$char_id.' doesn\'t exist or isn\'t on your account';
				}
				if(count($errors) == 0)
				{
					$last_post = 0;
					$query = $db->query('SELECT post_date FROM ' . TABLE_PREFIX . 'forum ORDER BY post_date DESC LIMIT 1');
					if($query->rowCount() > 0)
					{
						$query = $query->fetch();
						$last_post = $query['post_date'];
					}
					if($last_post+$config['forum_post_interval']-time() > 0 && !Forum::isModerator())
						$errors[] = 'You can post one time per '.$config['forum_post_interval'].' seconds. Next post after '.($last_post+$config['forum_post_interval']-time()).' second(s).';
				}
				if(count($errors) == 0)
				{
					$saved = true;
					$db->query("INSERT INTO `" . TABLE_PREFIX . "forum` (`id` ,`first_post` ,`last_post` ,`section` ,`replies` ,`views` ,`author_aid` ,`author_guid` ,`post_text` ,`post_topic` ,`post_smile` ,`post_date` ,`last_edit_aid` ,`edit_date`, `post_ip`) VALUES (NULL, '".$thread['id']."', '0', '".$thread['section']."', '0', '0', '".$account_logged->getId()."', '".(int) $char_id."', ".$db->quote($text).", ".$db->quote($post_topic).", '".(int) $smile."', '".time()."', '0', '0', '".$_SERVER['REMOTE_ADDR']."')");
					$db->query("UPDATE `" . TABLE_PREFIX . "forum` SET `replies`=`replies`+1, `last_post`=".time()." WHERE `id` = ".(int) $thread_id);
					$post_page = $db->query("SELECT COUNT(`" . TABLE_PREFIX . "forum`.`id`) AS posts_count FROM `players`, `" . TABLE_PREFIX . "forum` WHERE `players`.`id` = `" . TABLE_PREFIX . "forum`.`author_guid` AND `" . TABLE_PREFIX . "forum`.`post_date` <= ".time()." AND `" . TABLE_PREFIX . "forum`.`first_post` = ".(int) $thread['id'])->fetch();
					$_page = (int) ceil($post_page['posts_count'] / $config['forum_threads_per_page']) - 1;
					header('Location: ' . getForumThreadLink($thread_id, $_page));
					echo '<br />Thank you for posting.<br /><a href="' . getForumThreadLink($thread_id, $_page) . '">GO BACK TO LAST THREAD</a>';
				}
			}
			if(!$saved)
			{
				if(!empty($errors))
					echo $twig->render('error_box.html.twig', array('errors' => $errors));

				echo '<form action="?" method="POST">
					<input type="hidden" name="action" value="new_post" />
					<input type="hidden" name="thread_id" value="'.$thread_id.'" />
					<input type="hidden" name="subtopic" value="forum" />
					<input type="hidden" name="save" value="save" />
					<table width="100%">
						<tr bgcolor="'.$config['vdarkborder'].'">
							<td colspan="2"><font color="white"><b>Post New Reply</b></font></td>
						</tr>
						<tr bgcolor="'.$config['darkborder'].'">
							<td width="180"><b>Character:</b></td>
							<td>
								<select name="char_id">
									<option value="0">(Choose character)</option>';
				foreach($players_from_account as $player)
				{
					echo '<option value="'.$player['id'].'"';
					if($player['id'] == $char_id)
						echo ' selected="selected"';
					echo '>'.$player['name'].'</option>';
				}
				echo '</select></td></tr><tr bgcolor="'.$config['lightborder'].'"><td><b>Topic:</b></td><td><input type="text" name="topic" value="'.htmlspecialchars($post_topic).'" size="40" maxlength="60" /> (Optional)</td></tr>
				<tr bgcolor="'.$config['darkborder'].'"><td valign="top"><b>Message:</b><font size="1"><br />You can use:<br />[player]Nick[/player]<br />[url]http://address.com/[/url]<br />[img]http://images.com/images3.gif[/img]<br />[code]Code[/code]<br />[b]<b>Text</b>[/b]<br />[i]<i>Text</i>[/i]<br />[u]<u>Text</u>[/u]<br />and smileys:<br />;) , :) , :D , :( , :rolleyes:<br />:cool: , :eek: , :o , :p</font></td><td><textarea rows="10" cols="60" name="text">'.htmlspecialchars($text).'</textarea><br />(Max. 15,000 letters)</td></tr>
				<tr bgcolor="'.$config['lightborder'].'"><td valign="top">Options:</td><td><label><input type="checkbox" name="smile" value="1"';
				if($smile == 1)
					echo ' checked="checked"';
				echo '/>Disable Smileys in This Post </label></td></tr></table><center><input type="submit" value="Post Reply" /></center></form>';
				$threads = $db->query("SELECT `players`.`name`, `" . TABLE_PREFIX . "forum`.`post_text`, `" . TABLE_PREFIX . "forum`.`post_topic`, `" . TABLE_PREFIX . "forum`.`post_smile` FROM `players`, `" . TABLE_PREFIX . "forum` WHERE `players`.`id` = `" . TABLE_PREFIX . "forum`.`author_guid` AND `" . TABLE_PREFIX . "forum`.`first_post` = ".(int) $thread_id." ORDER BY `" . TABLE_PREFIX . "forum`.`post_date` DESC LIMIT 10")->fetchAll();
				echo '<table width="100%"><tr bgcolor="'.$config['vdarkborder'].'"><td colspan="2"><font color="white"><b>Last 5 posts from thread: '.$thread['post_topic'].'</b></font></td></tr>';
				foreach($threads as $thread)
				{
					echo '<tr bgcolor="' . getStyle($number_of_rows++) . '"><td>'.$thread['name'].'</td><td>'.showPost($thread['post_topic'], $thread['post_text'], $thread['post_smile']).'</td></tr>';
				}
				echo '</table>';
			}
		}
		else
			echo 'Thread with ID '.$thread_id.' doesn\'t exist.';
	}
	else
		echo "Your account is banned, deleted or you don't have any player with level " . $config['forum_level_required'] . " on your account. You can't post.";
}

if($action == 'edit_post')
{
	if(Forum::canPost($account_logged))
	{
		$post_id = (int) $_REQUEST['id'];
		$thread = $db->query("SELECT `" . TABLE_PREFIX . "forum`.`author_guid`, `" . TABLE_PREFIX . "forum`.`author_aid`, `" . TABLE_PREFIX . "forum`.`first_post`, `" . TABLE_PREFIX . "forum`.`post_topic`, `" . TABLE_PREFIX . "forum`.`post_date`, `" . TABLE_PREFIX . "forum`.`post_text`, `" . TABLE_PREFIX . "forum`.`post_smile`, `" . TABLE_PREFIX . "forum`.`id`, `" . TABLE_PREFIX . "forum`.`section` FROM `" . TABLE_PREFIX . "forum` WHERE `" . TABLE_PREFIX . "forum`.`id` = ".(int) $post_id." LIMIT 1")->fetch();
		if(isset($thread['id']))
		{
			$first_post = $db->query("SELECT `" . TABLE_PREFIX . "forum`.`author_guid`, `" . TABLE_PREFIX . "forum`.`author_aid`, `" . TABLE_PREFIX . "forum`.`first_post`, `" . TABLE_PREFIX . "forum`.`post_topic`, `" . TABLE_PREFIX . "forum`.`post_text`, `" . TABLE_PREFIX . "forum`.`post_smile`, `" . TABLE_PREFIX . "forum`.`id`, `" . TABLE_PREFIX . "forum`.`section` FROM `" . TABLE_PREFIX . "forum` WHERE `" . TABLE_PREFIX . "forum`.`id` = ".(int) $thread['first_post']." LIMIT 1")->fetch();
			echo '<a href="' . getLink('forum') . '">Boards</a> >> <a href="' . getForumBoardLink($thread['section']) . '">'.$sections[$thread['section']]['name'].'</a> >> <a href="' . getForumThreadLink($thread['first_post']) . '">'.$first_post['post_topic'].'</a> >> <b>Edit post</b>';
			if($account_logged->getId() == $thread['author_aid'] || Forum::isModerator())
			{
				$players_from_account = $db->query("SELECT `players`.`name`, `players`.`id` FROM `players` WHERE `players`.`account_id` = ".(int) $account_logged->getId())->fetchAll();
				$saved = false;
				if(isset($_REQUEST['save']))
				{
					$text = stripslashes(trim($_REQUEST['text']));
					$char_id = (int) $_REQUEST['char_id'];
					$post_topic = stripslashes(trim($_REQUEST['topic']));
					$smile = (int) $_REQUEST['smile'];
					$lenght = 0;
					for($i = 0; $i <= strlen($post_topic); $i++)
					{
						if(ord($post_topic[$i]) >= 33 && ord($post_topic[$i]) <= 126)
							$lenght++;
					}
					if(($lenght < 1 || strlen($post_topic) > 60) && $thread['id'] == $thread['first_post'])
						$errors[] = 'Too short or too long topic (short: '.$lenght.' long: '.strlen($post_topic).' letters). Minimum 1 letter, maximum 60 letters.';
					$lenght = 0;
					for($i = 0; $i <= strlen($text); $i++)
					{
						if(ord($text[$i]) >= 33 && ord($text[$i]) <= 126)
							$lenght++;
					}
					if($lenght < 1 || strlen($text) > 15000)
						$errors[] = 'Too short or too long post (short: '.$lenght.' long: '.strlen($text).' letters). Minimum 1 letter, maximum 15000 letters.';
					if($char_id == 0)
						$errors[] = 'Please select a character.';
					if(empty($post_topic) && $thread['id'] == $thread['first_post'])
						$errors[] = 'Thread topic can\'t be empty.';
					$player_on_account == false;
					if(count($errors) == 0)
					{
						foreach($players_from_account as $player)
							if($char_id == $player['id'])
								$player_on_account = true;
						if(!$player_on_account)
							$errors[] = 'Player with selected ID '.$char_id.' doesn\'t exist or isn\'t on your account';
					}
					if(count($errors) == 0)
					{
						$saved = true;
						if($account_logged->getId() != $thread['author_aid'])
							$char_id = $thread['author_guid'];
						$db->query("UPDATE `" . TABLE_PREFIX . "forum` SET `author_guid` = ".(int) $char_id.", `post_text` = ".$db->quote($text).", `post_topic` = ".$db->quote($post_topic).", `post_smile` = ".(int) $smile.", `last_edit_aid` = ".(int) $account_logged->getId().",`edit_date` = ".time()." WHERE `id` = ".(int) $thread['id']);
						$post_page = $db->query("SELECT COUNT(`" . TABLE_PREFIX . "forum`.`id`) AS posts_count FROM `players`, `" . TABLE_PREFIX . "forum` WHERE `players`.`id` = `" . TABLE_PREFIX . "forum`.`author_guid` AND `" . TABLE_PREFIX . "forum`.`post_date` <= ".$thread['post_date']." AND `" . TABLE_PREFIX . "forum`.`first_post` = ".(int) $thread['first_post'])->fetch();
						$_page = (int) ceil($post_page['posts_count'] / $config['forum_threads_per_page']) - 1;
						header('Location: ' . getForumThreadLink($thread['first_post'], $_page));
						echo '<br />Thank you for editing post.<br /><a href="' . getForumThreadLink($thread['first_post'], $_page) . '">GO BACK TO LAST THREAD</a>';
					}
				}
				else
				{
					$text = $thread['post_text'];
					$char_id = (int) $thread['author_guid'];
					$post_topic = $thread['post_topic'];
					$smile = (int) $thread['post_smile'];
				}
				if(!$saved)
				{
					if(!empty($errors))
						echo $twig->render('error_box.html.twig', array('errors' => $errors));

					echo '<br /><form action="?" method="POST"><input type="hidden" name="action" value="edit_post" /><input type="hidden" name="id" value="'.$post_id.'" /><input type="hidden" name="subtopic" value="forum" /><input type="hidden" name="save" value="save" /><table width="100%"><tr bgcolor="'.$config['vdarkborder'].'"><td colspan="2"><font color="white"><b>Edit Post</b></font></td></tr><tr bgcolor="'.$config['darkborder'].'"><td width="180"><b>Character:</b></td><td><select name="char_id"><option value="0">(Choose character)</option>';
					foreach($players_from_account as $player)
					{
						echo '<option value="'.$player['id'].'"';
						if($player['id'] == $char_id)
							echo ' selected="selected"';
						echo '>'.$player['name'].'</option>';
					}
					echo '</select></td></tr><tr bgcolor="'.$config['lightborder'].'"><td><b>Topic:</b></td><td><input type="text" value="'.htmlspecialchars($post_topic).'" name="topic" size="40" maxlength="60" /> (Optional)</td></tr>
					<tr bgcolor="'.$config['darkborder'].'"><td valign="top"><b>Message:</b><font size="1"><br />You can use:<br />[player]Nick[/player]<br />[url]http://address.com/[/url]<br />[img]http://images.com/images3.gif[/img]<br />[code]Code[/code]<br />[b]<b>Text</b>[/b]<br />[i]<i>Text</i>[/i]<br />[u]<u>Text</u>[/u]<br />and smileys:<br />;) , :) , :D , :( , :rolleyes:<br />:cool: , :eek: , :o , :p</font></td><td><textarea rows="10" cols="60" name="text">'.htmlspecialchars($text).'</textarea><br />(Max. 15,000 letters)</td></tr>
					<tr bgcolor="'.$config['lightborder'].'"><td valign="top">Options:</td><td><label><input type="checkbox" name="smile" value="1"';
					if($smile == 1)
						echo ' checked="checked"';
					echo '/>Disable Smileys in This Post </label></td></tr></table><center><input type="submit" value="Save Post" /></center></form>';
				}
			}
			else
				echo '<br />You are not an author of this post.';
		}
		else
			echo '<br />Post with ID '.$post_id.' doesn\'t exist.';
	}
	else
		echo '<br />Your account is banned, deleted or you don\'t have any player with level '.$config['forum_level_required'].' on your account. You can\'t post.';
}

if($action == 'new_thread')
{
	if(Forum::canPost($account_logged))
	{
		$players_from_account = $db->query('SELECT `players`.`name`, `players`.`id` FROM `players` WHERE `players`.`account_id` = '.(int) $account_logged->getId())->fetchAll();
		$section_id = isset($_REQUEST['section_id']) ? $_REQUEST['section_id'] : null;
		if($section_id !== null) {
			echo '<a href="' . getLink('forum') . '">Boards</a> >> <a href="' . getForumBoardLink($section_id) . '">' . $sections[$section_id]['name'] . '</a> >> <b>Post new thread</b><br />';
			if (isset($sections[$section_id]['name'])) {
				if ($sections[$section_id]['closed'] && !Forum::isModerator())
					$errors[] = 'You cannot create topic on this board.';
				
				$quote = (int)(isset($_REQUEST['quote']) ? $_REQUEST['quote'] : 0);
				$text = isset($_REQUEST['text']) ? stripslashes($_REQUEST['text']) : '';
				$char_id = (int)(isset($_REQUEST['char_id']) ? $_REQUEST['char_id'] : 0);
				$post_topic = isset($_REQUEST['topic']) ? stripslashes($_REQUEST['topic']) : '';
				$smile = (int)(isset($_REQUEST['smile']) ? $_REQUEST['smile'] : 0);
				$saved = false;
				if (isset($_REQUEST['save'])) {
					$errors = array();
					
					$lenght = 0;
					for ($i = 0; $i < strlen($post_topic); $i++) {
						if (ord($post_topic[$i]) >= 33 && ord($post_topic[$i]) <= 126)
							$lenght++;
					}
					if ($lenght < 1 || strlen($post_topic) > 60)
						$errors[] = 'Too short or too long topic (short: ' . $lenght . ' long: ' . strlen($post_topic) . ' letters). Minimum 1 letter, maximum 60 letters.';
					$lenght = 0;
					for ($i = 0; $i < strlen($text); $i++) {
						if (ord($text[$i]) >= 33 && ord($text[$i]) <= 126)
							$lenght++;
					}
					if ($lenght < 1 || strlen($text) > 15000)
						$errors[] = 'Too short or too long post (short: ' . $lenght . ' long: ' . strlen($text) . ' letters). Minimum 1 letter, maximum 15000 letters.';
					
					if ($char_id == 0)
						$errors[] = 'Please select a character.';
					$player_on_account = false;
					
					if (count($errors) == 0) {
						foreach ($players_from_account as $player)
							if ($char_id == $player['id'])
								$player_on_account = true;
						if (!$player_on_account)
							$errors[] = 'Player with selected ID ' . $char_id . ' doesn\'t exist or isn\'t on your account';
					}
					
					if (count($errors) == 0) {
						$last_post = 0;
						$query = $db->query('SELECT `post_date` FROM `' . TABLE_PREFIX . 'forum` ORDER BY `post_date` DESC LIMIT 1');
						if ($query->rowCount() > 0) {
							$query = $query->fetch();
							$last_post = $query['post_date'];
						}
						if ($last_post + $config['forum_post_interval'] - time() > 0 && !Forum::isModerator())
							$errors[] = 'You can post one time per ' . $config['forum_post_interval'] . ' seconds. Next post after ' . ($last_post + $config['forum_post_interval'] - time()) . ' second(s).';
					}
					if (count($errors) == 0) {
						$saved = true;
						$db->query("INSERT INTO `" . TABLE_PREFIX . "forum` (`first_post` ,`last_post` ,`section` ,`replies` ,`views` ,`author_aid` ,`author_guid` ,`post_text` ,`post_topic` ,`post_smile` ,`post_date` ,`last_edit_aid` ,`edit_date`, `post_ip`) VALUES ('0', '" . time() . "', '" . (int)$section_id . "', '0', '0', '" . $account_logged->getId() . "', '" . (int)$char_id . "', " . $db->quote($text) . ", " . $db->quote($post_topic) . ", '" . (int)$smile . "', '" . time() . "', '0', '0', '" . $_SERVER['REMOTE_ADDR'] . "')");
						$thread_id = $db->lastInsertId();
						$db->query("UPDATE `" . TABLE_PREFIX . "forum` SET `first_post`=" . (int)$thread_id . " WHERE `id` = " . (int)$thread_id);
						header('Location: ' . getForumThreadLink($thread_id));
						echo '<br />Thank you for posting.<br /><a href="' . getForumThreadLink($thread_id) . '">GO BACK TO LAST THREAD</a>';
					}
				}
				if (!$saved) {
					if (!empty($errors))
						echo $twig->render('error_box.html.twig', array('errors' => $errors));
					
					echo $twig->render('forum.new_thread.html.twig', array(
						'section_id' => $section_id,
						'players' => $players_from_account,
						'post_player_id' => $char_id,
						'post_thread' => $post_topic,
						'text' => $text,
						'smiles_enabled' => $smile > 0
					));
				}
			}
			else
				echo 'Board with ID ' . $board_id . ' doesn\'t exist.';
		}
		else
			echo 'Please enter section_id.';
	}
	else
		echo 'Your account is banned, deleted or you don\'t have any player with level '.$config['forum_level_required'].' on your account. You can\'t post.';
}

//Board Change Function. Scripted by Cybermaster and Absolute Mango
if($action == 'move_thread')
{
	if(Forum::isModerator())
	{
		$id = (int) $_REQUEST['id'];
		$post = $db->query("SELECT `id`, `section`, `first_post`, `post_topic`, `author_guid` FROM `" . TABLE_PREFIX . "forum` WHERE `id` = ".$id." LIMIT 1")->fetch();
		$name= $db->query("SELECT `name` FROM `players` WHERE `id` = ".$post['author_guid']." ")->fetch();
		if($post['id'] == $id)
		{
			if($post['id'] == $post['first_post'])
			{
				echo $twig->render('forum.move_thread.html.twig', array(
					'thread' => $post['post_topic'],
					'author' => $name[0],
					'board' => $sections[$post['section']]['name'],
					'post_id' => $post['id'],
					'sections' => $sections,
					'section_link' => getForumBoardLink($post['section']),
				));
			}
		}
		else
			echo 'Post with ID '.$id.' does not exist.';
	}
	else
		echo 'You are not logged in or you are not moderator.';
}

if($action == 'moved_thread')
{
	if(Forum::isModerator())
	{
		$id = (int) $_REQUEST['id'];
		$board = (int) $_REQUEST['section'];
		$post = $db->query("SELECT `id`, `first_post`, `section` FROM `" . TABLE_PREFIX . "forum` WHERE `id` = ".$id." LIMIT 1")->fetch();
		if($post['id'] == $id)
		{
			if($post['id'] == $post['first_post'])
			{
				$db->query("UPDATE `" . TABLE_PREFIX . "forum` SET `section` = ".$board." WHERE `id` = ".$post['id']."") or die(mysql_error());
				$nPost = $db->query( 'SELECT `section` FROM `' . TABLE_PREFIX . 'forum` WHERE `id` = \''.$id.'\' LIMIT 1;' )->fetch();
				header('Location: ' . getForumBoardLink($nPost['section']));
			}
		}
		else
			echo 'Post with ID ' . $id . ' does not exist.';
	}
	else
		echo 'You are not logged in or you are not moderator.';
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
	
	static public function add_board($name, $description, &$errors)
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
				$db->insert(TABLE_PREFIX . 'forum_boards', array('name' => $name, 'description' => $description, 'ordering' => $ordering));
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
	
	static public function update_board($id, $name, $description) {
		global $db;
		$db->update(TABLE_PREFIX . 'forum_boards', array('name' => $name, 'description' => $description), array('id' => $id));
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
}
