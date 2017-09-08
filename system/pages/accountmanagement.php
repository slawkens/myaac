<?php
/**
 * Account management
 *
 * @package   MyAAC
 * @author    Gesior <jerzyskalski@wp.pl>
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2017 MyAAC
 * @version   0.3.0
 * @link      http://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');
$title = 'Account Management';

if($config['account_country'])
	require(SYSTEM . 'countries.conf.php');

$groups = new OTS_Groups_List();

$dontshowtableagain = false;
$config_salt_enabled = fieldExist('salt', 'accounts');
if(!$logged)
{
	if($action == "logout") {
		echo $twig->render('account.logout.html');
	}
	else
	{
		if(!empty($errors))
			echo $twig->render('error_box.html', array('errors' => $errors));
	
		echo $twig->render('account.login.html', array(
			'redirect' => isset($_REQUEST['redirect']) ? $_REQUEST['redirect'] : null,
			'account' => USE_ACCOUNT_NAME ? 'Name' : 'Number',
			'error' => isset($errors[0]) ? $errors[0] : null
		));
	return;
	}
}

	if(isset($_REQUEST['redirect']))
	{
		$redirect = urldecode($_REQUEST['redirect']);

		echo $twig->render('account.redirect.html', array(
			'redirect' => $redirect
		));
		return;
	}

	if($action == '')
	{
		$freePremium = isset($config['lua']['freePremium']) && getBoolean($config['lua']['freePremium']);
		$recovery_key = $account_logged->getCustomField('key');
		if(!$account_logged->isPremium())
			$account_status = '<b><font color="red">Free Account</font></b>';
		else
			$account_status = '<b><font color="green">Premium Account, ' . ($freePremium ? 'Unlimited' : $account_logged->getPremDays() . ' days left') . '</font></b>';
		
		if(empty($recovery_key))
			$account_registered = '<b><font color="red">No</font></b>';
		else
		{
			if($config['generate_new_reckey'] && $config['mail_enabled'])
				$account_registered = '<b><font color="green">Yes ( <a href="?subtopic=accountmanagement&action=newreckey"> Buy new Recovery Key </a> )</font></b>';
			else
				$account_registered = '<b><font color="green">Yes</font></b>';
		}

		$account_created = $account_logged->getCustomField("created");
		$account_email = $account_logged->getEMail();
		$email_new_time = $account_logged->getCustomField("email_new_time");
		if($email_new_time > 1)
			$email_new = $account_logged->getCustomField("email_new");
		$account_rlname = $account_logged->getRLName();
		$account_location = $account_logged->getLocation();
		if($account_logged->isBanned())
			if($account_logged->getBanTime() > 0)
				$welcome_message = '<font color="red">Your account is banished until '.date("j F Y, G:i:s", $account_logged->getBanTime()).'!</font>';
			else
				$welcome_message = '<font color="red">Your account is banished FOREVER!</font>';
		else
			$welcome_message = 'Welcome to your account!';
		
		$email_change = '';
		$email_request = false;
		if($email_new_time > 1)
		{
			if($email_new_time < time())
				$email_change = '<br>(You can accept <b>'.$email_new.'</b> as a new email.)';
			else
			{
				$email_change = ' <br>You can accept <b>new e-mail after '.date("j F Y", $email_new_time).".</b>";
				$email_request = true;
			}
		}
		
		$actions = array();
		foreach($account_logged->getActionsLog(0, 1000) as $action) {
			$actions[] = array('action' => $action['action'], 'date' => $action['date'], 'ip' => $action['ip'] != 0 ? long2ip($action['ip']) : inet_ntop($action['ipv6']));
		}
		
		$players = array();
		$account_players = $account_logged->getPlayersList();
		$account_players->orderBy('id');
		//show list of players on account
		foreach($account_players as $player)
		{
			$players[] = array(
				'name' => $player->getName(),
				'name_encoded' => urlencode($player->getName()),
				'deleted' => $player->isDeleted(),
				'level' => $player->getLevel(),
				'vocation' => $config['vocations'][$player->getVocation()],
				'online' => $player->isOnline()
			);
		}
		
		echo $twig->render('account.management.html', array(
			'welcome_message' => $welcome_message,
			'recovery_key' => $recovery_key,
			'email_change' => $email_change,
			'email_request' => $email_request,
			'email_new_time' => $email_new_time,
			'email_new' => isset($email_new) ? $email_new : '',
			'account' => USE_ACCOUNT_NAME ? $account_logged->getName() : $account_logged->getId(),
			'account_email' => $account_email,
			'account_created' => $account_created,
			'account_status' => $account_status,
			'account_registered' => $account_registered,
			'account_rlname' => $account_rlname,
			'account_location' => $account_location,
			'config' => $config,
			'actions' => $actions,
			'players' => $players
		));
	}
//########### CHANGE PASSWORD ##########
	if($action == "changepassword") {
		$new_password = isset($_POST['newpassword']) ? $_POST['newpassword'] : NULL;
		$new_password2 = isset($_POST['newpassword2']) ? $_POST['newpassword2'] : NULL;
		$old_password = isset($_POST['oldpassword']) ? $_POST['oldpassword'] : NULL;
		if(empty($new_password) && empty($new_password2) && empty($old_password)) {
			echo $twig->render('account.change_password.html');
		}
		else
		{
			if(empty($new_password) || empty($new_password2) || empty($old_password)){
				$show_msgs[] = "Please fill in form.";
			}
			$password_strlen = strlen($new_password);
			if($new_password != $new_password2) {
				$show_msgs[] = "The new passwords do not match!";
			}
			else if($password_strlen < 8) {
				$show_msgs[] = "New password minimal length is 8 characters.";
			}
			else if($password_strlen > 32) {
				$show_msgs[] = "New password maximal length is 32 characters.";
			}
			
			if(empty($show_msgs)) {
				if(!check_password($new_password)) {
					$show_msgs[] = "New password contains illegal chars (a-z, A-Z and 0-9 only!). Minimum password length is 7 characters and maximum 32.";
				}
				$old_password = encrypt(($config_salt_enabled ? $account_logged->getCustomField('salt') : '') . $old_password);
				if($old_password != $account_logged->getPassword()) {
					$show_msgs[] = "Current password is incorrect!";
				}
			}
			if(!empty($show_msgs)){
				//show errors
				echo $twig->render('error_box.html', array('errors' => $show_msg));
				
				//show form
				echo $twig->render('account.change_password.html');
			}
			else
			{
				$org_pass = $new_password;
				
				if($config_salt_enabled)
				{
					$salt = generateRandomString(10, false, true, true);
					$new_password = $salt . $new_password;
					$account_logged->setCustomField('salt', $salt);
				}
		
				$new_password = encrypt($new_password);
				$account_logged->setPassword($new_password);
				$account_logged->save();
				$account_logged->logAction('Account password changed.');
				
				$message = '';
				if($config['mail_enabled'] && $config['send_mail_when_change_password'])
				{
					$mailBody = $twig->render('mail.password_changed.html', array(
						'config' => $config,
						'new_password' => $org_pass
					));
					
					if(_mail($account_logged->getEMail(), $config['lua']['serverName']." - Changed password", $mailBody))
						$message = '<br/><small>Your new password were send on email address <b>'.$account_logged->getEMail().'</b>.</small>';
					else
						$message = '<br/><p class="error">An error occorred while sending email with password:<br/>' . $mailer->ErrorInfo . '</p>';
				}
				
				echo $twig->render('success.html', array(
					'title' => 'Password Changed',
					'description' => 'Your password has been changed.' . $message
				));
				$_SESSION['password'] = $new_password;
			}
		}
	}

//############# CHANGE E-MAIL ###################
	if($action == "changeemail") {
		$email_new_time = $account_logged->getCustomField("email_new_time");
	if($email_new_time > 10) {$email_new = $account_logged->getCustomField("email_new"); }
	if($email_new_time < 10){
	if(isset($_POST['changeemailsave']) && $_POST['changeemailsave'] == 1) {
		$email_new = $_POST['new_email'];
		$post_password = $_POST['password'];
		if(empty($email_new)) {
			$errors[] = "Please enter your new email address.";
		}
		else
		{
			if(!check_mail($email_new)) {
				$errors[] = "E-mail address is not correct.";
			}
		}
		if(empty($post_password)) {
			$errors[] = "Please enter password to your account.";
		}
		else
		{
			$post_password = encrypt(($config_salt_enabled ? $account_logged->getCustomField('salt') : '') . $post_password);
			if($post_password != $account_logged->getPassword()) {
				$errors[] = "Wrong password to account.";
			}
		}
		if(empty($errors)) {
			$email_new_time = time() + $config['account_mail_change'] * 24 * 3600;
			$account_logged->setCustomField("email_new", $email_new);
			$account_logged->setCustomField("email_new_time", $email_new_time);
			echo '<div class="TableContainer" >  <table class="Table1" cellpadding="0" cellspacing="0" >    <div class="CaptionContainer" >      <div class="CaptionInnerContainer" >        <span class="CaptionEdgeLeftTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionEdgeRightTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionBorderTop" style="background-image:url('.$template_path.'/images/content/table-headline-border.gif);" ></span>        <span class="CaptionVerticalLeft" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></span>        <div class="Text" >New Email Address Requested</div>        <span class="CaptionVerticalRight" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></span>        <span class="CaptionBorderBottom" style="background-image:url('.$template_path.'/images/content/table-headline-border.gif);" ></span>        <span class="CaptionEdgeLeftBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionEdgeRightBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>      </div>    </div>    <tr>      <td>        <div class="InnerTableContainer" >          <table style="width:100%;" ><tr><td>You have requested to change your email address to <b>'.$email_new.'</b>. The actual change will take place after <b>'.date("j F Y, G:i:s", $email_new_time).'</b>, during which you can cancel the request at any time.</td></tr>          </table>        </div>  </table></div></td></tr><br/><center><table border="0" cellspacing="0" cellpadding="0" ><form action="?subtopic=accountmanagement" method="post" ><tr><td style="border:0px;" ><div class="BigButton" style="background-image:url('.$template_path.'/images/buttons/sbutton.gif)" ><div onMouseOver="MouseOverBigButton(this);" onMouseOut="MouseOutBigButton(this);" ><div class="BigButtonOver" style="background-image:url('.$template_path.'/images/buttons/sbutton_over.gif);" ></div><input class="ButtonText" type="image" name="Back" alt="Back" src="'.$template_path.'/images/buttons/_sbutton_back.gif" ></div></div></td></tr></form></table></center>';
		}
		else
		{
			//show errors
			echo $twig->render('error_box.html', array('errors' => $errors));
			
			//show form
			echo $twig->render('account.change_mail.html', array(
				'new_email' => isset($_POST['new_email']) ? $_POST['new_email'] : null
			));
		}
	}
	else
	{
		echo $twig->render('account.change_mail.html', array(
			'new_email' => isset($_POST['new_email']) ? $_POST['new_email'] : null
		));
	}
	}
	else
	{
		if($email_new_time < time()) {
			if($_POST['changeemailsave'] == 1) {
				$account_logged->setCustomField("email_new", "");
				$account_logged->setCustomField("email_new_time", 0);
				$account_logged->setEmail($email_new);
				$account_logged->save();
				$account_logged->logAction('Account email changed to <b>' . $email_new . '</b>');
				
				echo $twig->render('success.html', array(
					'title' => 'Email Address Change Accepted',
					'description' => 'You have accepted <b>' . $account_logged->getEmail() . '</b> as your new email adress.'
				));
			}
			else
			{
				echo '<div class="TableContainer" >  <table class="Table1" cellpadding="0" cellspacing="0" >    <div class="CaptionContainer" >      <div class="CaptionInnerContainer" >        <span class="CaptionEdgeLeftTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionEdgeRightTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionBorderTop" style="background-image:url('.$template_path.'/images/content/table-headline-border.gif);" ></span>        <span class="CaptionVerticalLeft" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></span>        <div class="Text" >Email Address Change Accepted</div>        <span class="CaptionVerticalRight" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></span>        <span class="CaptionBorderBottom" style="background-image:url('.$template_path.'/images/content/table-headline-border.gif);" ></span>        <span class="CaptionEdgeLeftBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionEdgeRightBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>      </div>    </div>    <tr>      <td>        <div class="InnerTableContainer" >          <table style="width:100%;" ><tr><td>Do you accept <b>'.$email_new.'</b> as your new email adress?</td></tr>          </table>        </div>  </table></div></td></tr><br /><table width="100%"><tr><td width="30">&nbsp;</td><td align=left><form action="?subtopic=accountmanagement&action=changeemail" method="post"><input type="hidden" name="changeemailsave" value=1 ><INPUT TYPE=image NAME="I Agree" SRC="'.$template_path.'/images/buttons/sbutton_iagree.gif" BORDER=0 WIDTH=120 HEIGHT=17></FORM></td><td align=left><form action="?subtopic=accountmanagement&action=changeemail" method="post"><input type="hidden" name="emailchangecancel" value=1 ><input type=image name="Cancel" src="'.$template_path.'/images/buttons/sbutton_cancel.gif" BORDER=0 WIDTH=120 HEIGHT=17></form></td><td align=right><form action="?subtopic=accountmanagement" method="post" ><div class="BigButton" style="background-image:url('.$template_path.'/images/buttons/sbutton.gif)" ><div onMouseOver="MouseOverBigButton(this);" onMouseOut="MouseOutBigButton(this);" ><div class="BigButtonOver" style="background-image:url('.$template_path.'/images/buttons/sbutton_over.gif);" ></div><input class="ButtonText" type="image" name="Back" alt="Back" src="'.$template_path.'/images/buttons/_sbutton_back.gif" ></div></div></form></td><td width="30">&nbsp;</td></tr></table>';
			}
		}
		else
		{
			echo '<div class="TableContainer" >  <table class="Table1" cellpadding="0" cellspacing="0" >    <div class="CaptionContainer" >      <div class="CaptionInnerContainer" >        <span class="CaptionEdgeLeftTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionEdgeRightTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionBorderTop" style="background-image:url('.$template_path.'/images/content/table-headline-border.gif);" ></span>        <span class="CaptionVerticalLeft" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></span>        <div class="Text" >Change of Email Address</div>        <span class="CaptionVerticalRight" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></span>        <span class="CaptionBorderBottom" style="background-image:url('.$template_path.'/images/content/table-headline-border.gif);" ></span>        <span class="CaptionEdgeLeftBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionEdgeRightBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>      </div>    </div>    <tr>      <td>        <div class="InnerTableContainer" >          <table style="width:100%;" ><tr><td>A request has been submitted to change the email address of this account to <b>'.$email_new.'</b>.<br/>The actual change will take place on <b>'.date("j F Y, G:i:s", $email_new_time).'</b>.<br>If you do not want to change your email address, please click on "Cancel".</td></tr>          </table>        </div>  </table></div></td></tr><br/><table style="width:100%;" ><tr align="center"><td><table border="0" cellspacing="0" cellpadding="0" ><form action="?subtopic=accountmanagement&action=changeemail" method="post" ><tr><td style="border:0px;" ><input type="hidden" name="emailchangecancel" value=1 ><div class="BigButton" style="background-image:url('.$template_path.'/images/buttons/sbutton.gif)" ><div onMouseOver="MouseOverBigButton(this);" onMouseOut="MouseOutBigButton(this);" ><div class="BigButtonOver" style="background-image:url('.$template_path.'/images/buttons/sbutton_over.gif);" ></div><input class="ButtonText" type="image" name="Cancel" alt="Cancel" src="'.$template_path.'/images/buttons/_sbutton_cancel.gif" ></div></div></td></tr></form></table></td><td><table border="0" cellspacing="0" cellpadding="0" ><form action="?subtopic=accountmanagement" method="post" ><tr><td style="border:0px;" ><div class="BigButton" style="background-image:url('.$template_path.'/images/buttons/sbutton.gif)" ><div onMouseOver="MouseOverBigButton(this);" onMouseOut="MouseOutBigButton(this);" ><div class="BigButtonOver" style="background-image:url('.$template_path.'/images/buttons/sbutton_over.gif);" ></div><input class="ButtonText" type="image" name="Back" alt="Back" src="'.$template_path.'/images/buttons/_sbutton_back.gif" ></div></div></td></tr></form></table></td></tr></table>';
		}
	}
	if(isset($_POST['emailchangecancel']) && $_POST['emailchangecancel'] == 1) {
		$account_logged->setCustomField("email_new", "");
		$account_logged->setCustomField("email_new_time", 0);
		echo '<div class="TableContainer" >  <table class="Table1" cellpadding="0" cellspacing="0" >    <div class="CaptionContainer" >      <div class="CaptionInnerContainer" >        <span class="CaptionEdgeLeftTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionEdgeRightTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionBorderTop" style="background-image:url('.$template_path.'/images/content/table-headline-border.gif);" ></span>        <span class="CaptionVerticalLeft" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></span>        <div class="Text" >Email Address Change Cancelled</div>        <span class="CaptionVerticalRight" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></span>        <span class="CaptionBorderBottom" style="background-image:url('.$template_path.'/images/content/table-headline-border.gif);" ></span>        <span class="CaptionEdgeLeftBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionEdgeRightBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>      </div>    </div>    <tr>      <td>        <div class="InnerTableContainer" >          <table style="width:100%;" ><tr><td>Your request to change the email address of your account has been cancelled. The email address will not be changed.</td></tr>          </table>        </div>  </table></div></td></tr><br/><center><table border="0" cellspacing="0" cellpadding="0" ><form action="?subtopic=accountmanagement" method="post" ><tr><td style="border:0px;" ><div class="BigButton" style="background-image:url('.$template_path.'/images/buttons/sbutton.gif)" ><div onMouseOver="MouseOverBigButton(this);" onMouseOut="MouseOutBigButton(this);" ><div class="BigButtonOver" style="background-image:url('.$template_path.'/images/buttons/sbutton_over.gif);" ></div><input class="ButtonText" type="image" name="Back" alt="Back" src="'.$template_path.'/images/buttons/_sbutton_back.gif" ></div></div></td></tr></form></table></center>';
	}
	}

//########### CHANGE PUBLIC INFORMATION (about account owner) ######################
	if($action == "changeinfo") {
		$show_form = true;
		$new_rlname = isset($_POST['info_rlname']) ? htmlspecialchars(stripslashes($_POST['info_rlname'])) : NULL;
		$new_location = isset($_POST['info_location']) ? htmlspecialchars(stripslashes($_POST['info_location'])) : NULL;
		$new_country = isset($_POST['info_country']) ? htmlspecialchars(stripslashes($_POST['info_country'])) : NULL;
		if(isset($_POST['changeinfosave']) && $_POST['changeinfosave'] == 1) {
			$errors = array();
			
			if(!isset($config['countries'][$new_country]))
				$errors[] = 'Country is not correct.';
			
			if(empty($errors)) {
				//save data from form
				$account_logged->setCustomField("rlname", $new_rlname);
				$account_logged->setCustomField("location", $new_location);
				$account_logged->setCustomField("country", $new_country);
				$account_logged->logAction('Changed Real Name to ' . $new_rlname . ', Location to ' . $new_location . ' and Country to ' . $config['countries'][$new_country] . '.');
				echo $twig->render('success.html', array(
					'title' => 'Public Information Changed',
					'description' => 'Your public information has been changed.'
				));
				$show_form = false;
			}
			else {
				echo $twig->render('error_box.html', array('errors' => $errors));
			}
		}
		
		//show form
		if($show_form) {
			$account_rlname = $account_logged->getCustomField("rlname");
			$account_location = $account_logged->getCustomField("location");
			if ($config['account_country'])
				$account_country = $account_logged->getCustomField("country");
			
			$countries = array();
			foreach (array('pl', 'se', 'br', 'us', 'gb',) as $country)
				$countries[$country] = $config['countries'][$country];
			
			$countries['--'] = '----------';
			
			foreach ($config['countries'] as $code => $country)
				$countries[$code] = $country;
			
			echo $twig->render('account.change_info.html', array(
				'countries' => $countries,
				'account_rlname' => $account_rlname,
				'account_location' => $account_location,
				'account_country' => $account_country
			));
		}
	}

//############## GENERATE RECOVERY KEY ###########
	if($action == "registeraccount")
	{
		$_POST['reg_password'] = isset($_POST['reg_password']) ? $_POST['reg_password'] : '';
		$reg_password = encrypt(($config_salt_enabled ? $account_logged->getCustomField('salt') : '') . $_POST['reg_password']);
		$old_key = $account_logged->getCustomField("key");
		if(isset($_POST['registeraccountsave']) && $_POST['registeraccountsave'] == "1") {
			if($reg_password == $account_logged->getPassword()) {
				if(empty($old_key)) {
					$dontshowtableagain = true;
					$new_rec_key = generateRandomString(10, false, true, true);

					$account_logged->setCustomField("key", $new_rec_key);
					$account_logged->logAction('Generated recovery key.');
					echo '<div class="TableContainer" >  <table class="Table1" cellpadding="0" cellspacing="0" >    <div class="CaptionContainer" >      <div class="CaptionInnerContainer" >        <span class="CaptionEdgeLeftTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionEdgeRightTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionBorderTop" style="background-image:url('.$template_path.'/images/content/table-headline-border.gif);" ></span>        <span class="CaptionVerticalLeft" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></span>        <div class="Text" >Account Registered</div>        <span class="CaptionVerticalRight" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></span>        <span class="CaptionBorderBottom" style="background-image:url('.$template_path.'/images/content/table-headline-border.gif);" ></span>        <span class="CaptionEdgeLeftBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionEdgeRightBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>      </div>    </div>    <tr>      <td>        <div class="InnerTableContainer" >          <table style="width:100%;" >Thank you for registering your account! You can now recover your account if you have lost access to the assigned email address by using the following<br/><br/><font size="5">&nbsp;&nbsp;&nbsp;<b>Recovery Key: '.$new_rec_key.'</b></font><br/><br/><br/><b>Important:</b><ul><li>Write down this recovery key carefully.</li><li>Store it at a safe place!</li>';
					if($config['mail_enabled'] && $config['send_mail_when_generate_reckey'])
					{
						$mailBody = '
						<h3>New recovery key!</h3>
						<p>You or someone else generated recovery key to your account on server <a href="'.BASE_URL.'"><b>'.$config['lua']['serverName'].'</b></a>.</p>
						<p>Recovery key: <b>'.$new_rec_key.'</b></p>';
						if(_mail($account_logged->getEMail(), $config['lua']['serverName']." - recovery key", $mailBody))
							echo '<br /><small>Your recovery key were send on email address <b>'.$account_logged->getEMail().'</b>.</small>';
						else
							echo '<br /><p class="error">An error occorred while sending email with recovery key! You will not receive e-mail with this key. Error:<br/>' . $mailer->ErrorInfo . '</p>';
					}
					echo '</ul>          </table>        </div>  </table></div></td></tr><br/><center><table border="0" cellspacing="0" cellpadding="0" ><form action="?subtopic=accountmanagement" method="post" ><tr><td style="border:0px;" ><div class="BigButton" style="background-image:url('.$template_path.'/images/buttons/sbutton.gif)" ><div onMouseOver="MouseOverBigButton(this);" onMouseOut="MouseOutBigButton(this);" ><div class="BigButtonOver" style="background-image:url('.$template_path.'/images/buttons/sbutton_over.gif);" ></div><input class="ButtonText" type="image" name="Back" alt="Back" src="'.$template_path.'/images/buttons/_sbutton_back.gif" ></div></div></td></tr></form></table></center>';
				}
				else
					$reg_errors[] = 'Your account is already registred.';
			}
			else
				$reg_errors[] = 'Wrong password to account.';
		}
		if(!$dontshowtableagain)
		{
			if(!empty($reg_errors))
			{
				//show errors
				echo $twig->render('error_box.html', array('errors' => $reg_errors));
			}
			
			//show form
			echo $twig->render('account.register.html');
		}
	}

//############## GENERATE NEW RECOVERY KEY ###########
	if($action == "newreckey")
	{
		if(isset($_POST['reg_password']))
			$reg_password = encrypt(($config_salt_enabled ? $account_logged->getCustomField('salt') : '') . $_POST['reg_password']);
		
		$reckey = $account_logged->getCustomField('key');
		if((!$config['generate_new_reckey'] || !$config['mail_enabled']) || empty($reckey))
			echo 'You cant get new rec key';
		else
		{
			$points = $account_logged->getCustomField('premium_points');
			if(isset($_POST['registeraccountsave']) && $_POST['registeraccountsave'] == '1')
			{
				if($reg_password == $account_logged->getPassword())
				{
					if($points >= $config['generate_new_reckey_price'])
					{
							$dontshowtableagain = true;
							$new_rec_key = generateRandomString(10, false, true, true);

							echo '<div class="TableContainer" >  <table class="Table1" cellpadding="0" cellspacing="0" >    <div class="CaptionContainer" >      <div class="CaptionInnerContainer" >        <span class="CaptionEdgeLeftTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionEdgeRightTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionBorderTop" style="background-image:url('.$template_path.'/images/content/table-headline-border.gif);" ></span>        <span class="CaptionVerticalLeft" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></span>        <div class="Text" >Account Registered</div>        <span class="CaptionVerticalRight" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></span>        <span class="CaptionBorderBottom" style="background-image:url('.$template_path.'/images/content/table-headline-border.gif);" ></span>        <span class="CaptionEdgeLeftBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionEdgeRightBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>      </div>    </div>    <tr>      <td>        <div class="InnerTableContainer" >          <table style="width:100%;" ><ul>';

								$mailBody = '
								<h3>New recovery key!</h3>
								<p>You or someone else generated recovery key to your account on server <a href="'.BASE_URL.'"><b>'.$config['lua']['serverName'].'</b></a>.</p>
								<p>Recovery key: <b>'.$new_rec_key.'</b></p>';
								if(_mail($account_logged->getEMail(), $config['lua']['serverName']." - new recovery key", $mailBody))
								{
									$account_logged->setCustomField("key", $new_rec_key);
									$account_logged->setCustomField("premium_points", $account_logged->getCustomField("premium_points") - $config['generate_new_reckey_price']);
									$account_logged->logAction('Generated new recovery key for ' . $config['generate_new_reckey_price'] . ' premium points.');
									echo '<br />Your recovery key were send on email address <b>'.$account_logged->getEMail().'</b> for '.$config['generate_new_reckey_price'].' premium points.';
								}
								else
									echo '<br /><p class="error">An error occorred while sending email ( <b>'.$account_logged->getEMail().'</b> ) with recovery key! Recovery key not changed. Try again. Error:<br/>' . $mailer->ErrorInfo . '</p>';
							echo '</ul>          </table>        </div>  </table></div></td></tr><br/><center><table border="0" cellspacing="0" cellpadding="0" ><form action="?subtopic=accountmanagement" method="post" ><tr><td style="border:0px;" ><div class="BigButton" style="background-image:url('.$template_path.'/images/buttons/sbutton.gif)" ><div onMouseOver="MouseOverBigButton(this);" onMouseOut="MouseOutBigButton(this);" ><div class="BigButtonOver" style="background-image:url('.$template_path.'/images/buttons/sbutton_over.gif);" ></div><input class="ButtonText" type="image" name="Back" alt="Back" src="'.$template_path.'/images/buttons/_sbutton_back.gif" ></div></div></td></tr></form></table></center>';
					}
					else
						$reg_errors[] = 'You need '.$config['generate_new_reckey_price'].' premium points to generate new recovery key. You have <b>'.$points.'<b> premium points.';
				}
				else
					$reg_errors[] = 'Wrong password to account.';
			}
			if(!$dontshowtableagain)
			{
				//show errors if not empty
				if(!empty($reg_errors)) {
					echo $twig->render('error_box.html', array('errors' => $reg_errors));
				}
				//show form
				echo 'To generate NEW recovery key for your account please enter your password.<br/><font color="red"><b>New recovery key cost '.$config['generate_new_reckey_price'].' Premium Points.</font> You have '.$points.' premium points. You will receive e-mail with this recovery key.</b><br/><form action="?subtopic=accountmanagement&action=newreckey" method="post" ><input type="hidden" name="registeraccountsave" value="1"><div class="TableContainer" >  <table class="Table1" cellpadding="0" cellspacing="0" >    <div class="CaptionContainer" >      <div class="CaptionInnerContainer" >        <span class="CaptionEdgeLeftTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionEdgeRightTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionBorderTop" style="background-image:url('.$template_path.'/images/content/table-headline-border.gif);" ></span>        <span class="CaptionVerticalLeft" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></span>        <div class="Text" >Generate recovery key</div>        <span class="CaptionVerticalRight" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></span>        <span class="CaptionBorderBottom" style="background-image:url('.$template_path.'/images/content/table-headline-border.gif);" ></span>        <span class="CaptionEdgeLeftBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionEdgeRightBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>      </div>    </div>    <tr>      <td>        <div class="InnerTableContainer" >          <table style="width:100%;" ><tr><td class="LabelV" ><span >Password:</td><td><input type="password" name="reg_password" size="30" maxlength="29" ></td></tr>          </table>        </div>  </table></div></td></tr><br/><table style="width:100%" ><tr align="center" ><td><table border="0" cellspacing="0" cellpadding="0" ><tr><td style="border:0px;" ><div class="BigButton" style="background-image:url('.$template_path.'/images/buttons/sbutton.gif)" ><div onMouseOver="MouseOverBigButton(this);" onMouseOut="MouseOutBigButton(this);" ><div class="BigButtonOver" style="background-image:url('.$template_path.'/images/buttons/sbutton_over.gif);" ></div><input class="ButtonText" type="image" name="Submit" alt="Submit" src="'.$template_path.'/images/buttons/_sbutton_submit.gif" ></div></div></td><tr></form></table></td><td><table border="0" cellspacing="0" cellpadding="0" ><form action="?subtopic=accountmanagement" method="post" ><tr><td style="border:0px;" ><div class="BigButton" style="background-image:url('.$template_path.'/images/buttons/sbutton.gif)" ><div onMouseOver="MouseOverBigButton(this);" onMouseOut="MouseOutBigButton(this);" ><div class="BigButtonOver" style="background-image:url('.$template_path.'/images/buttons/sbutton_over.gif);" ></div><input class="ButtonText" type="image" name="Back" alt="Back" src="'.$template_path.'/images/buttons/_sbutton_back.gif" ></div></div></td></tr></form></table></td></tr></table>';
			}
		}
	}



//###### CHANGE CHARACTER COMMENT ######
	if($action == "changecomment") {
		$player_name = stripslashes($_REQUEST['name']);
		$new_comment = isset($_POST['comment']) ? htmlspecialchars(stripslashes(substr($_POST['comment'],0,2000))) : NULL;
		$new_hideacc = isset($_POST['accountvisible']) ? (int)$_POST['accountvisible'] : NULL;
		if(check_name($player_name)) {
			$player = $ots->createObject('Player');
			$player->find($player_name);
			if($player->isLoaded()) {
				$player_account = $player->getAccount();
				if($account_logged->getId() == $player_account->getId()) {
					if(isset($_POST['changecommentsave']) && $_POST['changecommentsave'] == 1) {
						$player->setCustomField("hidden", $new_hideacc);
						$player->setCustomField("comment", $new_comment);
						$account_logged->logAction('Changed comment for character <b>' . $player->getName() . '</b>.');
						echo '<div class="TableContainer" >  <table class="Table1" cellpadding="0" cellspacing="0" >    <div class="CaptionContainer" >      <div class="CaptionInnerContainer" >        <span class="CaptionEdgeLeftTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionEdgeRightTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionBorderTop" style="background-image:url('.$template_path.'/images/content/table-headline-border.gif);" ></span>        <span class="CaptionVerticalLeft" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></span>        <div class="Text" >Character Information Changed</div>        <span class="CaptionVerticalRight" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></span>        <span class="CaptionBorderBottom" style="background-image:url('.$template_path.'/images/content/table-headline-border.gif);" ></span>        <span class="CaptionEdgeLeftBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionEdgeRightBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>      </div>    </div>    <tr>      <td>        <div class="InnerTableContainer" >          <table style="width:100%;" ><tr><td>The character information has been changed.</td></tr>          </table>        </div>  </table></div></td></tr><br><center><table border="0" cellspacing="0" cellpadding="0" ><form action="?subtopic=accountmanagement" method="post" ><tr><td style="border:0px;" ><div class="BigButton" style="background-image:url('.$template_path.'/images/buttons/sbutton.gif)" ><div onMouseOver="MouseOverBigButton(this);" onMouseOut="MouseOutBigButton(this);" ><div class="BigButtonOver" style="background-image:url('.$template_path.'/images/buttons/sbutton_over.gif);" ></div><input class="ButtonText" type="image" name="Back" alt="Back" src="'.$template_path.'/images/buttons/_sbutton_back.gif" ></div></div></td></tr></form></table></center>';
					}
					else
					{
						echo 'Here you can see and edit the information about your character.<br/>If you do not want to specify a certain field, just leave it blank.<br/><br/><form action="?subtopic=accountmanagement&action=changecomment" method="post" ><div class="TableContainer" >  <table class="Table5" cellpadding="0" cellspacing="0" >    <div class="CaptionContainer" >      <div class="CaptionInnerContainer" >        <span class="CaptionEdgeLeftTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionEdgeRightTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionBorderTop" style="background-image:url('.$template_path.'/images/content/table-headline-border.gif);" ></span>        <span class="CaptionVerticalLeft" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></span>        <div class="Text" >Edit Character Information</div>        <span class="CaptionVerticalRight" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></span>        <span class="CaptionBorderBottom" style="background-image:url('.$template_path.'/images/content/table-headline-border.gif);" ></span>        <span class="CaptionEdgeLeftBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionEdgeRightBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>      </div>    </div>    <tr>      <td>        <div class="InnerTableContainer" >          <table style="width:100%;" ><tr><td><div class="TableShadowContainerRightTop" >  <div class="TableShadowRightTop" style="background-image:url('.$template_path.'/images/content/table-shadow-rt.gif);" ></div></div><div class="TableContentAndRightShadow" style="background-image:url('.$template_path.'/images/content/table-shadow-rm.gif);" >  <div class="TableContentContainer" >    <table class="TableContent" width="100%" ><tr><td class="LabelV" >Name:</td><td style="width:80%;" >'.$player_name.'</td></tr><tr><td class="LabelV" >Hide Account:</td><td>
						<input type="hidden" value="0" name="accountvisible">
					
						<input type="checkbox" name="accountvisible" id="accountvisible" value="1" ' . ($player->getCustomField("hidden") == 1 ? ' checked="checked"' : '') . '>
						<label for="accountvisible"> check to hide your account information</label>';
							if((int)$player->getCustomField('group_id') > 1)
								echo ' (you will be also hidden on the Team page!)';

						echo '</td></tr>    </table>  </div></div><div class="TableShadowContainer" >  <div class="TableBottomShadow" style="background-image:url('.$template_path.'/images/content/table-shadow-bm.gif);" >    <div class="TableBottomLeftShadow" style="background-image:url('.$template_path.'/images/content/table-shadow-bl.gif);" ></div>    <div class="TableBottomRightShadow" style="background-image:url('.$template_path.'/images/content/table-shadow-br.gif);" ></div>  </div></div></td></tr><tr><td><div class="TableShadowContainerRightTop" >  <div class="TableShadowRightTop" style="background-image:url('.$template_path.'/images/content/table-shadow-rt.gif);" ></div></div><div class="TableContentAndRightShadow" style="background-image:url('.$template_path.'/images/content/table-shadow-rm.gif);" >  <div class="TableContentContainer" >    <table class="TableContent" width="100%" ><tr><td class="LabelV" ><span >Comment:</span></td><td style="width:80%;" ><textarea name="comment" rows="10" cols="50" wrap="virtual" >'.$player->getCustomField("comment").'</textarea><br>[max. length: 2000 chars, 50 lines (ENTERs)]</td></tr>    </table>  </div></div><div class="TableShadowContainer" >  <div class="TableBottomShadow" style="background-image:url('.$template_path.'/images/content/table-shadow-bm.gif);" ><div class="TableBottomLeftShadow" style="background-image:url('.$template_path.'/images/content/table-shadow-bl.gif);" ></div><div class="TableBottomRightShadow" style="background-image:url('.$template_path.'/images/content/table-shadow-br.gif);" ></div></div></div></td></tr></td></tr>          </table>        </div>  </table></div></td></tr><br/><table style="width:100%" ><tr align="center" ><td><table border="0" cellspacing="0" cellpadding="0" ><tr><td style="border:0px;" ><input type="hidden" name="name" value="'.$player->getName().'"><input type="hidden" name="changecommentsave" value="1"><div class="BigButton" style="background-image:url('.$template_path.'/images/buttons/sbutton.gif)" ><div onMouseOver="MouseOverBigButton(this);" onMouseOut="MouseOutBigButton(this);" ><div class="BigButtonOver" style="background-image:url('.$template_path.'/images/buttons/sbutton_over.gif);" ></div><input class="ButtonText" type="image" name="Submit" alt="Submit" src="'.$template_path.'/images/buttons/_sbutton_submit.gif" ></div></div></td><tr></form></table></td><td><table border="0" cellspacing="0" cellpadding="0" ><form action="?subtopic=accountmanagement" method="post" ><tr><td style="border:0px;" ><div class="BigButton" style="background-image:url('.$template_path.'/images/buttons/sbutton.gif)" ><div onMouseOver="MouseOverBigButton(this);" onMouseOut="MouseOutBigButton(this);" ><div class="BigButtonOver" style="background-image:url('.$template_path.'/images/buttons/sbutton_over.gif);" ></div><input class="ButtonText" type="image" name="Back" alt="Back" src="'.$template_path.'/images/buttons/_sbutton_back.gif" ></div></div></td></tr></form></table></td></tr></table>';
					}
				}
				else
				{
					echo "Error. Character <b>".$player_name."</b> is not on your account.";
				}
			}
			else
			{
				echo "Error. Character with this name doesn't exist.";
			}
		}
		else
		{
			echo "Error. Name contain illegal characters.";
		}
	}

	if($action == "changename") {
		echo '<script type="text/javascript" src="tools/check_name.js"></script>';
		
		$name_changed = false;
		$player_id = isset($_POST['player_id']) ? (int)$_POST['player_id'] : NULL;
		$name = isset($_POST['name']) ? stripslashes(ucwords(strtolower($_POST['name']))) : NULL;
		if((!$config['account_change_character_name']))
			echo 'You cant change your character name';
		else
		{
			$points = $account_logged->getCustomField('premium_points');
			if(isset($_POST['changenamesave']) && $_POST['changenamesave'] == 1) {
				if($points < $config['account_change_character_name_points'])
					$errors[] = 'You need ' . $config['account_change_character_name_points'] . ' premium points to change name. You have <b>'.$points.'<b> premium points.';
	
				if(empty($errors) && empty($name))
					$errors[] = 'Please enter a name for your character!';

				if(empty($errors) && strlen($name) > 25)
					$errors[] = 'Name is too long. Max. lenght <b>25</b> letters.';
				else if(empty($errors) && strlen($name) < 3)
					$errors[] = 'Name is too short. Min. lenght <b>25</b> letters.';
				
				if(empty($errors))
				{
					$error = '';
					if(!admin() && !check_name_new_char($name, $error))
						$errors[] = $error;
				}
				
				if(empty($errors)) {
					$player = $ots->createObject('Player');
					$player->load($player_id);
					if($player->isLoaded()) {
						$player_account = $player->getAccount();
						if($account_logged->getId() == $player_account->getId()) {
							if($player->isOnline()) {
								$errors[] = 'This character is online.';
							}
							
							if(empty($errors)) {
								$name_changed = true;
								$old_name = $player->getName();
								$player->setName($name);
								$player->save();
								$account_logged->setCustomField("premium_points", $points - $config['account_change_character_name_points']);
								$account_logged->logAction('Changed name from <b>' . $old_name . '</b> to <b>' . $player->getName() . '</b>.');
								echo $twig->render('success.html', array(
									'title' => 'Character Name Changed',
									'description' => 'The character <b>'.$old_name.'</b> name has been changed to <b>' . $player->getName() . '</b>.'
								));
							}
						}
						else {
							$errors[] = 'Character <b>' . $player_name . '</b> is not on your account.';
						}
					}
					else {
						$errors[] = "Character with this name doesn't exist.";
					}
				}
			}

			if(!$name_changed) {
				if(!empty($errors)) {
					echo $twig->render('error_box.html', array('errors' => $errors));
				}
				echo 'To change a name of character select player and choose a new name.<br/>
				<font color="red">Change name cost ' . $config['account_change_character_name_points'] . ' premium points. You have ' . $points . ' premium points.</font><br/><br/><form action="?subtopic=accountmanagement&action=changename" method="post" ><input type="hidden" name="changenamesave" value="1"><div class="TableContainer" >  <table class="Table1" cellpadding="0" cellspacing="0" >    <div class="CaptionContainer" >      <div class="CaptionInnerContainer" >        <span class="CaptionEdgeLeftTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionEdgeRightTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionBorderTop" style="background-image:url('.$template_path.'/images/content/table-headline-border.gif);" ></span>        <span class="CaptionVerticalLeft" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></span>        <div class="Text" >Change Name</div>        <span class="CaptionVerticalRight" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></span>        <span class="CaptionBorderBottom" style="background-image:url('.$template_path.'/images/content/table-headline-border.gif);" ></span>        <span class="CaptionEdgeLeftBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionEdgeRightBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>      </div>    </div>    <tr>      <td>        <div class="InnerTableContainer" >
				<table style="width:100%;" >
					<tr>
						<td class="LabelV" ><span >Character:</td>
						<td style="width:90%;" >
							<select name="player_id">';
								$players = $account_logged->getPlayersList();
								foreach($players as $player)
									echo '<option value="' . $player->getId() . '">' . $player->getName() . '</option>';
							echo '
							</select>
						</td>
					</tr>
					<tr>
						<td class="LabelV" ><span >New Name:</td>
						<td>
							<input type="text" name="name" id="name" onkeyup="checkName();" size="25" maxlength="25" >
							<font size="1" face="verdana,arial,helvetica">
								<div id="name_check">Please enter your character name.</div>
							</font>
						</td>
					</tr>
				</table>        </div>  </table></div></td></tr><br/><table style="width:100%" ><tr align="center" ><td><table border="0" cellspacing="0" cellpadding="0" ><tr><td style="border:0px;" ><div class="BigButton" style="background-image:url('.$template_path.'/images/buttons/sbutton.gif)" ><div onMouseOver="MouseOverBigButton(this);" onMouseOut="MouseOutBigButton(this);" ><div class="BigButtonOver" style="background-image:url('.$template_path.'/images/buttons/sbutton_over.gif);" ></div><input class="ButtonText" type="image" name="Submit" alt="Submit" src="'.$template_path.'/images/buttons/_sbutton_submit.gif" ></div></div></td><tr></form></table></td><td><table border="0" cellspacing="0" cellpadding="0" ><form action="?subtopic=accountmanagement" method="post" ><tr><td style="border:0px;" ><div class="BigButton" style="background-image:url('.$template_path.'/images/buttons/sbutton.gif)" ><div onMouseOver="MouseOverBigButton(this);" onMouseOut="MouseOutBigButton(this);" ><div class="BigButtonOver" style="background-image:url('.$template_path.'/images/buttons/sbutton_over.gif);" ></div><input class="ButtonText" type="image" name="Back" alt="Back" src="'.$template_path.'/images/buttons/_sbutton_back.gif" ></div></div></td></tr></form></table></td></tr></table>';
			}
		}
	}

	if($action == "changesex") {
		$sex_changed = false;
		$player_id = isset($_POST['player_id']) ? (int)$_POST['player_id'] : NULL;
		$new_sex = isset($_POST['new_sex']) ? (int)$_POST['new_sex'] : NULL;
		if((!$config['account_change_character_sex']))
			echo 'You cant change your character sex';
		else
		{
			$points = $account_logged->getCustomField('premium_points');
			if(isset($_POST['changesexsave']) && $_POST['changesexsave'] == 1) {
				if($points < $config['account_change_character_sex_points'])
					$errors[] = 'You need ' . $config['account_change_character_sex_points'] . ' premium points to change sex. You have <b>'.$points.'</b> premium points.';
	
				if(empty($errors) && !isset($config['genders'][$new_sex])) {
					$errors[] = 'This sex is invalid.';
				}
				
				if(empty($errors)) {
					$player = new OTS_Player();
					$player->load($player_id);
					
					if($player->isLoaded()) {
						$player_account = $player->getAccount();
						
						if($account_logged->getId() == $player_account->getId()) {
							if($player->isOnline()) {
								$errors[] = 'This character is online.';
							}
							
							if(empty($errors) && $player->getSex() == $new_sex)
								$errors[] = 'Sex cannot be same';
							
							if(empty($errors)) {
								$sex_changed = true;
								$old_sex = $player->getSex();
								$player->setSex($new_sex);
								
								$old_sex_str = 'Unknown';
								if(isset($config['genders'][$old_sex]))
									$old_sex_str = $config['genders'][$old_sex];
								
								$new_sex_str = 'Unknown';
								if(isset($config['genders'][$new_sex]))
									$new_sex_str = $config['genders'][$new_sex];
								
								$player->save();
								$account_logged->setCustomField("premium_points", $points - $config['account_change_character_name_points']);
								$account_logged->logAction('Changed sex on character <b>' . $player->getName() . '</b> from <b>' . $old_sex_str . '</b> to <b>' . $new_sex_str . '</b>.');
								echo $twig->render('success.html', array(
									'title' => 'Character Sex Changed',
									'description' => 'The character <b>' . $player->getName() . '</b> sex has been changed to <b>' . $new_sex_str . '</b>.'
								));
							}
						}
						else {
							$errors[] = 'Character <b>'.$player_name.'</b> is not on your account.';
						}
					}
					else {
						$errors[] = "Character with this name doesn't exist.";
					}
				}
			}

			if(!$sex_changed) {
				if(!empty($errors)) {
					echo $twig->render('error_box.html', array('errors' => $errors));
				}
				echo $twig->render('account.change_sex.html', array(
					'config' => $config,
					'players' => $account_logged->getPlayersList(),
					'player_sex' => isset($player) ? $player->getSex() : -1,
					'points' => $points
				));
			}
		}
	}
//### DELETE character from account ###
	if($action == "deletecharacter") {
		$player_name = isset($_POST['delete_name']) ? stripslashes($_POST['delete_name']) : NULL;
		$password_verify = isset($_POST['delete_password']) ? $_POST['delete_password'] : NULL;
		$password_verify = encrypt(($config_salt_enabled ? $account_logged->getCustomField('salt') : '') . $password_verify);
		if(isset($_POST['deletecharactersave']) && $_POST['deletecharactersave'] == 1) {
			if(!empty($player_name) && !empty($password_verify)) {
				if(check_name($player_name)) {
					$player = new OTS_Player();
					$player->find($player_name);
					if($player->isLoaded()) {
						$player_account = $player->getAccount();
						if($account_logged->getId() == $player_account->getId()) {
							if($password_verify == $account_logged->getPassword()) {
								if(!$player->isOnline())
								{
								//dont show table "delete character" again
								$dontshowtableagain = true;
								//delete player
								if(fieldExist('deletion', 'players'))
									$player->setCustomField('deletion', 1);
								else
									$player->setCustomField('deleted', 1);
								$account_logged->logAction('Deleted character <b>' . $player->getName() . '</b>.');
								echo $twig->render('success.html', array(
									'title' => 'Character Deleted',
									'description' => 'The character <b>' . $player_name . '</b> has been deleted.'
								));
								}
								else
									$errors[] = 'This character is online.';
							}
							else {
								$errors[] = 'Wrong password to account.';
							}
						}
						else {
							$errors[] = 'Character <b>'.$player_name.'</b> is not on your account.';
						}
					}
					else {
						$errors[] = 'Character with this name doesn\'t exist.';
					}
				}
				else {
					$errors[] = 'Name contain illegal characters.';
				}
			}
			else {
				$errors[] = 'Character name or/and password is empty. Please fill in form.';
			}
		}
		if(!$dontshowtableagain) {
			if(!empty($errors)) {
				echo $twig->render('error_box.html', array('errors' => $errors));
			}
			echo $twig->render('account.delete_character.html');
		}
	}

//## CREATE CHARACTER on account ###
	if($action == "createcharacter") {
		echo '<script type="text/javascript" src="tools/check_name.js"></script>';
		$newchar_name = isset($_POST['name']) ? stripslashes(ucwords(strtolower($_POST['name']))) : NULL;
		$newchar_sex = isset($_POST['sex']) ? $_POST['sex'] : NULL;
		$newchar_vocation = isset($_POST['vocation']) ? $_POST['vocation'] : NULL;
		$newchar_town = isset($_POST['town']) ? $_POST['town'] : NULL;
		$errors = array();
		
		$newchar_created = false;
		if(isset($_POST['savecharacter']) && $_POST['savecharacter'] == 1) {
			if(empty($newchar_name))
				$errors[] = 'Please enter a name for your character!';

			if(strlen($newchar_name) > 25)
				$errors[] = 'Name is too long. Max. lenght <b>25</b> letters.';
			else if(strlen($newchar_name) < 3)
				$errors[] = 'Name is too short. Min. lenght <b>25</b> letters.';

			if(empty($newchar_sex) && $newchar_sex != "0")
				$errors[] = 'Please select the sex for your character!';

			if(count($config['character_samples']) > 1)
			{
				if(!isset($newchar_vocation))
					$errors[] = 'Please select a vocation for your character.';
			}
			else
				$newchar_vocation = $config['character_samples'][0];

			if(count($config['character_towns']) > 1) {
				if(!isset($newchar_town))
					$errors[] = 'Please select a town for your character.';
			}
			else {
				$newchar_town = $config['character_towns'][0];
			}

			$exist = new OTS_Player();
			$exist->find($newchar_name);
			if($exist->isLoaded()) {
				$errors[] .= 'Character with this name already exist.';
			}

			if(empty($errors)) {
				$error = '';
				if(!admin() && !check_name_new_char($newchar_name, $error)) {
					$errors[] = $error;
				}
				if(!isset($config['genders'][$newchar_sex]))
					$errors[] = 'Sex is invalid.';
				if(!in_array($newchar_town, $config['character_towns']))
					$errors[] = 'Please select valid town.';
				if(count($config['character_samples']) > 1)
				{
					$newchar_vocation_check = false;
					foreach($config['character_samples'] as $char_vocation_key => $sample_char)
						if($newchar_vocation == $char_vocation_key)
							$newchar_vocation_check = true;
					if(!$newchar_vocation_check)
						$errors[] = 'Unknown vocation. Please fill in form again.';
				}
				else
					$newchar_vocation = 0;
			}

			if(empty($errors))
			{
				$number_of_players_on_account = $account_logged->getPlayersList()->count();
				if($number_of_players_on_account >= $config['characters_per_account'])
					$errors[] .= 'You have too many characters on your account <b>('.$number_of_players_on_account.'/'.$config['characters_per_account'].')</b>!';
			}

			if(empty($errors))
			{
				$char_to_copy_name = $config['character_samples'][$newchar_vocation];
				$char_to_copy = new OTS_Player();
				$char_to_copy->find($char_to_copy_name);
				if(!$char_to_copy->isLoaded())
					$errors[] .= 'Wrong characters configuration. Try again or contact with admin. ADMIN: Edit file config/config.php and set valid characters to copy names. Character to copy: <b>'.$char_to_copy_name.'</b> doesn\'t exist.';
			}

			if(empty($errors))
			{
				if($newchar_sex == "0")
					$char_to_copy->setLookType(136);
				$player = $ots->createObject('Player');
				$player->setName($newchar_name);
				$player->setAccount($account_logged);
				//$player->setGroupId($char_to_copy->getGroup()->getId());
				$player->setGroupId(1);
				$player->setSex($newchar_sex);
				$player->setVocation($char_to_copy->getVocation());
				if(fieldExist('promotion', 'players'))
					$player->setPromotion($char_to_copy->getPromotion());
			
				if(fieldExist('direction', 'players'))
					$player->setDirection($char_to_copy->getDirection());
				
				$player->setConditions($char_to_copy->getConditions());
				$rank = $char_to_copy->getRank();
				if($rank->isLoaded()) {
					$player->setRank($char_to_copy->getRank());
				}
				
				if(fieldExist('lookaddons', 'players'))
					$player->setLookAddons($char_to_copy->getLookAddons());
	
				$player->setTownId($newchar_town);
				$player->setExperience($char_to_copy->getExperience());
				$player->setLevel($char_to_copy->getLevel());
				$player->setMagLevel($char_to_copy->getMagLevel());
				$player->setHealth($char_to_copy->getHealth());
				$player->setHealthMax($char_to_copy->getHealthMax());
				$player->setMana($char_to_copy->getMana());
				$player->setManaMax($char_to_copy->getManaMax());
				$player->setManaSpent($char_to_copy->getManaSpent());
				$player->setSoul($char_to_copy->getSoul());

				for($skill = POT::SKILL_FIRST; $skill <= POT::SKILL_LAST; $skill++)
					$player->setSkill($skill, 10);

				$player->setLookBody($char_to_copy->getLookBody());
				$player->setLookFeet($char_to_copy->getLookFeet());
				$player->setLookHead($char_to_copy->getLookHead());
				$player->setLookLegs($char_to_copy->getLookLegs());
				$player->setLookType($char_to_copy->getLookType());
				$player->setCap($char_to_copy->getCap());
				$player->setBalance(0);
				$player->setPosX(0);
				$player->setPosY(0);
				$player->setPosZ(0);
				$player->setStamina($config['otserv_version'] == TFS_03 ? 151200000 : 2520);
				if(fieldExist('loss_experience', 'players')) {
					$player->setLossExperience($char_to_copy->getLossExperience());
					$player->setLossMana($char_to_copy->getLossMana());
					$player->setLossSkills($char_to_copy->getLossSkills());
				}
				if(fieldExist('loss_items', 'players')) {
					$player->setLossItems($char_to_copy->getLossItems());
					$player->setLossContainers($char_to_copy->getLossContainers());
				}
					
				$player->save();
				$player->setCustomField("created", time());
				
				$newchar_created = true;
				$account_logged->logAction('Created character <b>' . $player->getName() . '</b>.');
				unset($player);
				$player = new OTS_Player();
				$player->find($newchar_name);
				if($player->isLoaded())
				{
					if(tableExist('player_skills')) {
						for($i=0;$i<7;$i++)
						{
							$skillExists = $db->query('SELECT `skillid` FROM `player_skills` WHERE `player_id` = ' . $player->getId() . ' AND `skillid` = ' . $i);
							if($skillExists->rowCount() <= 0)
							{
								$db->query('INSERT INTO `player_skills` (`player_id`, `skillid`, `value`, `count`) VALUES ('.$player->getId().', '.$i.', 10, 0)');
							}
						}
					}

					$loaded_items_to_copy = $db->query("SELECT * FROM player_items WHERE player_id = ".$char_to_copy->getId()."");
					foreach($loaded_items_to_copy as $save_item)
						$db->query("INSERT INTO `player_items` (`player_id` ,`pid` ,`sid` ,`itemtype`, `count`, `attributes`) VALUES ('".$player->getId()."', '".$save_item['pid']."', '".$save_item['sid']."', '".$save_item['itemtype']."', '".$save_item['count']."', '".$save_item['attributes']."');");
					
					echo $twig->render('success.html', array(
						'title' => 'Character Created',
						'description' => 'The character <b>' . $newchar_name . '</b> has been created.<br/>
							Please select the outfit when you log in for the first time.<br/><br/>
							<b>See you on ' . $config['lua']['serverName'] . '!</b>'
					));
				}
				else
				{
					echo "Error. Can\'t create character. Probably problem with database. Try again or contact with admin.";
					exit;
				}
			}
		}

		if(count($errors) > 0) {
			echo $twig->render('error_box.html', array('errors' => $errors));
		}
		
		if(!$newchar_created) {
			echo $twig->render('account.create_character.html', array(
				'config' => $config,
				'newchar_name' => $newchar_name
				
			));
			
			echo '<div class="InnerTableContainer" >          <table style="width:100%;" ><tr>';
			if(count($config['character_samples']) > 1)
			{
				echo '<td><table class="TableContent" width="100%" ><tr class="Odd" valign="top"><td width="160"><br /><b>Select your vocation:</b></td><td><table class="TableContent" width="100%" >';
				foreach($config['character_samples'] as $key => $sample_char)
				{
					echo '<tr><td><input type="radio" name="vocation" id="vocation' . $key . '" value="'.$key.'" ';
					if($newchar_vocation == $key)
						echo 'checked="checked" ';
					echo '><label for="vocation' . $key . '">'.$config['vocations'][$key].'</label></td></tr>';
				}
				echo '</table></td></table>';
			}
			if(count($config['character_towns']) > 1)
			{
				echo '<td><table class="TableContent" width="100%" ><tr class="Odd" valign="top"><td width="160"><br /><b>Select your city:</b></td><td><table class="TableContent" width="100%" >';
				foreach($config['character_towns'] as $town_id)
				{
					echo '<tr><td><input type="radio" name="town" id="town' . $town_id . '" value="' . $town_id . '" ';
					if($newchar_town == $town_id)
						echo 'checked="checked" ';
					echo '><label for="town' . $town_id . '">'.$config['towns'][$town_id].'</label></td></tr>';
				}
				echo '</table></td></tr></table></table></div>';
			}
			else
				echo '</tr></table></div>';
			echo '</table></div></td></tr><br/><table style="width:100%;" ><tr align="center" ><td><table border="0" cellspacing="0" cellpadding="0" ><tr><td style="border:0px;" ><div class="BigButton" style="background-image:url('.$template_path.'/images/buttons/sbutton.gif)" ><div onMouseOver="MouseOverBigButton(this);" onMouseOut="MouseOutBigButton(this);" ><div class="BigButtonOver" style="background-image:url('.$template_path.'/images/buttons/sbutton_over.gif);" ></div><input class="ButtonText" type="image" name="Submit" alt="Submit" src="'.$template_path.'/images/buttons/_sbutton_submit.gif" ></div></div></td><tr></form></table></td><td><table border="0" cellspacing="0" cellpadding="0" ><form action="?subtopic=accountmanagement" method="post" ><tr><td style="border:0px;" ><div class="BigButton" style="background-image:url('.$template_path.'/images/buttons/sbutton.gif)" ><div onMouseOver="MouseOverBigButton(this);" onMouseOut="MouseOutBigButton(this);" ><div class="BigButtonOver" style="background-image:url('.$template_path.'/images/buttons/sbutton_over.gif);" ></div><input class="ButtonText" type="image" name="Back" alt="Back" src="'.$template_path.'/images/buttons/_sbutton_back.gif" ></div></div></td></tr></form></table></td></tr></table>';
		}
	}
?>
