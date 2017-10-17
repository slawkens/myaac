<?php
/**
 * Edit forum post
 *
 * @package   MyAAC
 * @author    Gesior <jerzyskalski@wp.pl>
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2017 MyAAC
 * @version   0.6.0
 * @link      http://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');

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

?>