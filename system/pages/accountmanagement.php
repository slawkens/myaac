<?php
/**
 * Account management
 *
 * @package   MyAAC
 * @author    Gesior <jerzyskalski@wp.pl>
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2017 MyAAC
 * @version   0.1.5
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
	if($action == "logout")
		echo '<div class="TableContainer">' .
			'<table class="Table1" cellpadding="0" cellspacing="0" >' .
				'<div class="CaptionContainer">' .
					'<div class="CaptionInnerContainer" >' .
						'<span class="CaptionEdgeLeftTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);"/></span>' .
						'<span class="CaptionEdgeRightTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);"/></span>' .
						'<span class="CaptionBorderTop" style="background-image:url('.$template_path.'/images/content/table-headline-border.gif);" ></span>' .
						'<span class="CaptionVerticalLeft" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);"/></span>' .
						'<div class="Text">Logout Successful</div>' .
						'<span class="CaptionVerticalRight" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);"/></span>
						<span class="CaptionBorderBottom" style="background-image:url('.$template_path.'/images/content/table-headline-border.gif);" ></span>
						<span class="CaptionEdgeLeftBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);"/></span>
						<span class="CaptionEdgeRightBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);"/></span>
					</div>
				</div>
				<tr>
					<td>
						<div class="InnerTableContainer">
							<table style="width:100%;">
								<tr>
									<td>You have logged out of your '.$config['lua']['serverName'].' account. In order to view your account you need to <a href="?subtopic=accountmanagement" >log in</a> again.</td>
								</tr>
							</table>
						</div>
					</table>
				</div>
			</td>
		</tr>';
	else
	{
		if(!empty($errors))
			output_errors($errors);
	?>
Please enter your account name and your password.<br/><a href="?subtopic=createaccount" >Create an account</a> if you do not have one yet.<br/><br/>
	<form action="?subtopic=accountmanagement" method="post" >
		<?php if(isset($_REQUEST['redirect'])): ?>
		<input type="hidden" name="redirect" value="<?php echo $_REQUEST['redirect']; ?>" />
		<?php endif; ?>
		<div class="TableContainer" >
			<table class="Table1" cellpadding="0" cellspacing="0" >
				<div class="CaptionContainer" >
					<div class="CaptionInnerContainer" >
						<span class="CaptionEdgeLeftTop" style="background-image:url(<?php echo $template_path; ?>/images/content/box-frame-edge.gif);" /></span>
						<span class="CaptionEdgeRightTop" style="background-image:url(<?php echo $template_path; ?>/images/content/box-frame-edge.gif);" /></span>
						<span class="CaptionBorderTop" style="background-image:url(<?php echo $template_path; ?>/images/content/table-headline-border.gif);" ></span>
						<span class="CaptionVerticalLeft" style="background-image:url(<?php echo $template_path; ?>/images/content/box-frame-vertical.gif);" /></span>
						<div class="Text" >Account Login</div>
						<span class="CaptionVerticalRight" style="background-image:url(<?php echo $template_path; ?>/images/content/box-frame-vertical.gif);" /></span>
						<span class="CaptionBorderBottom" style="background-image:url(<?php echo $template_path; ?>/images/content/table-headline-border.gif);" ></span>
						<span class="CaptionEdgeLeftBottom" style="background-image:url(<?php echo $template_path; ?>/images/content/box-frame-edge.gif);" /></span>
						<span class="CaptionEdgeRightBottom" style="background-image:url(<?php echo $template_path; ?>/images/content/box-frame-edge.gif);" /></span>
					</div>
				</div>
				<tr>
					<td><div class="InnerTableContainer" >
					<table style="width:100%;" >
						<tr>
							<td class="LabelV" >
								<span<?php echo (isset($errors[0]) ? ' class="red"' : ''); ?>>Account <?php echo (USE_ACCOUNT_NAME ? 'Name' : 'Number'); ?>:</span>
							</td>
							<td style="width:100%;" ><input type="password" name="account_login" id="account-name-input" size="30" maxlength="30" ></td>
						</tr>
						<tr>
							<td class="LabelV" >
								<span<?php echo (isset($errors[0]) ? ' class="red"' : ''); ?>>Password:</span>
							</td>
							<td><input type="password" name="password_login" size="30" maxlength="29" ></td>
						</tr>
						<tr>
							<td class="LabelV" ></td>
							<td><input type="checkbox" id="remember_me" name="remember_me" value="true" />
							<label for="remember_me"> Remember me</label></td>
						</tr>
						<?php
						if(isset($errors[0]))
							echo '<tr><td></td><td><span class="FormFieldError">' . $errors[0] . '</span></td></tr>';
						?>
					</table>
				</div>
			</table>
		</div>
		</td>
		</tr><br/>
		<table width="100%" >
			<tr align="center" ><td><table border="0" cellspacing="0" cellpadding="0" >
		<tr><td style="border:0px;" >
		<div class="BigButton" style="background-image:url(<?php echo $template_path; ?>/images/buttons/sbutton.gif)" >
		<div onMouseOver="MouseOverBigButton(this);" onMouseOut="MouseOutBigButton(this);" >
		<div class="BigButtonOver" style="background-image:url(<?php echo $template_path; ?>/images/buttons/sbutton_over.gif);" ></div>
		<input class="ButtonText" type="image" name="Submit" alt="Submit" src="<?php echo $template_path; ?>/images/buttons/_sbutton_submit.gif" >
		</div></div></td><tr></form></table></td><td><table border="0" cellspacing="0" cellpadding="0" >
		<form action="?subtopic=lostaccount" method="post" ><tr><td style="border:0px;" >
		<div class="BigButton" style="background-image:url(<?php echo $template_path; ?>/images/buttons/sbutton.gif)" >
		<div onMouseOver="MouseOverBigButton(this);" onMouseOut="MouseOutBigButton(this);" >
		<div class="BigButtonOver" style="background-image:url(<?php echo $template_path; ?>/images/buttons/sbutton_over.gif);" ></div>
		<input class="ButtonText" type="image" name="Account lost?" alt="Account lost?" src="<?php echo $template_path; ?>/images/buttons/_sbutton_accountlost.gif" >
		</div></div></td></tr></form></table></td></tr></table>

		<script type="text/javascript">
			$(function() {
				$('#account-name-input').focus();
			});
		</script>
	<?php
	return;
	}
}

	if(isset($_REQUEST['redirect']))
	{
		$redirect = urldecode($_REQUEST['redirect']);
?>
	<div class="TableContainer">
		<table class="Table1" cellpadding="0" cellspacing="0">
			<div class="CaptionContainer">
				<div class="CaptionInnerContainer">
					<span class="CaptionEdgeLeftTop" style="background-image:url(<?php echo $template_path; ?>/images/content/box-frame-edge.gif);" /></span>
					<span class="CaptionEdgeRightTop" style="background-image:url(<?php echo $template_path; ?>/images/content/box-frame-edge.gif);" /></span>
					<span class="CaptionBorderTop" style="background-image:url(<?php echo $template_path; ?>/images/content/table-headline-border.gif);" ></span>
					<span class="CaptionVerticalLeft" style="background-image:url(<?php echo $template_path; ?>/images/content/box-frame-vertical.gif);" /></span>
					<div class="Text" >Login Successful</div>
					<span class="CaptionVerticalRight" style="background-image:url(<?php echo $template_path; ?>/images/content/box-frame-vertical.gif);" /></span>
					<span class="CaptionBorderBottom" style="background-image:url(<?php echo $template_path; ?>/images/content/table-headline-border.gif);" ></span>
					<span class="CaptionEdgeLeftBottom" style="background-image:url(<?php echo $template_path; ?>/images/content/box-frame-edge.gif);" /></span>
					<span class="CaptionEdgeRightBottom" style="background-image:url(<?php echo $template_path; ?>/images/content/box-frame-edge.gif);" /></span>
				</div>
			</div>
			<tr>
				<td>
					<div class="InnerTableContainer">
						<table style="width:100%;" >
							<tr>
								<td>You have logged in.<br/>Press <a href="<?php echo $redirect; ?>" >here</a> if you are not returned automatically.</td>
							</tr>
						</table>
					</div>
				</table>
			</div>
		</td>
	</tr>

<script language="javascript" type="text/javascript">
<!-- // Automatic redirect
	setTimeout ("automaticRedirect()", 1000);
	function automaticRedirect() {
		window.location = "<?php echo $redirect; ?>";
	}
// -->
</script>
<?php
		return;
	}

	if($action == "")
	{
		$freePremium = isset($config['lua']['freePremium']) && getBoolean($config['lua']['freePremium']);
		$account_reckey = $account_logged->getCustomField("key");
		if(!$account_logged->isPremium())
			$account_status = '<b><font color="red">Free Account</font></b>';
		else
			$account_status = '<b><font color="green">Premium Account, ' . ($freePremium ? 'Unlimited' : $account_logged->getPremDays() . ' days left') . '</font></b>';
		if(empty($account_reckey))
			$account_registred = '<b><font color="red">No</font></b>';
		else
		{
			if($config['generate_new_reckey'] && $config['mail_enabled'])
				$account_registred = '<b><font color="green">Yes ( <a href="?subtopic=accountmanagement&action=newreckey"> Buy new Recovery Key </a> )</font></b>';
			else
				$account_registred = '<b><font color="green">Yes</font></b>';
		}

		$account_created = $account_logged->getCustomField("created");
		$account_email = $account_logged->getEMail();
		$account_email_new_time = $account_logged->getCustomField("email_new_time");
		if($account_email_new_time > 1)
			$account_email_new = $account_logged->getCustomField("email_new");
		$account_rlname = $account_logged->getRLName();
		$account_location = $account_logged->getLocation();
		if($account_logged->isBanned())
			if($account_logged->getBanTime() > 0)
				$welcome_msg = '<font color="red">Your account is banished until '.date("j F Y, G:i:s", $account_logged->getBanTime()).'!</font>';
			else
				$welcome_msg = '<font color="red">Your account is banished FOREVER!</font>';
		else
			$welcome_msg = 'Welcome to your account!';
		echo '<div class="SmallBox" >  <div class="MessageContainer" ><div class="BoxFrameHorizontal" style="background-image:url('.$template_path.'/images/content/box-frame-horizontal.gif);" /></div><div class="BoxFrameEdgeLeftTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div><div class="BoxFrameEdgeRightTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div><div class="Message" >      <div class="BoxFrameVerticalLeft" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></div><div class="BoxFrameVerticalRight" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></div><table><td width="100%"></td><td><table border="0" cellspacing="0" cellpadding="0" ><form action="?subtopic=accountmanagement&action=logout" method="post" ><tr><td style="border:0px;" ><div class="BigButton" style="background-image:url('.$template_path.'/images/buttons/sbutton.gif)" ><div onMouseOver="MouseOverBigButton(this);" onMouseOut="MouseOutBigButton(this);" ><div class="BigButtonOver" style="background-image:url('.$template_path.'/images/buttons/sbutton_over.gif);" ></div><input class="ButtonText" type="image" name="Logout" alt="Logout" src="'.$template_path.'/images/buttons/_sbutton_logout.gif" ></div></div></td></tr></form></table></td></tr></table>    </div><div class="BoxFrameHorizontal" style="background-image:url('.$template_path.'/images/content/box-frame-horizontal.gif);" /></div>    <div class="BoxFrameEdgeRightBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="BoxFrameEdgeLeftBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>  </div></div><br/><center><table><tr><td><img src="'.$template_path.'/images/content/headline-bracer-left.gif" /></td><td style="text-align:center;vertical-align:middle;horizontal-align:center;font-size:17px;font-weight:bold;" >'.$welcome_msg.'<br/></td><td><img src="'.$template_path.'/images/content/headline-bracer-right.gif" /></td></tr></table><br/></center>';
		//if account dont have recovery key show hint
		if(empty($account_reckey))
			echo '<div class="SmallBox" ><div class="MessageContainer" >    <div class="BoxFrameHorizontal" style="background-image:url('.$template_path.'/images/content/box-frame-horizontal.gif);" /></div><div class="BoxFrameEdgeLeftTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div><div class="BoxFrameEdgeRightTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div><div class="Message" ><div class="BoxFrameVerticalLeft" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></div><div class="BoxFrameVerticalRight" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></div><table><tr><td class="LabelV" >Hint:</td><td style="width:100%;" >You can register your account for increased protection. Click on "Register Account" and get your free recovery key today!</td></tr></table><div align="center" ><table border="0" cellspacing="0" cellpadding="0" ><form action="?subtopic=accountmanagement&action=registeraccount" method="post" ><tr><td style="border:0px;" ><div class="BigButton" style="background-image:url('.$template_path.'/images/buttons/sbutton.gif)" ><div onMouseOver="MouseOverBigButton(this);" onMouseOut="MouseOutBigButton(this);" ><div class="BigButtonOver" style="background-image:url('.$template_path.'/images/buttons/sbutton_over.gif);" ></div><input class="ButtonText" type="image" name="Register Account" alt="Register Account" src="'.$template_path.'/images/buttons/_sbutton_registeraccount.gif" ></div></div></td></tr></form></table></div></div>    <div class="BoxFrameHorizontal" style="background-image:url('.$template_path.'/images/content/box-frame-horizontal.gif);" /></div><div class="BoxFrameEdgeRightBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div><div class="BoxFrameEdgeLeftBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>  </div></div><br/>';
		
		$account_email_change = '';
		if($account_email_new_time > 1)
		{
			if($account_email_new_time < time())
				$account_email_change = '<br>(You can accept <b>'.$account_email_new.'</b> as a new email.)';
			else
			{
				$account_email_change = ' <br>You can accept <b>new e-mail after '.date("j F Y", $account_email_new_time).".</b>";
				echo '<div class="SmallBox" >  <div class="MessageContainer" >    <div class="BoxFrameHorizontal" style="background-image:url('.$template_path.'/images/content/box-frame-horizontal.gif);" /></div>    <div class="BoxFrameEdgeLeftTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="BoxFrameEdgeRightTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="Message" >      <div class="BoxFrameVerticalLeft" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></div>      <div class="BoxFrameVerticalRight" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></div><table><tr><td class="LabelV" >Note:</td><td style="width:100%;" >A request has been submitted to change the email address of this account to <b>'.$account_email_new.'</b>. After <b>'.date("j F Y, G:i:s", $account_email_new_time).'</b> you can accept the new email address and finish the process. Please cancel the request if you do not want your email address to be changed! Also cancel the request if you have no access to the new email address!</td></tr></table><div align="center" ><table border="0" cellspacing="0" cellpadding="0" ><form action="?subtopic=accountmanagement&action=changeemail" method="post" ><tr><td style="border:0px;" ><div class="BigButton" style="background-image:url('.$template_path.'/images/buttons/sbutton.gif)" ><div onMouseOver="MouseOverBigButton(this);" onMouseOut="MouseOutBigButton(this);" ><div class="BigButtonOver" style="background-image:url('.$template_path.'/images/buttons/sbutton_over.gif);" ></div><input class="ButtonText" type="image" name="Edit" alt="Edit" src="'.$template_path.'/images/buttons/_sbutton_edit.gif" ></div></div></td></tr></form></table></div>    </div>    <div class="BoxFrameHorizontal" style="background-image:url('.$template_path.'/images/content/box-frame-horizontal.gif);" /></div>    <div class="BoxFrameEdgeRightBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="BoxFrameEdgeLeftBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>  </div></div><br/><br/>';
			}
		}
		echo '<a name="General+Information" ></a><div class="TopButtonContainer" ><div class="TopButton" ><a href="#top" >	<img style="border:0px;" src="'.$template_path.'/images/content/back-to-top.gif" /></a></div></div><div class="TableContainer" ><table class="Table3" cellpadding="0" cellspacing="0" >    <div class="CaptionContainer" >      <div class="CaptionInnerContainer" >        <span class="CaptionEdgeLeftTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span><span class="CaptionEdgeRightTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span><span class="CaptionBorderTop" style="background-image:url('.$template_path.'/images/content/table-headline-border.gif);" ></span><span class="CaptionVerticalLeft" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></span><div class="Text" >General Information</div><span class="CaptionVerticalRight" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></span><span class="CaptionBorderBottom" style="background-image:url('.$template_path.'/images/content/table-headline-border.gif);" ></span><span class="CaptionEdgeLeftBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span><span class="CaptionEdgeRightBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span></div>    </div><tr>      <td>        <div class="InnerTableContainer" >          <table style="width:100%;" ><tr><td><div class="TableShadowContainerRightTop" >  <div class="TableShadowRightTop" style="background-image:url('.$template_path.'/images/content/table-shadow-rt.gif);" ></div></div><div class="TableContentAndRightShadow" style="background-image:url('.$template_path.'/images/content/table-shadow-rm.gif);" ><div class="TableContentContainer" >    <table class="TableContent" width="100%" >
		<tr style="background-color:'.$config['lightborder'].';" >
			<td class="LabelV" >Account ' . (USE_ACCOUNT_NAME ? 'Name' : 'Number') . ':</td>
			<td style="width:90%;" >' . (USE_ACCOUNT_NAME ? $account_logged->getName() : $account_logged->getId()) . '</td>
		</tr>
		<tr style="background-color:'.$config['darkborder'].';" >
			<td class="LabelV" >Email Address:</td>
			<td style="width:90%;" >'.$account_email.''.$account_email_change.'</td>
		</tr>
		<tr style="background-color:'.$config['lightborder'].';" >
			<td class="LabelV" >Created:</td>
			<td>'.date("j F Y, G:i:s", $account_created).'</td>
		</tr>
		<tr style="background-color:'.$config['darkborder'].';" >
		<td class="LabelV" >Last Login:</td><td>'.date("j F Y, G:i:s", time()).'</td></tr><tr style="background-color:'.$config['lightborder'].';" ><td class="LabelV" >Account Status:</td><td>'.$account_status.'</td></tr><tr style="background-color:'.$config['darkborder'].';" ><td class="LabelV" >Registred:</td><td>'.$account_registred.'</td></tr></table></div></div><div class="TableShadowContainer" ><div class="TableBottomShadow" style="background-image:url('.$template_path.'/images/content/table-shadow-bm.gif);" ><div class="TableBottomLeftShadow" style="background-image:url('.$template_path.'/images/content/table-shadow-bl.gif);" ></div><div class="TableBottomRightShadow" style="background-image:url('.$template_path.'/images/content/table-shadow-br.gif);" ></div>  </div></div></td></tr><tr><td><table class="InnerTableButtonRow" cellpadding="0" cellspacing="0" ><tr><td><table border="0" cellspacing="0" cellpadding="0" ><form action="?subtopic=accountmanagement&action=changepassword" method="post" ><tr><td style="border:0px;" ><div class="BigButton" style="background-image:url('.$template_path.'/images/buttons/sbutton.gif)" ><div onMouseOver="MouseOverBigButton(this);" onMouseOut="MouseOutBigButton(this);" ><div class="BigButtonOver" style="background-image:url('.$template_path.'/images/buttons/sbutton_over.gif);" ></div><input class="ButtonText" type="image" name="Change Password" alt="Change Password" src="'.$template_path.'/images/buttons/_sbutton_changepassword.gif" ></div></div></td></tr></form></table></td><td><table border="0" cellspacing="0" cellpadding="0" ><form action="?subtopic=accountmanagement&action=changeemail" method="post" ><tr><td style="border:0px;" ><input type="hidden" name=newemail value="" ><input type="hidden" name=newemaildate value=0 ><div class="BigButton" style="background-image:url('.$template_path.'/images/buttons/sbutton.gif)" ><div onMouseOver="MouseOverBigButton(this);" onMouseOut="MouseOutBigButton(this);" ><div class="BigButtonOver" style="background-image:url('.$template_path.'/images/buttons/sbutton_over.gif);" ></div><input class="ButtonText" type="image" name="Change Email" alt="Change Email" src="'.$template_path.'/images/buttons/_sbutton_changeemail.gif" ></div></div></td></tr></form>	</table></td><td width="100%"></td>';
		//show button "register account"
		if(empty($account_reckey))
			echo '<td><table border="0" cellspacing="0" cellpadding="0" ><form action="?subtopic=accountmanagement&action=registeraccount" method="post" ><tr><td style="border:0px;" ><div class="BigButton" style="background-image:url('.$template_path.'/images/buttons/sbutton.gif)" ><div onMouseOver="MouseOverBigButton(this);" onMouseOut="MouseOutBigButton(this);" ><div class="BigButtonOver" style="background-image:url('.$template_path.'/images/buttons/sbutton_over.gif);" ></div><input class="ButtonText" type="image" name="Register Account" alt="Register Account" src="'.$template_path.'/images/buttons/_sbutton_registeraccount.gif" ></div></div></td></tr></form></table></td>';
		echo '</tr></table></td></tr></table></div></table></div></td></tr><br/><a name="Public+Information" ></a><div class="TopButtonContainer" ><div class="TopButton" ><a href="#top" ><img style="border:0px;" src="'.$template_path.'/images/content/back-to-top.gif" /></a></div></div><div class="TableContainer" >  <table class="Table5" cellpadding="0" cellspacing="0" >    <div class="CaptionContainer" >      <div class="CaptionInnerContainer" ><span class="CaptionEdgeLeftTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span><span class="CaptionEdgeRightTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span><span class="CaptionBorderTop" style="background-image:url('.$template_path.'/images/content/table-headline-border.gif);" ></span><span class="CaptionVerticalLeft" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></span><div class="Text" >Public Information</div><span class="CaptionVerticalRight" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></span><span class="CaptionBorderBottom" style="background-image:url('.$template_path.'/images/content/table-headline-border.gif);" ></span><span class="CaptionEdgeLeftBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span><span class="CaptionEdgeRightBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span></div>    </div>    <tr>      <td>        <div class="InnerTableContainer" >          <table style="width:100%;" ><tr><td><div class="TableShadowContainerRightTop" >  <div class="TableShadowRightTop" style="background-image:url('.$template_path.'/images/content/table-shadow-rt.gif);" ></div></div><div class="TableContentAndRightShadow" style="background-image:url('.$template_path.'/images/content/table-shadow-rm.gif);" ><div class="TableContentContainer" >    <table class="TableContent" width="100%" ><tr><td><table style="width:100%;"><tr><td class="LabelV" >Real Name:</td><td style="width:90%;" >'.$account_rlname.'</td></tr><tr><td class="LabelV" >Location:</td><td style="width:90%;" >'.$account_location.'</td></tr></table></td><td align=right><table border="0" cellspacing="0" cellpadding="0" ><form action="?subtopic=accountmanagement&action=changeinfo" method="post" ><tr><td style="border:0px;" ><div class="BigButton" style="background-image:url('.$template_path.'/images/buttons/sbutton.gif)" ><div onMouseOver="MouseOverBigButton(this);" onMouseOut="MouseOutBigButton(this);" ><div class="BigButtonOver" style="background-image:url('.$template_path.'/images/buttons/sbutton_over.gif);" ></div><input class="ButtonText" type="image" name="Edit" alt="Edit" src="'.$template_path.'/images/buttons/_sbutton_edit.gif" ></div></div></td></tr></form></table></td></tr>    </table>  </div></div><div class="TableShadowContainer" >  <div class="TableBottomShadow" style="background-image:url('.$template_path.'/images/content/table-shadow-bm.gif);" >    <div class="TableBottomLeftShadow" style="background-image:url('.$template_path.'/images/content/table-shadow-bl.gif);" ></div>    <div class="TableBottomRightShadow" style="background-image:url('.$template_path.'/images/content/table-shadow-br.gif);" ></div>  </div></div></td></tr>          </table>        </div>  </table></div></td></tr>';
		echo '<br/><a name="Account+Logs" ></a><div class="TopButtonContainer" ><div class="TopButton" ><a href="#top" ><img style="border:0px;" src="'.$template_path.'/images/content/back-to-top.gif" /></a></div></div><div class="TableContainer" >  <table class="Table5" cellpadding="0" cellspacing="0" >    <div class="CaptionContainer" >      <div class="CaptionInnerContainer" ><span class="CaptionEdgeLeftTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span><span class="CaptionEdgeRightTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span><span class="CaptionBorderTop" style="background-image:url('.$template_path.'/images/content/table-headline-border.gif);" ></span><span class="CaptionVerticalLeft" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></span><div class="Text" >Account logs</div><span class="CaptionVerticalRight" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></span><span class="CaptionBorderBottom" style="background-image:url('.$template_path.'/images/content/table-headline-border.gif);" ></span><span class="CaptionEdgeLeftBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span><span class="CaptionEdgeRightBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span></div>    </div>    <tr>      <td>        <div class="InnerTableContainer" >          <table style="width:100%;" ><tr><td><div class="TableShadowContainerRightTop" >  <div class="TableShadowRightTop" style="background-image:url('.$template_path.'/images/content/table-shadow-rt.gif);" ></div></div><div class="TableContentAndRightShadow" style="background-image:url('.$template_path.'/images/content/table-shadow-rm.gif);" ><div class="TableContentContainer" > 
		<table class="TableContent" width="100%" ><tr class="LabelH" ><td style="width:60%" >Action</td><td style="width:25%" >Date</td><td style="width:15%">IP</td></tr>';
		
		$player_number_counter = 0;
		foreach($account_logged->getActionsLog(0, 1000) as $action)
		{
			echo '<tr style="background-color:' . getStyle($player_number_counter++) . '"><td>'.$action['action'] . '</td><td>' . date("jS F Y H:i:s",$action['date']) . '</td>
			<td>' . ($action['ip'] != 0 ? long2ip($action['ip']) : inet_ntop($action['ipv6'])) . '</td></tr>';
		}
		echo '</table>
	
	
		</td><td align=right></td></tr>    </table>  </div></div><div class="TableShadowContainer" >  <div class="TableBottomShadow" style="background-image:url('.$template_path.'/images/content/table-shadow-bm.gif);" >    <div class="TableBottomLeftShadow" style="background-image:url('.$template_path.'/images/content/table-shadow-bl.gif);" ></div>    <div class="TableBottomRightShadow" style="background-image:url('.$template_path.'/images/content/table-shadow-br.gif);" ></div>  </div></div></td></tr>          </table>        </div>  </table></td></tr><br/>';
		echo '<a name="Characters" ></a><div class="TopButtonContainer" ><div class="TopButton" ><a href="#top" ><img style="border:0px;" src="'.$template_path.'/images/content/back-to-top.gif" /></a></div></div><div class="TableContainer" >  <table class="Table5" cellpadding="0" cellspacing="0" >    <div class="CaptionContainer" ><div class="CaptionInnerContainer" >        <span class="CaptionEdgeLeftTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span><span class="CaptionEdgeRightTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span><span class="CaptionBorderTop" style="background-image:url('.$template_path.'/images/content/table-headline-border.gif);" ></span><span class="CaptionVerticalLeft" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></span><div class="Text" >Characters</div>        <span class="CaptionVerticalRight" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></span><span class="CaptionBorderBottom" style="background-image:url('.$template_path.'/images/content/table-headline-border.gif);" ></span><span class="CaptionEdgeLeftBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span><span class="CaptionEdgeRightBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span></div>    </div>   <tr>      <td>        <div class="InnerTableContainer" >          <table style="width:100%;" ><tr><td><div class="TableShadowContainerRightTop" ><div class="TableShadowRightTop" style="background-image:url('.$template_path.'/images/content/table-shadow-rt.gif);" ></div></div><div class="TableContentAndRightShadow" style="background-image:url('.$template_path.'/images/content/table-shadow-rm.gif);" ><div class="TableContentContainer" >    <table class="TableContent" width="100%" ><tr class="LabelH" ><td style="width:65%" >Name</td><td style="width:15%" >Level</td><td style="width:7%">Status</td><td style="width:5%">&#160;</td></tr>';
		$account_players = $account_logged->getPlayersList();
		$account_players->orderBy('id');
		//show list of players on account
		$player_number_counter = 0;
		foreach($account_players as $account_player)
		{
			echo '<tr style="background-color:' . getStyle($player_number_counter++);
			echo ';" ><td><NOBR>'.$player_number_counter.'. '.$account_player->getName();
			if($account_player->isDeleted())
				echo '<font color="red"><b> [ DELETED ] </b></font>';
			echo '</NOBR></td><td><NOBR>'.$account_player->getLevel().' '.$config['vocations'][$account_player->getVocation()].'</NOBR></td>';
			if(!$account_player->isOnline())
				echo '<td><font color="red"><b>Offline</b></font></td>';
			else
				echo '<td><font color="green"><b>Online</b></font></td>';
			echo '<td>[<a href="?subtopic=accountmanagement&action=changecomment&name='.urlencode($account_player->getName()).'" >Edit</a>]</td></tr>';
		}
		echo '</table>  </div></div><div class="TableShadowContainer" >  <div class="TableBottomShadow" style="background-image:url('.$template_path.'/images/content/table-shadow-bm.gif);" >    <div class="TableBottomLeftShadow" style="background-image:url('.$template_path.'/images/content/table-shadow-bl.gif);" ></div>    <div class="TableBottomRightShadow" style="background-image:url('.$template_path.'/images/content/table-shadow-br.gif);" ></div>  </div></div></td></tr><tr><td><table class="InnerTableButtonRow" cellpadding="0" cellspacing="0" ><tr>
		<td>
			<table border="0" cellspacing="0" cellpadding="0" >
				<form action="?subtopic=accountmanagement&action=createcharacter" method="post" >
					<tr>
						<td style="border:0px;" ><div class="BigButton" style="background-image:url('.$template_path.'/images/buttons/sbutton.gif)" ><div onMouseOver="MouseOverBigButton(this);" onMouseOut="MouseOutBigButton(this);" ><div class="BigButtonOver" style="background-image:url('.$template_path.'/images/buttons/sbutton_over.gif);" ></div>
								<input class="ButtonText" type="image" name="Create Character" alt="Create Character" src="'.$template_path.'/images/buttons/_sbutton_createcharacter.gif" ></div>
							</div>
						</td>
					</tr>
				</form>
			</table>
		</td>';
		if($config['account_change_character_name']) {
			echo '
		<td>
			<table border="0" cellspacing="0" cellpadding="0" >
				<form action="?subtopic=accountmanagement&action=changename" method="post" >
					<tr>
						<td style="border:0px;" ><div class="BigButton" style="background-image:url('.$template_path.'/images/buttons/sbutton.gif)" ><div onMouseOver="MouseOverBigButton(this);" onMouseOut="MouseOutBigButton(this);" ><div class="BigButtonOver" style="background-image:url('.$template_path.'/images/buttons/sbutton_over.gif);" ></div>
								<input class="ButtonText" type="image" name="Change Name" alt="Change Name" src="'.$template_path.'/images/buttons/_sbutton_change_name.gif" ></div>
							</div>
						</td>
					</tr>
				</form>
			</table>
		</td>';
		}
		echo '
		<td style="width:100%;" ></td>
		<td><table border="0" cellspacing="0" cellpadding="0" ><form action="?subtopic=accountmanagement&action=deletecharacter" method="post" ><tr><td style="border:0px;" ><div class="BigButton" style="background-image:url('.$template_path.'/images/buttons/sbutton.gif)" ><div onMouseOver="MouseOverBigButton(this);" onMouseOut="MouseOutBigButton(this);" ><div class="BigButtonOver" style="background-image:url('.$template_path.'/images/buttons/sbutton_over.gif);" ></div><input class="ButtonText" type="image" name="Delete Character" alt="Delete Character" src="'.$template_path.'/images/buttons/_sbutton_deletecharacter.gif" ></div></div></td></tr></form></table></td></tr></table></td></tr>          </table>        </div>  </table></td></div></tr><br/><br/>';
	}
//########### CHANGE PASSWORD ##########
	if($action == "changepassword") {
		$new_password = isset($_POST['newpassword']) ? $_POST['newpassword'] : NULL;
		$new_password2 = isset($_POST['newpassword2']) ? $_POST['newpassword2'] : NULL;
		$old_password = isset($_POST['oldpassword']) ? $_POST['oldpassword'] : NULL;
		if(empty($new_password) && empty($new_password2) && empty($old_password)) {
			echo 'Please enter your current password and a new password. For your security, please enter the new password twice.<br/><br/><form action="?subtopic=accountmanagement&action=changepassword" method="post" ><div class="TableContainer" ><table class="Table1" cellpadding="0" cellspacing="0" >    <div class="CaptionContainer" ><div class="CaptionInnerContainer" ><span class="CaptionEdgeLeftTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span><span class="CaptionEdgeRightTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span><span class="CaptionBorderTop" style="background-image:url('.$template_path.'/images/content/table-headline-border.gif);" ></span><span class="CaptionVerticalLeft" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></span><div class="Text" >Change Password</div><span class="CaptionVerticalRight" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></span><span class="CaptionBorderBottom" style="background-image:url('.$template_path.'/images/content/table-headline-border.gif);" ></span><span class="CaptionEdgeLeftBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span><span class="CaptionEdgeRightBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span></div>    </div>    <tr>      <td>        <div class="InnerTableContainer" >          <table style="width:100%;" ><tr><td class="LabelV" ><span >New Password:</span></td><td style="width:90%;" ><input type="password" name="newpassword" size="30" maxlength="29" ></td></tr><tr><td class="LabelV" ><span >New Password Again:</span></td><td><input type="password" name="newpassword2" size="30" maxlength="29" ></td></tr><tr><td class="LabelV" ><span >Current Password:</span></td><td><input type="password" name="oldpassword" size="30" maxlength="29" ></td></tr></table>        </div>  </table></div></td></tr><br/><table style="width:100%;" ><tr align="center"><td><table border="0" cellspacing="0" cellpadding="0" ><tr><td style="border:0px;" ><div class="BigButton" style="background-image:url('.$template_path.'/images/buttons/sbutton.gif)" ><div onMouseOver="MouseOverBigButton(this);" onMouseOut="MouseOutBigButton(this);" ><div class="BigButtonOver" style="background-image:url('.$template_path.'/images/buttons/sbutton_over.gif);" ></div><input class="ButtonText" type="image" name="Submit" alt="Submit" src="'.$template_path.'/images/buttons/_sbutton_submit.gif" ></div></div></td><tr></form></table></td><td><table border="0" cellspacing="0" cellpadding="0" ><form action="?subtopic=accountmanagement" method="post" ><tr><td style="border:0px;" ><div class="BigButton" style="background-image:url('.$template_path.'/images/buttons/sbutton.gif)" ><div onMouseOver="MouseOverBigButton(this);" onMouseOut="MouseOutBigButton(this);" ><div class="BigButtonOver" style="background-image:url('.$template_path.'/images/buttons/sbutton_over.gif);" ></div><input class="ButtonText" type="image" name="Back" alt="Back" src="'.$template_path.'/images/buttons/_sbutton_back.gif" ></div></div></td></tr></form></table></td></tr></table>';
		}
		else
		{
			if(empty($new_password) || empty($new_password2) || empty($old_password)){
				$show_msgs[] = "Please fill in form.";
			}
			if($new_password != $new_password2) {
				$show_msgs[] = "The new passwords do not match!";
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
				echo '<div class="SmallBox" >  <div class="MessageContainer" >    <div class="BoxFrameHorizontal" style="background-image:url('.$template_path.'/images/content/box-frame-horizontal.gif);" /></div>    <div class="BoxFrameEdgeLeftTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="BoxFrameEdgeRightTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="ErrorMessage" >      <div class="BoxFrameVerticalLeft" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></div>      <div class="BoxFrameVerticalRight" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></div>      <div class="AttentionSign" style="background-image:url('.$template_path.'/images/content/attentionsign.gif);" /></div><b>The Following Errors Have Occurred:</b><br/>';
				foreach($show_msgs as $show_msg) {
					echo '<li>'.$show_msg;
				}
				echo '</div>    <div class="BoxFrameHorizontal" style="background-image:url('.$template_path.'/images/content/box-frame-horizontal.gif);" /></div>    <div class="BoxFrameEdgeRightBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="BoxFrameEdgeLeftBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>  </div></div><br/>';
				//show form
				echo 'Please enter your current password and a new password. For your security, please enter the new password twice.<br/><br/><form action="?subtopic=accountmanagement&action=changepassword" method="post" ><div class="TableContainer" ><table class="Table1" cellpadding="0" cellspacing="0" >    <div class="CaptionContainer" ><div class="CaptionInnerContainer" ><span class="CaptionEdgeLeftTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span><span class="CaptionEdgeRightTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span><span class="CaptionBorderTop" style="background-image:url('.$template_path.'/images/content/table-headline-border.gif);" ></span><span class="CaptionVerticalLeft" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></span><div class="Text" >Change Password</div><span class="CaptionVerticalRight" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></span><span class="CaptionBorderBottom" style="background-image:url('.$template_path.'/images/content/table-headline-border.gif);" ></span><span class="CaptionEdgeLeftBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span><span class="CaptionEdgeRightBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span></div>    </div>    <tr>      <td>        <div class="InnerTableContainer" >          <table style="width:100%;" ><tr><td class="LabelV" ><span >New Password:</span></td><td style="width:90%;" ><input type="password" name="newpassword" size="30" maxlength="29" ></td></tr><tr><td class="LabelV" ><span >New Password Again:</span></td><td><input type="password" name="newpassword2" size="30" maxlength="29" ></td></tr><tr><td class="LabelV" ><span >Current Password:</span></td><td><input type="password" name="oldpassword" size="30" maxlength="29" ></td></tr></table>        </div>  </table></div></td></tr><br/><table style="width:100%;" ><tr align="center"><td><table border="0" cellspacing="0" cellpadding="0" ><tr><td style="border:0px;" ><div class="BigButton" style="background-image:url('.$template_path.'/images/buttons/sbutton.gif)" ><div onMouseOver="MouseOverBigButton(this);" onMouseOut="MouseOutBigButton(this);" ><div class="BigButtonOver" style="background-image:url('.$template_path.'/images/buttons/sbutton_over.gif);" ></div><input class="ButtonText" type="image" name="Submit" alt="Submit" src="'.$template_path.'/images/buttons/_sbutton_submit.gif" ></div></div></td><tr></form></table></td><td><table border="0" cellspacing="0" cellpadding="0" ><form action="?subtopic=accountmanagement" method="post" ><tr><td style="border:0px;" ><div class="BigButton" style="background-image:url('.$template_path.'/images/buttons/sbutton.gif)" ><div onMouseOver="MouseOverBigButton(this);" onMouseOut="MouseOutBigButton(this);" ><div class="BigButtonOver" style="background-image:url('.$template_path.'/images/buttons/sbutton_over.gif);" ></div><input class="ButtonText" type="image" name="Back" alt="Back" src="'.$template_path.'/images/buttons/_sbutton_back.gif" ></div></div></td></tr></form></table></td></tr></table>';
			}
			else
			{
				$org_pass = $new_password;
				
				if($config_salt_enabled)
				{
					$salt = generateRandomString(10, false, true, true);
					$new_password = $salt . $new_password;
					$account_logged->setSalt($salt);
				}
		
				$new_password = encrypt($new_password);
				$account_logged->setPassword($new_password);
				$account_logged->save();
				$account_logged->logAction('Account password changed.');
				echo '<div class="TableContainer" >  <table class="Table1" cellpadding="0" cellspacing="0" >    <div class="CaptionContainer" >      <div class="CaptionInnerContainer" >        <span class="CaptionEdgeLeftTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionEdgeRightTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionBorderTop" style="background-image:url('.$template_path.'/images/content/table-headline-border.gif);" ></span>        <span class="CaptionVerticalLeft" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></span>        <div class="Text" >Password Changed</div>        <span class="CaptionVerticalRight" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></span>        <span class="CaptionBorderBottom" style="background-image:url('.$template_path.'/images/content/table-headline-border.gif);" ></span>        <span class="CaptionEdgeLeftBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionEdgeRightBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>      </div>    </div>    <tr>      <td>        <div class="InnerTableContainer" >          <table style="width:100%;" ><tr><td>Your password has been changed.';
				if($config['mail_enabled'] && $config['send_mail_when_change_password'])
				{
					$mailBody = '
					<h3>Password to account changed!</h3>
					<p>You or someone else changed password to your account on server <a href="'.BASE_URL.'"><b>'.$config['lua']['serverName'].'</b></a>.</p>
					<p>New password: <b>'.$org_pass.'</b></p>';

					if(_mail($account_logged->getEMail(), $config['lua']['serverName']." - Changed password", $mailBody))
						echo '<br /><small>Your new password were send on email address <b>'.$account_logged->getEMail().'</b>.</small>';
					else
						echo '<br /><p class="error">An error occorred while sending email with password:<br/>' . $mailer->ErrorInfo . '</p>';
				}
				echo '</td></tr>          </table>        </div>  </table></div></td></tr><br/><center><table border="0" cellspacing="0" cellpadding="0" ><form action="?subtopic=accountmanagement" method="post" ><tr><td style="border:0px;" ><div class="BigButton" style="background-image:url('.$template_path.'/images/buttons/sbutton.gif)" ><div onMouseOver="MouseOverBigButton(this);" onMouseOut="MouseOutBigButton(this);" ><div class="BigButtonOver" style="background-image:url('.$template_path.'/images/buttons/sbutton_over.gif);" ></div><input class="ButtonText" type="image" name="Back" alt="Back" src="'.$template_path.'/images/buttons/_sbutton_back.gif" ></div></div></td></tr></form></table></center>';
				$_SESSION['password'] = $new_password;
			}
		}
	}

//############# CHANGE E-MAIL ###################
	if($action == "changeemail") {
	$account_email_new_time = $account_logged->getCustomField("email_new_time");
	if($account_email_new_time > 10) {$account_email_new = $account_logged->getCustomField("email_new"); }
	if($account_email_new_time < 10){
	if(isset($_POST['changeemailsave']) && $_POST['changeemailsave'] == 1) {
		$account_email_new = $_POST['new_email'];
		$post_password = $_POST['password'];
		if(empty($account_email_new)) {
			$change_email_errors[] = "Please enter your new email address.";
		}
		else
		{
			if(!check_mail($account_email_new)) {
				$change_email_errors[] = "E-mail address is not correct.";
			}
		}
		if(empty($post_password)) {
			$change_email_errors[] = "Please enter password to your account.";
		}
		else
		{
			$post_password = encrypt(($config_salt_enabled ? $account_logged->getCustomField('salt') : '') . $post_password);
			if($post_password != $account_logged->getPassword()) {
				$change_email_errors[] = "Wrong password to account.";
			}
		}
		if(empty($change_email_errors)) {
			$account_email_new_time = time() + $config['account_mail_change'] * 24 * 3600;
			$account_logged->setCustomField("email_new", $account_email_new);
			$account_logged->setCustomField("email_new_time", $account_email_new_time);
			echo '<div class="TableContainer" >  <table class="Table1" cellpadding="0" cellspacing="0" >    <div class="CaptionContainer" >      <div class="CaptionInnerContainer" >        <span class="CaptionEdgeLeftTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionEdgeRightTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionBorderTop" style="background-image:url('.$template_path.'/images/content/table-headline-border.gif);" ></span>        <span class="CaptionVerticalLeft" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></span>        <div class="Text" >New Email Address Requested</div>        <span class="CaptionVerticalRight" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></span>        <span class="CaptionBorderBottom" style="background-image:url('.$template_path.'/images/content/table-headline-border.gif);" ></span>        <span class="CaptionEdgeLeftBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionEdgeRightBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>      </div>    </div>    <tr>      <td>        <div class="InnerTableContainer" >          <table style="width:100%;" ><tr><td>You have requested to change your email address to <b>'.$account_email_new.'</b>. The actual change will take place after <b>'.date("j F Y, G:i:s", $account_email_new_time).'</b>, during which you can cancel the request at any time.</td></tr>          </table>        </div>  </table></div></td></tr><br/><center><table border="0" cellspacing="0" cellpadding="0" ><form action="?subtopic=accountmanagement" method="post" ><tr><td style="border:0px;" ><div class="BigButton" style="background-image:url('.$template_path.'/images/buttons/sbutton.gif)" ><div onMouseOver="MouseOverBigButton(this);" onMouseOut="MouseOutBigButton(this);" ><div class="BigButtonOver" style="background-image:url('.$template_path.'/images/buttons/sbutton_over.gif);" ></div><input class="ButtonText" type="image" name="Back" alt="Back" src="'.$template_path.'/images/buttons/_sbutton_back.gif" ></div></div></td></tr></form></table></center>';
		}
		else
		{
			//show errors
			echo '<div class="SmallBox" >  <div class="MessageContainer" >    <div class="BoxFrameHorizontal" style="background-image:url('.$template_path.'/images/content/box-frame-horizontal.gif);" /></div>    <div class="BoxFrameEdgeLeftTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="BoxFrameEdgeRightTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="ErrorMessage" >      <div class="BoxFrameVerticalLeft" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></div>      <div class="BoxFrameVerticalRight" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></div>      <div class="AttentionSign" style="background-image:url('.$template_path.'/images/content/attentionsign.gif);" /></div><b>The Following Errors Have Occurred:</b><br/>';
			foreach($change_email_errors as $change_email_error) {
				echo '<li>'.$change_email_error;
			}
			echo '</div>    <div class="BoxFrameHorizontal" style="background-image:url('.$template_path.'/images/content/box-frame-horizontal.gif);" /></div>    <div class="BoxFrameEdgeRightBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="BoxFrameEdgeLeftBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>  </div></div><br/>';
			//show form
			echo 'Please enter your password and the new email address. Make sure that you enter a valid email address which you have access to. <b>For security reasons, the actual change will be finalised after a waiting period of '.$config['account_mail_change'].' days.</b><br/><br/><form action="?subtopic=accountmanagement&action=changeemail" method="post" ><div class="TableContainer" >  <table class="Table1" cellpadding="0" cellspacing="0" >    <div class="CaptionContainer" >      <div class="CaptionInnerContainer" >        <span class="CaptionEdgeLeftTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionEdgeRightTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionBorderTop" style="background-image:url('.$template_path.'/images/content/table-headline-border.gif);" ></span>        <span class="CaptionVerticalLeft" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></span>        <div class="Text" >Change Email Address</div>        <span class="CaptionVerticalRight" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></span>        <span class="CaptionBorderBottom" style="background-image:url('.$template_path.'/images/content/table-headline-border.gif);" ></span>        <span class="CaptionEdgeLeftBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionEdgeRightBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>      </div>    </div>    <tr>      <td>        <div class="InnerTableContainer" >          <table style="width:100%;" ></tr><td class="LabelV" ><span >New Email Address:</span></td>  <td style="width:90%;" ><input name="new_email" value="'.$_POST['new_email'].'" size="30" maxlength="50" ></td><tr></tr><td class="LabelV" ><span >Password:</span></td>  <td><input type="password" name="password" size="30" maxlength="29" ></td></tr>          </table>        </div>  </table></div></td></tr><br/><table style="width:100%;" ><tr align="center"><td><table border="0" cellspacing="0" cellpadding="0" ><tr><td style="border:0px;" ><input type="hidden" name=changeemailsave value=1 ><div class="BigButton" style="background-image:url('.$template_path.'/images/buttons/sbutton.gif)" ><div onMouseOver="MouseOverBigButton(this);" onMouseOut="MouseOutBigButton(this);" ><div class="BigButtonOver" style="background-image:url('.$template_path.'/images/buttons/sbutton_over.gif);" ></div><input class="ButtonText" type="image" name="Submit" alt="Submit" src="'.$template_path.'/images/buttons/_sbutton_submit.gif" ></div></div></td><tr></form></table></td><td><table border="0" cellspacing="0" cellpadding="0" ><form action="?subtopic=accountmanagement" method="post" ><tr><td style="border:0px;" ><div class="BigButton" style="background-image:url('.$template_path.'/images/buttons/sbutton.gif)" ><div onMouseOver="MouseOverBigButton(this);" onMouseOut="MouseOutBigButton(this);" ><div class="BigButtonOver" style="background-image:url('.$template_path.'/images/buttons/sbutton_over.gif);" ></div><input class="ButtonText" type="image" name="Back" alt="Back" src="'.$template_path.'/images/buttons/_sbutton_back.gif" ></div></div></td></tr></form></table></td></tr></table>';
		}
	}
	else
	{
	echo 'Please enter your password and the new email address. Make sure that you enter a valid email address which you have access to. <b>For security reasons, the actual change will be finalised after a waiting period of '.$config['account_mail_change'].' days.</b><br/><br/><form action="?subtopic=accountmanagement&action=changeemail" method="post" ><div class="TableContainer" >  <table class="Table1" cellpadding="0" cellspacing="0" >    <div class="CaptionContainer" >      <div class="CaptionInnerContainer" >        <span class="CaptionEdgeLeftTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionEdgeRightTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionBorderTop" style="background-image:url('.$template_path.'/images/content/table-headline-border.gif);" ></span>        <span class="CaptionVerticalLeft" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></span>        <div class="Text" >Change Email Address</div>        <span class="CaptionVerticalRight" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></span>        <span class="CaptionBorderBottom" style="background-image:url('.$template_path.'/images/content/table-headline-border.gif);" ></span>        <span class="CaptionEdgeLeftBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionEdgeRightBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>      </div>    </div>    <tr>      <td>        <div class="InnerTableContainer" >          <table style="width:100%;" ></tr><td class="LabelV" ><span >New Email Address:</span></td>  <td style="width:90%;" ><input name="new_email" value="'.(isset($_POST['new_email']) ? $_POST['new_email'] : '').'" size="30" maxlength="50" ></td><tr></tr><td class="LabelV" ><span >Password:</span></td>  <td><input type="password" name="password" size="30" maxlength="29" ></td></tr>          </table>        </div>  </table></div></td></tr><br/><table style="width:100%;" ><tr align="center"><td><table border="0" cellspacing="0" cellpadding="0" ><tr><td style="border:0px;" ><input type="hidden" name=changeemailsave value=1 ><div class="BigButton" style="background-image:url('.$template_path.'/images/buttons/sbutton.gif)" ><div onMouseOver="MouseOverBigButton(this);" onMouseOut="MouseOutBigButton(this);" ><div class="BigButtonOver" style="background-image:url('.$template_path.'/images/buttons/sbutton_over.gif);" ></div><input class="ButtonText" type="image" name="Submit" alt="Submit" src="'.$template_path.'/images/buttons/_sbutton_submit.gif" ></div></div></td><tr></form></table></td><td><table border="0" cellspacing="0" cellpadding="0" ><form action="?subtopic=accountmanagement" method="post" ><tr><td style="border:0px;" ><div class="BigButton" style="background-image:url('.$template_path.'/images/buttons/sbutton.gif)" ><div onMouseOver="MouseOverBigButton(this);" onMouseOut="MouseOutBigButton(this);" ><div class="BigButtonOver" style="background-image:url('.$template_path.'/images/buttons/sbutton_over.gif);" ></div><input class="ButtonText" type="image" name="Back" alt="Back" src="'.$template_path.'/images/buttons/_sbutton_back.gif" ></div></div></td></tr></form></table></td></tr></table>';
	}
	}
	else
	{
		if($account_email_new_time < time()) {
			if($_POST['changeemailsave'] == 1) {
				$account_logged->setCustomField("email_new", "");
				$account_logged->setCustomField("email_new_time", 0);
				$account_logged->setEmail($account_email_new);
				$account_logged->save();
				$account_logged->logAction('Account email changed to <b>' . $account_email_new . '</b>');
				echo '<div class="TableContainer" >  <table class="Table1" cellpadding="0" cellspacing="0" >    <div class="CaptionContainer" >      <div class="CaptionInnerContainer" >        <span class="CaptionEdgeLeftTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionEdgeRightTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionBorderTop" style="background-image:url('.$template_path.'/images/content/table-headline-border.gif);" ></span>        <span class="CaptionVerticalLeft" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></span>        <div class="Text" >Email Address Change Accepted</div>        <span class="CaptionVerticalRight" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></span>        <span class="CaptionBorderBottom" style="background-image:url('.$template_path.'/images/content/table-headline-border.gif);" ></span>        <span class="CaptionEdgeLeftBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionEdgeRightBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>      </div>    </div>    <tr>      <td>        <div class="InnerTableContainer" >          <table style="width:100%;" ><tr><td>You have accepted <b>'.$account_logged->getEmail().'</b> as your new email adress.</td></tr>          </table>        </div>  </table></div></td></tr><br/><center><table border="0" cellspacing="0" cellpadding="0" ><form action="?subtopic=accountmanagement" method="post" ><tr><td style="border:0px;" ><div class="BigButton" style="background-image:url('.$template_path.'/images/buttons/sbutton.gif)" ><div onMouseOver="MouseOverBigButton(this);" onMouseOut="MouseOutBigButton(this);" ><div class="BigButtonOver" style="background-image:url('.$template_path.'/images/buttons/sbutton_over.gif);" ></div><input class="ButtonText" type="image" name="Back" alt="Back" src="'.$template_path.'/images/buttons/_sbutton_back.gif" ></div></div></td></tr></form></table></center>';
			}
			else
			{
				echo '<div class="TableContainer" >  <table class="Table1" cellpadding="0" cellspacing="0" >    <div class="CaptionContainer" >      <div class="CaptionInnerContainer" >        <span class="CaptionEdgeLeftTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionEdgeRightTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionBorderTop" style="background-image:url('.$template_path.'/images/content/table-headline-border.gif);" ></span>        <span class="CaptionVerticalLeft" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></span>        <div class="Text" >Email Address Change Accepted</div>        <span class="CaptionVerticalRight" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></span>        <span class="CaptionBorderBottom" style="background-image:url('.$template_path.'/images/content/table-headline-border.gif);" ></span>        <span class="CaptionEdgeLeftBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionEdgeRightBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>      </div>    </div>    <tr>      <td>        <div class="InnerTableContainer" >          <table style="width:100%;" ><tr><td>Do you accept <b>'.$account_email_new.'</b> as your new email adress?</td></tr>          </table>        </div>  </table></div></td></tr><br /><table width="100%"><tr><td width="30">&nbsp;</td><td align=left><form action="?subtopic=accountmanagement&action=changeemail" method="post"><input type="hidden" name="changeemailsave" value=1 ><INPUT TYPE=image NAME="I Agree" SRC="'.$template_path.'/images/buttons/sbutton_iagree.gif" BORDER=0 WIDTH=120 HEIGHT=17></FORM></td><td align=left><form action="?subtopic=accountmanagement&action=changeemail" method="post"><input type="hidden" name="emailchangecancel" value=1 ><input type=image name="Cancel" src="'.$template_path.'/images/buttons/sbutton_cancel.gif" BORDER=0 WIDTH=120 HEIGHT=17></form></td><td align=right><form action="?subtopic=accountmanagement" method="post" ><div class="BigButton" style="background-image:url('.$template_path.'/images/buttons/sbutton.gif)" ><div onMouseOver="MouseOverBigButton(this);" onMouseOut="MouseOutBigButton(this);" ><div class="BigButtonOver" style="background-image:url('.$template_path.'/images/buttons/sbutton_over.gif);" ></div><input class="ButtonText" type="image" name="Back" alt="Back" src="'.$template_path.'/images/buttons/_sbutton_back.gif" ></div></div></form></td><td width="30">&nbsp;</td></tr></table>';
			}
		}
		else
		{
			echo '<div class="TableContainer" >  <table class="Table1" cellpadding="0" cellspacing="0" >    <div class="CaptionContainer" >      <div class="CaptionInnerContainer" >        <span class="CaptionEdgeLeftTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionEdgeRightTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionBorderTop" style="background-image:url('.$template_path.'/images/content/table-headline-border.gif);" ></span>        <span class="CaptionVerticalLeft" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></span>        <div class="Text" >Change of Email Address</div>        <span class="CaptionVerticalRight" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></span>        <span class="CaptionBorderBottom" style="background-image:url('.$template_path.'/images/content/table-headline-border.gif);" ></span>        <span class="CaptionEdgeLeftBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionEdgeRightBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>      </div>    </div>    <tr>      <td>        <div class="InnerTableContainer" >          <table style="width:100%;" ><tr><td>A request has been submitted to change the email address of this account to <b>'.$account_email_new.'</b>.<br/>The actual change will take place on <b>'.date("j F Y, G:i:s", $account_email_new_time).'</b>.<br>If you do not want to change your email address, please click on "Cancel".</td></tr>          </table>        </div>  </table></div></td></tr><br/><table style="width:100%;" ><tr align="center"><td><table border="0" cellspacing="0" cellpadding="0" ><form action="?subtopic=accountmanagement&action=changeemail" method="post" ><tr><td style="border:0px;" ><input type="hidden" name="emailchangecancel" value=1 ><div class="BigButton" style="background-image:url('.$template_path.'/images/buttons/sbutton.gif)" ><div onMouseOver="MouseOverBigButton(this);" onMouseOut="MouseOutBigButton(this);" ><div class="BigButtonOver" style="background-image:url('.$template_path.'/images/buttons/sbutton_over.gif);" ></div><input class="ButtonText" type="image" name="Cancel" alt="Cancel" src="'.$template_path.'/images/buttons/_sbutton_cancel.gif" ></div></div></td></tr></form></table></td><td><table border="0" cellspacing="0" cellpadding="0" ><form action="?subtopic=accountmanagement" method="post" ><tr><td style="border:0px;" ><div class="BigButton" style="background-image:url('.$template_path.'/images/buttons/sbutton.gif)" ><div onMouseOver="MouseOverBigButton(this);" onMouseOut="MouseOutBigButton(this);" ><div class="BigButtonOver" style="background-image:url('.$template_path.'/images/buttons/sbutton_over.gif);" ></div><input class="ButtonText" type="image" name="Back" alt="Back" src="'.$template_path.'/images/buttons/_sbutton_back.gif" ></div></div></td></tr></form></table></td></tr></table>';
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
		$new_rlname = isset($_POST['info_rlname']) ? htmlspecialchars(stripslashes($_POST['info_rlname'])) : NULL;
		$new_location = isset($_POST['info_location']) ? htmlspecialchars(stripslashes($_POST['info_location'])) : NULL;
		$new_country = isset($_POST['info_country']) ? htmlspecialchars(stripslashes($_POST['info_country'])) : NULL;
		if(isset($_POST['changeinfosave']) && $_POST['changeinfosave'] == 1) {
		//save data from form
			$account_logged->setCustomField("rlname", $new_rlname);
			$account_logged->setCustomField("location", $new_location);
			$account_logged->setCustomField("country", $new_country);
			$account_logged->logAction('Changed Real Name to ' . $new_rlname . ', Location to ' . $new_location . ' and Country to ' . $config['countries'][$new_country] . '.');
			echo '<div class="TableContainer" >  <table class="Table1" cellpadding="0" cellspacing="0" >    <div class="CaptionContainer" >      <div class="CaptionInnerContainer" >        <span class="CaptionEdgeLeftTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionEdgeRightTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionBorderTop" style="background-image:url('.$template_path.'/images/content/table-headline-border.gif);" ></span>        <span class="CaptionVerticalLeft" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></span>        <div class="Text" >Public Information Changed</div>        <span class="CaptionVerticalRight" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></span>        <span class="CaptionBorderBottom" style="background-image:url('.$template_path.'/images/content/table-headline-border.gif);" ></span>        <span class="CaptionEdgeLeftBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionEdgeRightBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>      </div>    </div>    <tr>      <td>        <div class="InnerTableContainer" >          <table style="width:100%;" ><tr><td>Your public information has been changed.</td></tr>          </table>        </div>  </table></div></td></tr><br><center><table border="0" cellspacing="0" cellpadding="0" ><form action="?subtopic=accountmanagement" method="post" ><tr><td style="border:0px;" ><div class="BigButton" style="background-image:url('.$template_path.'/images/buttons/sbutton.gif)" ><div onMouseOver="MouseOverBigButton(this);" onMouseOut="MouseOutBigButton(this);" ><div class="BigButtonOver" style="background-image:url('.$template_path.'/images/buttons/sbutton_over.gif);" ></div><input class="ButtonText" type="image" name="Back" alt="Back" src="'.$template_path.'/images/buttons/_sbutton_back.gif" ></div></div></td></tr></form></table></center>';
		}
		else
		{
		//show form
			$account_rlname = $account_logged->getCustomField("rlname");
			$account_location = $account_logged->getCustomField("location");
			if($config['account_country'])
				$account_country = $account_logged->getCustomField("country");
			?>
			Here you can tell other players about yourself. This information will be displayed alongside the data of your characters. If you do not want to fill in a certain field, just leave it blank.<br/><br/>
			<form action="?subtopic=accountmanagement&action=changeinfo" method=post>
			<div class="TableContainer" >
				<table class="Table1" cellpadding="0" cellspacing="0" >
					<div class="CaptionContainer" >
						<div class="CaptionInnerContainer" >
							<span class="CaptionEdgeLeftTop" style="background-image:url(<?php echo $template_path; ?>/images/content/box-frame-edge.gif);" /></span>
							<span class="CaptionEdgeRightTop" style="background-image:url(<?php echo $template_path; ?>/images/content/box-frame-edge.gif);" /></span>
							<span class="CaptionBorderTop" style="background-image:url(<?php echo $template_path; ?>/images/content/table-headline-border.gif);" ></span>
							<span class="CaptionVerticalLeft" style="background-image:url(<?php echo $template_path; ?>/images/content/box-frame-vertical.gif);" /></span>
							<div class="Text" >Change Public Information</div>
							<span class="CaptionVerticalRight" style="background-image:url(<?php echo $template_path; ?>/images/content/box-frame-vertical.gif);" /></span>
							<span class="CaptionBorderBottom" style="background-image:url(<?php echo $template_path; ?>/images/content/table-headline-border.gif);" ></span>
							<span class="CaptionEdgeLeftBottom" style="background-image:url(<?php echo $template_path; ?>/images/content/box-frame-edge.gif);" /></span>
							<span class="CaptionEdgeRightBottom" style="background-image:url(<?php echo $template_path; ?>/images/content/box-frame-edge.gif);" /></span>
						</div>
			</div>
				<tr>
					<td>
						<div class="InnerTableContainer" >
						<table style="width:100%;" >
							<tr>
								<td class="LabelV" >Real Name:</td>
								<td style="width:90%;" >
									<input name="info_rlname" value="<?php echo $account_rlname; ?>" size="30" maxlength="50" >
								</td>
						</tr>
						<tr>
							<td class="LabelV" >Location:</td>
							<td>
								<input name="info_location" value="<?php echo $account_location; ?>" size="30" maxlength="50" >
							</td>
						</tr>
						<?php if($config['account_country']): ?>
						<tr>
							<td class="LabelV" >Country:</td>
							<td>
								<select name="info_country" id="account_country">
									<?php
										foreach(array('pl', 'se', 'br', 'us', 'gb', ) as $country)
											echo '<option value="' . $country . '">' . $config['countries'][$country] . '</option>';

										echo '<option value="">----------</option>';
										foreach($config['countries'] as $code => $country)
											echo '<option value="' . $code . '"' . ($account_country == $code ? ' selected' : '') . '>' . $country . '</option>';
									?>
								</select>
								<img src="" id="account_country_img"/>
								<script>
									function updateFlag()
									{
										var img = $('#account_country_img');
										var country = $('#account_country :selected').val();
										if(country.length) {
											img.attr('src', 'images/flags/' + country + '.gif');
											img.show();
										}
										else {
											img.hide();
										}
									}

									$(function() {
										updateFlag();
										$('#account_country').change(function() {
											updateFlag();
										});
									});
								</script>
							</td>
						</tr>
						<?php endif; ?>
					</table>
				</div>  </table></div></td></tr><br/>
				<table width="100%">
				<tr align="center"><td><table border="0" cellspacing="0" cellpadding="0" ><tr>
				<td style="border:0px;" >
				<input type="hidden" name="changeinfosave" value="1" >
				<div class="BigButton" style="background-image:url(<?php echo $template_path; ?>/images/buttons/sbutton.gif)" >
				<div onMouseOver="MouseOverBigButton(this);" onMouseOut="MouseOutBigButton(this);" >
				<div class="BigButtonOver" style="background-image:url(<?php echo $template_path; ?>/images/buttons/sbutton_over.gif);" ></div>
				<input class="ButtonText" type="image" name="Submit" alt="Submit" src="<?php echo $template_path; ?>/images/buttons/_sbutton_submit.gif" ></div></div>
				</td><tr></form></table></td><td><table border="0" cellspacing="0" cellpadding="0" >
				<form action="?subtopic=accountmanagement" method="post" >
				<tr><td style="border:0px;" ><div class="BigButton" style="background-image:url(<?php echo $template_path; ?>/images/buttons/sbutton.gif)" >
				<div onMouseOver="MouseOverBigButton(this);" onMouseOut="MouseOutBigButton(this);" >
				<div class="BigButtonOver" style="background-image:url(<?php echo $template_path; ?>/images/buttons/sbutton_over.gif);" ></div>
				<input class="ButtonText" type="image" name="Back" alt="Back" src="<?php echo $template_path; ?>/images/buttons/_sbutton_back.gif" ></div></div></td></tr></form></table></td></tr></table>
	<?php
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
			//show errors if not empty
			if(!empty($reg_errors))
			{
				echo '<div class="SmallBox" >  <div class="MessageContainer" >    <div class="BoxFrameHorizontal" style="background-image:url('.$template_path.'/images/content/box-frame-horizontal.gif);" /></div>    <div class="BoxFrameEdgeLeftTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="BoxFrameEdgeRightTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="ErrorMessage" >      <div class="BoxFrameVerticalLeft" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></div>      <div class="BoxFrameVerticalRight" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></div>      <div class="AttentionSign" style="background-image:url('.$template_path.'/images/content/attentionsign.gif);" /></div><b>The Following Errors Have Occurred:</b><br/>';
				foreach($reg_errors as $reg_error)
					echo '<li>'.$reg_error . '</li>';
				echo '</div>    <div class="BoxFrameHorizontal" style="background-image:url('.$template_path.'/images/content/box-frame-horizontal.gif);" /></div>    <div class="BoxFrameEdgeRightBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="BoxFrameEdgeLeftBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>  </div></div><br/>';
			}
			//show form
			echo 'To generate recovery key for your account please enter your password.<br/><br/><form action="?subtopic=accountmanagement&action=registeraccount" method="post" ><input type="hidden" name="registeraccountsave" value="1"><div class="TableContainer" >  <table class="Table1" cellpadding="0" cellspacing="0" >    <div class="CaptionContainer" >      <div class="CaptionInnerContainer" >        <span class="CaptionEdgeLeftTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionEdgeRightTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionBorderTop" style="background-image:url('.$template_path.'/images/content/table-headline-border.gif);" ></span>        <span class="CaptionVerticalLeft" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></span>        <div class="Text" >Generate recovery key</div>        <span class="CaptionVerticalRight" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></span>        <span class="CaptionBorderBottom" style="background-image:url('.$template_path.'/images/content/table-headline-border.gif);" ></span>        <span class="CaptionEdgeLeftBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionEdgeRightBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>      </div>    </div>    <tr>      <td>        <div class="InnerTableContainer" >          <table style="width:100%;" ><tr><td class="LabelV" ><span >Password:</td><td><input type="password" name="reg_password" size="30" maxlength="29" ></td></tr>          </table>        </div>  </table></div></td></tr><br/><table style="width:100%" ><tr align="center" ><td><table border="0" cellspacing="0" cellpadding="0" ><tr><td style="border:0px;" ><div class="BigButton" style="background-image:url('.$template_path.'/images/buttons/sbutton.gif)" ><div onMouseOver="MouseOverBigButton(this);" onMouseOut="MouseOutBigButton(this);" ><div class="BigButtonOver" style="background-image:url('.$template_path.'/images/buttons/sbutton_over.gif);" ></div><input class="ButtonText" type="image" name="Submit" alt="Submit" src="'.$template_path.'/images/buttons/_sbutton_submit.gif" ></div></div></td><tr></form></table></td><td><table border="0" cellspacing="0" cellpadding="0" ><form action="?subtopic=accountmanagement" method="post" ><tr><td style="border:0px;" ><div class="BigButton" style="background-image:url('.$template_path.'/images/buttons/sbutton.gif)" ><div onMouseOver="MouseOverBigButton(this);" onMouseOut="MouseOutBigButton(this);" ><div class="BigButtonOver" style="background-image:url('.$template_path.'/images/buttons/sbutton_over.gif);" ></div><input class="ButtonText" type="image" name="Back" alt="Back" src="'.$template_path.'/images/buttons/_sbutton_back.gif" ></div></div></td></tr></form></table></td></tr></table>';
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
				if(!empty($reg_errors))
				{
					echo '<div class="SmallBox" >  <div class="MessageContainer" >    <div class="BoxFrameHorizontal" style="background-image:url('.$template_path.'/images/content/box-frame-horizontal.gif);" /></div>    <div class="BoxFrameEdgeLeftTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="BoxFrameEdgeRightTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="ErrorMessage" >      <div class="BoxFrameVerticalLeft" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></div>      <div class="BoxFrameVerticalRight" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></div>      <div class="AttentionSign" style="background-image:url('.$template_path.'/images/content/attentionsign.gif);" /></div><b>The Following Errors Have Occurred:</b><br/>';
					foreach($reg_errors as $reg_error)
						echo '<li>'.$reg_error;
					echo '</div>    <div class="BoxFrameHorizontal" style="background-image:url('.$template_path.'/images/content/box-frame-horizontal.gif);" /></div>    <div class="BoxFrameEdgeRightBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="BoxFrameEdgeLeftBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>  </div></div><br/>';
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
						echo 'Here you can see and edit the information about your character.<br/>If you do not want to specify a certain field, just leave it blank.<br/><br/><form action="?subtopic=accountmanagement&action=changecomment" method="post" ><div class="TableContainer" >  <table class="Table5" cellpadding="0" cellspacing="0" >    <div class="CaptionContainer" >      <div class="CaptionInnerContainer" >        <span class="CaptionEdgeLeftTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionEdgeRightTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionBorderTop" style="background-image:url('.$template_path.'/images/content/table-headline-border.gif);" ></span>        <span class="CaptionVerticalLeft" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></span>        <div class="Text" >Edit Character Information</div>        <span class="CaptionVerticalRight" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></span>        <span class="CaptionBorderBottom" style="background-image:url('.$template_path.'/images/content/table-headline-border.gif);" ></span>        <span class="CaptionEdgeLeftBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionEdgeRightBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>      </div>    </div>    <tr>      <td>        <div class="InnerTableContainer" >          <table style="width:100%;" ><tr><td><div class="TableShadowContainerRightTop" >  <div class="TableShadowRightTop" style="background-image:url('.$template_path.'/images/content/table-shadow-rt.gif);" ></div></div><div class="TableContentAndRightShadow" style="background-image:url('.$template_path.'/images/content/table-shadow-rm.gif);" >  <div class="TableContentContainer" >    <table class="TableContent" width="100%" ><tr><td class="LabelV" >Name:</td><td style="width:80%;" >'.$player_name.'</td></tr><tr><td class="LabelV" >Hide Account:</td><td>';
						if($player->getCustomField("hidden") == 1) {
							echo '<input type="checkbox" name="accountvisible"  value="1" checked="checked">';
						}
						else
						{
							echo '<input type="checkbox" name="accountvisible"  value="1" >';
						}

						echo ' check to hide your account information';
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

//### DELETE character from account ###
	if($action == "changename") {
		echo '<script type="text/javascript" src="tools/check_name.js"></script>';
		
		$name_changed = false;
		$player_id = isset($_POST['player_id']) ? $_POST['player_id'] : NULL;
		$newcharname = isset($_POST['newcharname']) ? stripslashes(ucwords(strtolower($_POST['newcharname']))) : NULL;
		if((!$config['account_change_character_name']))
			echo 'You cant change your character name';
		else
		{
			$points = $account_logged->getCustomField('premium_points');
			if(isset($_POST['changenamesave']) && $_POST['changenamesave'] == 1) {
				if($points < $config['account_change_character_name_points'])
					$errors[] = 'You need ' . $config['account_change_character_name_points'] . ' premium points to change name. You have <b>'.$points.'<b> premium points.';
	
				if(empty($errors) && empty($newcharname))
					$errors[] = 'Please enter a name for your character!';

				if(empty($errors) && strlen($newcharname) > 25)
					$errors[] = 'Name is too long. Max. lenght <b>25</b> letters.';
				else if(empty($errors) && strlen($newcharname) < 3)
					$errors[] = 'Name is too short. Min. lenght <b>25</b> letters.';
				
				if(empty($errors))
				{
					$error = '';
					if(!admin() && !check_name_new_char($newcharname, $error))
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
								$player->setName($newcharname);
								$player->save();
								$account_logged->setCustomField("premium_points", $points - $config['account_change_character_name_points']);
								$account_logged->logAction('Changed name from <b>' . $old_name . '</b> to <b>' . $player->getName() . '</b>.');
								echo '<div class="TableContainer" >  <table class="Table1" cellpadding="0" cellspacing="0" >    <div class="CaptionContainer" >      <div class="CaptionInnerContainer" >        <span class="CaptionEdgeLeftTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionEdgeRightTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionBorderTop" style="background-image:url('.$template_path.'/images/content/table-headline-border.gif);" ></span>        <span class="CaptionVerticalLeft" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></span>        <div class="Text" >Character Name Changed</div>        <span class="CaptionVerticalRight" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></span>        <span class="CaptionBorderBottom" style="background-image:url('.$template_path.'/images/content/table-headline-border.gif);" ></span>        <span class="CaptionEdgeLeftBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionEdgeRightBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>      </div>    </div>    <tr>      <td>        <div class="InnerTableContainer" >          <table style="width:100%;" ><tr><td>The character <b>'.$old_name.'</b> name has been changed to <b>' . $player->getName() . '</b>.</td></tr>          </table>        </div>  </table></div></td></tr><br><center><table border="0" cellspacing="0" cellpadding="0" ><form action="?subtopic=accountmanagement" method="post" ><tr><td style="border:0px;" ><div class="BigButton" style="background-image:url('.$template_path.'/images/buttons/sbutton.gif)" ><div onMouseOver="MouseOverBigButton(this);" onMouseOut="MouseOutBigButton(this);" ><div class="BigButtonOver" style="background-image:url('.$template_path.'/images/buttons/sbutton_over.gif);" ></div><input class="ButtonText" type="image" name="Back" alt="Back" src="'.$template_path.'/images/buttons/_sbutton_back.gif" ></div></div></td></tr></form></table></center>';
							}
							else
							{
								$errors[] = 'Character <b>'.$player_name.'</b> is not on your account.';
							}
						}
					}
					else
					{
						$errors[] = 'Character with this name doesn\'t exist.';
					}
				}
			}

			if(!$name_changed) {
				if(!empty($errors)) {
					echo '<div class="SmallBox" >  <div class="MessageContainer" >    <div class="BoxFrameHorizontal" style="background-image:url('.$template_path.'/images/content/box-frame-horizontal.gif);" /></div>    <div class="BoxFrameEdgeLeftTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="BoxFrameEdgeRightTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="ErrorMessage" >      <div class="BoxFrameVerticalLeft" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></div>      <div class="BoxFrameVerticalRight" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></div>      <div class="AttentionSign" style="background-image:url('.$template_path.'/images/content/attentionsign.gif);" /></div><b>The Following Errors Have Occurred:</b><br/>';
					foreach($errors as $errors) {
							echo '<li>'.$errors;
					}
					echo '</div>    <div class="BoxFrameHorizontal" style="background-image:url('.$template_path.'/images/content/box-frame-horizontal.gif);" /></div>    <div class="BoxFrameEdgeRightBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="BoxFrameEdgeLeftBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>  </div></div><br/>';
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
							<input type="text" name="newcharname" id="newcharname" onkeyup="checkName();" size="25" maxlength="25" >
							<font size="1" face="verdana,arial,helvetica">
								<div id="name_check">Please enter your character name.</div>
							</font>
						</td>
					</tr>
				</table>        </div>  </table></div></td></tr><br/><table style="width:100%" ><tr align="center" ><td><table border="0" cellspacing="0" cellpadding="0" ><tr><td style="border:0px;" ><div class="BigButton" style="background-image:url('.$template_path.'/images/buttons/sbutton.gif)" ><div onMouseOver="MouseOverBigButton(this);" onMouseOut="MouseOutBigButton(this);" ><div class="BigButtonOver" style="background-image:url('.$template_path.'/images/buttons/sbutton_over.gif);" ></div><input class="ButtonText" type="image" name="Submit" alt="Submit" src="'.$template_path.'/images/buttons/_sbutton_submit.gif" ></div></div></td><tr></form></table></td><td><table border="0" cellspacing="0" cellpadding="0" ><form action="?subtopic=accountmanagement" method="post" ><tr><td style="border:0px;" ><div class="BigButton" style="background-image:url('.$template_path.'/images/buttons/sbutton.gif)" ><div onMouseOver="MouseOverBigButton(this);" onMouseOut="MouseOutBigButton(this);" ><div class="BigButtonOver" style="background-image:url('.$template_path.'/images/buttons/sbutton_over.gif);" ></div><input class="ButtonText" type="image" name="Back" alt="Back" src="'.$template_path.'/images/buttons/_sbutton_back.gif" ></div></div></td></tr></form></table></td></tr></table>';
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
					$player = $ots->createObject('Player');
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
								echo '<div class="TableContainer" >  <table class="Table1" cellpadding="0" cellspacing="0" >    <div class="CaptionContainer" >      <div class="CaptionInnerContainer" >        <span class="CaptionEdgeLeftTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionEdgeRightTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionBorderTop" style="background-image:url('.$template_path.'/images/content/table-headline-border.gif);" ></span>        <span class="CaptionVerticalLeft" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></span>        <div class="Text" >Character Deleted</div>        <span class="CaptionVerticalRight" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></span>        <span class="CaptionBorderBottom" style="background-image:url('.$template_path.'/images/content/table-headline-border.gif);" ></span>        <span class="CaptionEdgeLeftBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionEdgeRightBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>      </div>    </div>    <tr>      <td>        <div class="InnerTableContainer" >          <table style="width:100%;" ><tr><td>The character <b>'.$player_name.'</b> has been deleted.</td></tr>          </table>        </div>  </table></div></td></tr><br><center><table border="0" cellspacing="0" cellpadding="0" ><form action="?subtopic=accountmanagement" method="post" ><tr><td style="border:0px;" ><div class="BigButton" style="background-image:url('.$template_path.'/images/buttons/sbutton.gif)" ><div onMouseOver="MouseOverBigButton(this);" onMouseOut="MouseOutBigButton(this);" ><div class="BigButtonOver" style="background-image:url('.$template_path.'/images/buttons/sbutton_over.gif);" ></div><input class="ButtonText" type="image" name="Back" alt="Back" src="'.$template_path.'/images/buttons/_sbutton_back.gif" ></div></div></td></tr></form></table></center>';
								}
								else
									$delete_errors[] = 'This character is online.';
							}
							else
							{
								$delete_errors[] = 'Wrong password to account.';
							}
						}
						else
						{
							$delete_errors[] = 'Character <b>'.$player_name.'</b> is not on your account.';
						}
					}
					else
					{
						$delete_errors[] = 'Character with this name doesn\'t exist.';
					}
				}
				else
				{
					$delete_errors[] = 'Name contain illegal characters.';
				}
			}
			else
			{
			$delete_errors[] = 'Character name or/and password is empty. Please fill in form.';
			}
		}
		if(!$dontshowtableagain) {
			if(!empty($delete_errors)) {
				echo '<div class="SmallBox" >  <div class="MessageContainer" >    <div class="BoxFrameHorizontal" style="background-image:url('.$template_path.'/images/content/box-frame-horizontal.gif);" /></div>    <div class="BoxFrameEdgeLeftTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="BoxFrameEdgeRightTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="ErrorMessage" >      <div class="BoxFrameVerticalLeft" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></div>      <div class="BoxFrameVerticalRight" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></div>      <div class="AttentionSign" style="background-image:url('.$template_path.'/images/content/attentionsign.gif);" /></div><b>The Following Errors Have Occurred:</b><br/>';
				foreach($delete_errors as $delete_error) {
						echo '<li>'.$delete_error;
				}
				echo '</div>    <div class="BoxFrameHorizontal" style="background-image:url('.$template_path.'/images/content/box-frame-horizontal.gif);" /></div>    <div class="BoxFrameEdgeRightBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="BoxFrameEdgeLeftBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>  </div></div><br/>';
			}
			echo 'To delete a character enter the name of the character and your password.<br/><br/><form action="?subtopic=accountmanagement&action=deletecharacter" method="post" ><input type="hidden" name="deletecharactersave" value="1"><div class="TableContainer" >  <table class="Table1" cellpadding="0" cellspacing="0" >    <div class="CaptionContainer" >      <div class="CaptionInnerContainer" >        <span class="CaptionEdgeLeftTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionEdgeRightTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionBorderTop" style="background-image:url('.$template_path.'/images/content/table-headline-border.gif);" ></span>        <span class="CaptionVerticalLeft" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></span>        <div class="Text" >Delete Character</div>        <span class="CaptionVerticalRight" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></span>        <span class="CaptionBorderBottom" style="background-image:url('.$template_path.'/images/content/table-headline-border.gif);" ></span>        <span class="CaptionEdgeLeftBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionEdgeRightBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>      </div>    </div>    <tr>      <td>        <div class="InnerTableContainer" >          <table style="width:100%;" ><tr><td class="LabelV" ><span >Character Name:</td><td style="width:90%;" ><input name="delete_name" value="" size="30" maxlength="29" ></td></tr><tr><td class="LabelV" ><span >Password:</td><td><input type="password" name="delete_password" size="30" maxlength="29" ></td></tr>          </table>        </div>  </table></div></td></tr><br/><table style="width:100%" ><tr align="center" ><td><table border="0" cellspacing="0" cellpadding="0" ><tr><td style="border:0px;" ><div class="BigButton" style="background-image:url('.$template_path.'/images/buttons/sbutton.gif)" ><div onMouseOver="MouseOverBigButton(this);" onMouseOut="MouseOutBigButton(this);" ><div class="BigButtonOver" style="background-image:url('.$template_path.'/images/buttons/sbutton_over.gif);" ></div><input class="ButtonText" type="image" name="Submit" alt="Submit" src="'.$template_path.'/images/buttons/_sbutton_submit.gif" ></div></div></td><tr></form></table></td><td><table border="0" cellspacing="0" cellpadding="0" ><form action="?subtopic=accountmanagement" method="post" ><tr><td style="border:0px;" ><div class="BigButton" style="background-image:url('.$template_path.'/images/buttons/sbutton.gif)" ><div onMouseOver="MouseOverBigButton(this);" onMouseOut="MouseOutBigButton(this);" ><div class="BigButtonOver" style="background-image:url('.$template_path.'/images/buttons/sbutton_over.gif);" ></div><input class="ButtonText" type="image" name="Back" alt="Back" src="'.$template_path.'/images/buttons/_sbutton_back.gif" ></div></div></td></tr></form></table></td></tr></table>';
		}
	}

//## CREATE CHARACTER on account ###
	if($action == "createcharacter") {
		echo '<script type="text/javascript" src="tools/check_name.js"></script>';
		$newchar_name = isset($_POST['newcharname']) ? stripslashes(ucwords(strtolower($_POST['newcharname']))) : NULL;
		$newchar_sex = isset($_POST['newcharsex']) ? $_POST['newcharsex'] : NULL;
		$newchar_vocation = isset($_POST['newcharvocation']) ? $_POST['newcharvocation'] : NULL;
		$newchar_town = isset($_POST['newchartown']) ? $_POST['newchartown'] : NULL;
		$newchar_errors = array();
		
		$newchar_created = false;
		if(isset($_POST['savecharacter']) && $_POST['savecharacter'] == 1) {
			if(empty($newchar_name))
				$newchar_errors[] = 'Please enter a name for your character!';

			if(strlen($newchar_name) > 25)
				$newchar_errors[] = 'Name is too long. Max. lenght <b>25</b> letters.';
			else if(strlen($newchar_name) < 3)
				$newchar_errors[] = 'Name is too short. Min. lenght <b>25</b> letters.';

			if(empty($newchar_sex) && $newchar_sex != "0")
				$newchar_errors[] = 'Please select the sex for your character!';

			if(count($config['character_samples']) > 1)
			{
				if(!isset($newchar_vocation))
					$newchar_errors[] = 'Please select a vocation for your character.';
			}
			else
				$newchar_vocation = $config['character_samples'][0];

			if(count($config['character_towns']) > 1)
			{
				if(empty($newchar_town))
					$newchar_errors[] = 'Please select a town for your character.';
			}
			else
				$newchar_town = $config['character_towns'][0];

			if(empty($newchar_errors))
			{
				$error = '';
				if(!admin() && !check_name_new_char($newchar_name, $error))
					$newchar_errors[] = $error;
				if($newchar_sex != 1 && $newchar_sex != "0")
					$newchar_errors[] = 'Sex must be equal <b>0 (female)</b> or <b>1 (male)</b>.';
				if(!in_array($newchar_town, $config['character_towns']))
					$newchar_errors[] = 'Please select valid town.';
				if(count($config['character_samples']) > 1)
				{
					$newchar_vocation_check = false;
					foreach($config['character_samples'] as $char_vocation_key => $sample_char)
						if($newchar_vocation == $char_vocation_key)
							$newchar_vocation_check = true;
					if(!$newchar_vocation_check)
						$newchar_errors[] = 'Unknown vocation. Please fill in form again.';
				}
				else
					$newchar_vocation = 0;
			}

			if(empty($newchar_errors))
			{
				$number_of_players_on_account = $account_logged->getPlayersList()->count();
				if($number_of_players_on_account >= $config['characters_per_account'])
					$newchar_errors[] .= 'You have too many characters on your account <b>('.$number_of_players_on_account.'/'.$config['characters_per_account'].')</b>!';
			}

			if(empty($newchar_errors))
			{
				$char_to_copy_name = $config['character_samples'][$newchar_vocation];
				$char_to_copy = new OTS_Player();
				$char_to_copy->find($char_to_copy_name);
				if(!$char_to_copy->isLoaded())
					$newchar_errors[] .= 'Wrong characters configuration. Try again or contact with admin. ADMIN: Edit file config/config.php and set valid characters to copy names. Character to copy'.$char_to_copy_name.'</b> doesn\'t exist.';
			}

			if(empty($newchar_errors))
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
				$player = $ots->createObject('Player');
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
					echo '<div class="TableContainer" >  <table class="Table1" cellpadding="0" cellspacing="0" >    <div class="CaptionContainer" >      <div class="CaptionInnerContainer" >        <span class="CaptionEdgeLeftTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionEdgeRightTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionBorderTop" style="background-image:url('.$template_path.'/images/content/table-headline-border.gif);" ></span>        <span class="CaptionVerticalLeft" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></span>        <div class="Text" >Character Created</div>        <span class="CaptionVerticalRight" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></span>        <span class="CaptionBorderBottom" style="background-image:url('.$template_path.'/images/content/table-headline-border.gif);" ></span>        <span class="CaptionEdgeLeftBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionEdgeRightBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>      </div>    </div>    <tr>      <td>        <div class="InnerTableContainer" >          <table style="width:100%;" ><tr><td>The character <b>'.$newchar_name.'</b> has been created.<br/>Please select the outfit when you log in for the first time.<br/><br/><b>See you on '.$config['lua']['serverName'].'!</b></td></tr>          </table>        </div>  </table></div></td></tr><br/><center><table border="0" cellspacing="0" cellpadding="0" ><form action="?subtopic=accountmanagement" method="post" ><tr><td style="border:0px;" ><div class="BigButton" style="background-image:url('.$template_path.'/images/buttons/sbutton.gif)" ><div onMouseOver="MouseOverBigButton(this);" onMouseOut="MouseOutBigButton(this);" ><div class="BigButtonOver" style="background-image:url('.$template_path.'/images/buttons/sbutton_over.gif);" ></div><input class="ButtonText" type="image" name="Back" alt="Back" src="'.$template_path.'/images/buttons/_sbutton_back.gif" ></div></div></td></tr></form></table></center>';
				}
				else
				{
					echo "Error. Can\'t create character. Probably problem with database. Try again or contact with admin.";
					exit;
				}
			}
		}

		if(count($newchar_errors) > 0) {
			echo '<div class="SmallBox" >  <div class="MessageContainer" >    <div class="BoxFrameHorizontal" style="background-image:url('.$template_path.'/images/content/box-frame-horizontal.gif);" /></div>    <div class="BoxFrameEdgeLeftTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="BoxFrameEdgeRightTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="ErrorMessage" >      <div class="BoxFrameVerticalLeft" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></div>      <div class="BoxFrameVerticalRight" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></div>      <div class="AttentionSign" style="background-image:url('.$template_path.'/images/content/attentionsign.gif);" /></div>';
			echo '<b>The Following Errors Have Occurred:</b><br/>';
			foreach($newchar_errors as $newchar_error)
				echo '<li>'.$newchar_error . '</li>';
			echo '</div>    <div class="BoxFrameHorizontal" style="background-image:url('.$template_path.'/images/content/box-frame-horizontal.gif);" /></div>    <div class="BoxFrameEdgeRightBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="BoxFrameEdgeLeftBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>  </div></div><br/>';
		}
		
		if(!$newchar_created) {
			echo 'Please choose a name';
			if(count($config['character_samples']) > 1)
				echo ', vocation';
			echo ' and sex for your character. <br/>In any case the name must not violate the naming conventions stated in the <a href="?subtopic=rules" target="_blank" >'.$config['lua']['serverName'].' Rules</a>, or your character might get deleted or name locked.';
			if($account_logged->getPlayersList()->count() >= $config['characters_per_account']) {
				echo '<b><font color="red"> You have maximum number of characters per account on your account. Delete one before you make new.</font></b>';
			}
			echo '<br/><br/><form action="?subtopic=accountmanagement&action=createcharacter" method="post" ><input type="hidden" name=savecharacter value="1" ><div class="TableContainer" >  <table class="Table3" cellpadding="0" cellspacing="0" >    <div class="CaptionContainer" >      <div class="CaptionInnerContainer" ><span class="CaptionEdgeLeftTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span><span class="CaptionEdgeRightTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span><span class="CaptionBorderTop" style="background-image:url('.$template_path.'/images/content/table-headline-border.gif);" ></span><span class="CaptionVerticalLeft" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></span><div class="Text" >Create Character</div>        <span class="CaptionVerticalRight" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></span><span class="CaptionBorderBottom" style="background-image:url('.$template_path.'/images/content/table-headline-border.gif);" ></span><span class="CaptionEdgeLeftBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span><span class="CaptionEdgeRightBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span></div>    </div><tr>      <td>        <div class="InnerTableContainer" >          <table style="width:100%;" ><tr><td><div class="TableShadowContainerRightTop" >  <div class="TableShadowRightTop" style="background-image:url('.$template_path.'/images/content/table-shadow-rt.gif);" ></div></div><div class="TableContentAndRightShadow" style="background-image:url('.$template_path.'/images/content/table-shadow-rm.gif);" >  <div class="TableContentContainer" ><table class="TableContent" width="100%" ><tr class="LabelH" ><td style="width:50%;" ><span >Name</td><td><span >Sex</td></tr><tr class="Odd" >
			<td>
				<input id="newcharname" name="newcharname" onkeyup="checkName();" value="'.$newchar_name.'" size="25" maxlength="25" ><br/>
				<font size="1" face="verdana,arial,helvetica">
					<div id="name_check">Please enter your character name.</div>
				</font>
			</td>
			<td>';
			echo '<input type="radio" name="newcharsex" id="newcharsex" value="1" ';
			if($newchar_sex == 1)
				echo 'checked="checked" ';
			echo '><label for="newcharsex">male</label><br/>';
			echo '<input type="radio" name="newcharsex" id="newcharsex2" value="0" ';
			if($newchar_sex == "0")
				echo 'checked="checked" ';
			echo '><label for="newcharsex2">female</label><br/></td></tr></table></div></div></table></div>';
			echo '<div class="InnerTableContainer" >          <table style="width:100%;" ><tr>';
			if(count($config['character_samples']) > 1)
			{
				echo '<td><table class="TableContent" width="100%" ><tr class="Odd" valign="top"><td width="160"><br /><b>Select your vocation:</b></td><td><table class="TableContent" width="100%" >';
				foreach($config['character_samples'] as $char_vocation_key => $sample_char)
				{
					echo '<tr><td><input type="radio" name="newcharvocation" id="newcharvocation' . $char_vocation_key . '" value="'.$char_vocation_key.'" ';
					if($newchar_vocation == $char_vocation_key)
						echo 'checked="checked" ';
					echo '><label for="newcharvocation' . $char_vocation_key . '">'.$config['vocations'][$char_vocation_key].'</label></td></tr>';
				}
				echo '</table></td></table>';
			}
			if(count($config['character_towns']) > 1)
			{
				echo '<td><table class="TableContent" width="100%" ><tr class="Odd" valign="top"><td width="160"><br /><b>Select your city:</b></td><td><table class="TableContent" width="100%" >';
				foreach($config['character_towns'] as $town_id)
				{
					echo '<tr><td><input type="radio" name="newchartown" id="newchartown' . $town_id . '" value="' . $town_id . '" ';
					if($newchar_town == $town_id)
						echo 'checked="checked" ';
					echo '><label for="newchartown' . $town_id . '">'.$config['towns'][$town_id].'</label></td></tr>';
				}
				echo '</table></td></tr></table></table></div>';
			}
			else
				echo '</tr></table></div>';
			echo '</table></div></td></tr><br/><table style="width:100%;" ><tr align="center" ><td><table border="0" cellspacing="0" cellpadding="0" ><tr><td style="border:0px;" ><div class="BigButton" style="background-image:url('.$template_path.'/images/buttons/sbutton.gif)" ><div onMouseOver="MouseOverBigButton(this);" onMouseOut="MouseOutBigButton(this);" ><div class="BigButtonOver" style="background-image:url('.$template_path.'/images/buttons/sbutton_over.gif);" ></div><input class="ButtonText" type="image" name="Submit" alt="Submit" src="'.$template_path.'/images/buttons/_sbutton_submit.gif" ></div></div></td><tr></form></table></td><td><table border="0" cellspacing="0" cellpadding="0" ><form action="?subtopic=accountmanagement" method="post" ><tr><td style="border:0px;" ><div class="BigButton" style="background-image:url('.$template_path.'/images/buttons/sbutton.gif)" ><div onMouseOver="MouseOverBigButton(this);" onMouseOut="MouseOutBigButton(this);" ><div class="BigButtonOver" style="background-image:url('.$template_path.'/images/buttons/sbutton_over.gif);" ></div><input class="ButtonText" type="image" name="Back" alt="Back" src="'.$template_path.'/images/buttons/_sbutton_back.gif" ></div></div></td></tr></form></table></td></tr></table>';
		}
	}
?>
