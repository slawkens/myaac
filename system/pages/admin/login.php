<?php
/**
 * Login
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2017 MyAAC
 * @version   0.2.2
 * @link      http://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');
$title = 'Login';

if($action == 'logout')
	echo 'You have been logout.<br/>';

if(isset($errors)) {
	foreach($errors as $error) {
		error($error);
	}
}
?>

Please login.
<form method="post">
	<input type="password" name="account_login" id="account-name-input" size="30" maxlength="30" autofocus/><br/>
	<input type="password" name="password_login" size="30" maxlength="29"/><br/>
	<input type="checkbox" id="remember_me" name="remember_me" value="true"/>
	<label for="remember_me"> Remember me</label><br/>
	<input type="hidden" name="admin" value="1"/>
	<input type="submit" class="button" value="Login"/>
</form>