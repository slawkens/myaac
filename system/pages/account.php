<?php
/**
 * Account confirm mail
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2017 MyAAC
 * @version   0.2.0
 * @link      http://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');
$title = 'Account';

if($action == 'confirm_email')
{
	$res = $db->query('SELECT email_hash FROM accounts WHERE email_hash = ' . $db->quote($_GET['v']));
	if(!$res->rowCount())
		echo '<div class="note">Your email couldn\'t be verified. Please contact staff to do it manually.</div>';
	else
	{
		$db->update('accounts', array('email_verified' => '1'), array('email_hash' => $_GET['v']));
		echo '<div class="success">You have now verified your e-mail, this will increase the security of your account. Thank you for doing this.</div>';
	}
}
?>
