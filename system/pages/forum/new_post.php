<?php
/**
 * New forum post
 *
 * @package   MyAAC
 * @author    Gesior <jerzyskalski@wp.pl>
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2017 MyAAC
 * @version   0.6.1
 * @link      http://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');

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

?>