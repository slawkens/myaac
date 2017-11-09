<?php
/**
 * Show forum board
 *
 * @package   MyAAC
 * @author    Gesior <jerzyskalski@wp.pl>
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2017 MyAAC
 * @link      http://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');

$links_to_pages = '';
$section_id = isset($_REQUEST['id']) ? (int) $_REQUEST['id'] : null;

if($section_id == null || !isset($sections[$section_id])) {
	echo "Board with this id does't exist.";
	return;
}

if(!Forum::hasAccess($section_id)) {
	echo "You don't have access to this board.";
	return;
}

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

?>