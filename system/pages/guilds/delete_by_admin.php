<?php
/**
 * Delete by admin
 *
 * @package   MyAAC
 * @author    Gesior <jerzyskalski@wp.pl>
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2017 MyAAC
 * @version   0.6.1
 * @link      http://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');

$guild_name = $_REQUEST['guild'];
if(!Validator::guildName($guild_name)) {
	$errors[] = Validator::getLastError();
}

if(empty($errors)) {
	$guild = new OTS_Guild();
	$guild->find($guild_name);
	if(!$guild->isLoaded()) {
		$errors[] = 'Guild with name <b>' . $guild_name . '</b> doesn\'t exist.';
	}
}

if(empty($errors)) {
	if($logged) {
		if(admin()) {
			$saved = false;
			if(isset($_POST['todo']) && $_POST['todo'] == 'save') {
				delete_guild($guild->getId());
				$saved = true;
			}
			
			if($saved) {
				echo $twig->render('success.html.twig', array(
					'title' => 'Guild Deleted',
					'description' => 'Guild with name <b>' . $guild_name . '</b> has been deleted.',
					'custom_buttons' => '<center><form action="?subtopic=guilds" METHOD=post><div class="BigButton" style="background-image:url(' . $template_path . '/images/buttons/sbutton.gif)" ><div onMouseOver="MouseOverBigButton(this);" onMouseOut="MouseOutBigButton(this);" ><div class="BigButtonOver" style="background-image:url(' . $template_path . '/images/buttons/sbutton_over.gif);" ></div><input class="ButtonText" type="image" name="Back" alt="Back" src="'.$template_path.'/images/buttons/_sbutton_back.gif" ></div></div></form></center>'
				));
			}
			else {
				echo $twig->render('success.html.twig', array(
					'title' => 'Delete Guild',
					'description' => 'Are you sure you want delete guild <b>' . $guild_name . '</b>?<br>
				<form action="?subtopic=guilds&guild=' . $guild->getName() . '&action=delete_by_admin" METHOD="post"><input type="hidden" name="todo" value="save"><input type="submit" value="Yes, delete"></form>',
					'custom_buttons' => '<center><form action="?subtopic=guilds" METHOD=post><div class="BigButton" style="background-image:url('.$template_path.'/images/buttons/sbutton.gif)" ><div onMouseOver="MouseOverBigButton(this);" onMouseOut="MouseOutBigButton(this);" ><div class="BigButtonOver" style="background-image:url('.$template_path.'/images/buttons/sbutton_over.gif);" ></div><input class="ButtonText" type="image" name="Back" alt="Back" src="'.$template_path.'/images/buttons/_sbutton_back.gif" ></div></div></form></center>'
				));
			}
		}
		else {
			$errors[] = 'You are not an admin!';
		}
	}
	else {
		$errors[] = "You are not logged. You can't delete guild.";
	}
}
if(!empty($errors)) {
	echo $twig->render('error_box.html.twig', array('errors' => $errors));
	
	echo '<br/><center><form action="?subtopic=guilds" METHOD=post><div class="BigButton" style="background-image:url('.$template_path.'/images/buttons/sbutton.gif)" ><div onMouseOver="MouseOverBigButton(this);" onMouseOut="MouseOutBigButton(this);" ><div class="BigButtonOver" style="background-image:url('.$template_path.'/images/buttons/sbutton_over.gif);" ></div><input class="ButtonText" type="image" name="Back" alt="Back" src="'.$template_path.'/images/buttons/_sbutton_back.gif" ></div></div></form></center>';
}

?>