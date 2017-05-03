<?php
/**
 * Mailer
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2017 MyAAC
 * @version   0.0.3
 * @link      http://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');
$title = 'Mailer';

if(!hasFlag(FLAG_CONTENT_MAILER) && !superAdmin())
{
	echo 'Access denied.';
	return;
}

if(!$config['mail_enabled'])
{
	echo 'Mail support disabled.';
	return;
}

$mail_content = isset($_POST['mail_content']) ? stripslashes($_POST['mail_content']) : NULL;
$mail_subject = isset($_POST['mail_subject']) ? stripslashes($_POST['mail_subject']) : NULL;
$preview = isset($_REQUEST['preview']);

$preview_done = false;
if($preview) {
	if(!empty($mail_content) && !empty($mail_subject))
		$preview_done = _mail($account_logged->getCustomField('email'), $mail_subject, $mail_content);
	
	if(!$preview_done)
		error('Error while sending preview mail: ' . $mailer->ErrorInfo);
}

?>
<script type="text/javascript">
tinyMCE.init({
	mode : "textareas",
	theme : "advanced",
	plugins: "safari,advimage,emotions,insertdatetime,preview,wordcount",

	relative_urls : false,
	remove_script_host : false,
	document_base_url : "<?php echo BASE_URL; ?>",

	theme_advanced_buttons3_add : "emotions,insertdate,inserttime,preview,|,forecolor,backcolor",

	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	theme_advanced_statusbar_location : "bottom",
	theme_advanced_resizing : true,
});
</script>
<table width="800" cellspacing="1" cellpadding="2" border="0" align="center">
	<form method="post">
		<tr>
			<td colspan="2" align="center">
				<p class="note note-image" style="width: 80%;">Sending mails may take some time if there are much users in db.</p>
			</td>
		</tr>
		<tr>
			<td align="right">
				<label for="mail_subject">Subject:</label>
			</td>
			<td align="left">
				<input type="text" id="mail_subject" name="mail_subject" value="<?php echo $mail_subject; ?>" size="30" maxlength="30" />
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<textarea id="mail_content" name="mail_content" style="width: 100%" class="tinymce"><?php echo $mail_content; ?></textarea>
			</td>
		</tr>
		<tr>
			<td colspan="2" align="center">
				<input type="checkbox" name="preview" id="preview" value="1"/><label for="preview">Just send test email to me (preview)</label><?php echo ($preview_done ? ' - <b>Done.</b>' : ''); ?><br/><input type="submit" name="submit" value="Send" />
			</td>
		</tr>
	</form>
</table>
<?php
	if(empty($mail_content) || empty($mail_subject) || $preview)
		return;

	$success = 0;
	$failed = 0;

	$add = '';
	if($config['account_mail_verify'])
		$add = ' AND ' . $db->fieldName('email_verified') . ' = 1';

	$query = $db->query('SELECT ' . $db->fieldName('email') . ' FROM ' . $db->tableName('accounts') . ' WHERE ' . $db->fieldName('email') . ' != ""' . $add);
	foreach($query  as $email)
	{
		if(_mail($email['email'], $mail_subject, $mail_content))
			$success++;
		else
		{
			$failed++;
			echo '<br />';
			error('An error occorred while sending email to <b>' . $email['email'] . '</b>. Error: ' . $mailer->ErrorInfo);
		}
	}
?>
	Mailing finished.<br/>
	<p class="success"><?php echo $success; ?> emails delivered.</p><br/>
	<p class="warning"><?php echo $failed; ?> emails failed.</p></br>
