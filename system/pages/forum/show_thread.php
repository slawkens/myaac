<?php
/**
 * Show forum thread
 *
 * @package   MyAAC
 * @author    Gesior <jerzyskalski@wp.pl>
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2017 MyAAC
 * @version   0.6.1
 * @link      http://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');

$links_to_pages = '';
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
		echo '<br />Posts: '.(int) $posts['posts'].'<br /></font></td><td valign="top">'.Forum::showPost(($canEditForum ? $thread['post_topic'] : htmlspecialchars($thread['post_topic'])), ($canEditForum ? $thread['post_text'] : htmlspecialchars($thread['post_text'])), $thread['post_smile']).'</td></tr>
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

?>