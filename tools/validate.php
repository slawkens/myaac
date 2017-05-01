<?php
/**
 * Ajax validator
 * Returns xml file with result
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2016 WodzAAC
 * @version   0.0.1
 * @link      http://myaac.info
 */

// we need some functions
require('../common.php');
require(LIBS . 'validator.php');
require(SYSTEM . 'functions.php');
require(SYSTEM . 'init.php');

echo '<?xml version="1.0" encoding="utf-8" standalone="yes"?>';
if(isset($_GET['account']))
{
	$account = trim($_GET['account']);
	$tmp = strtoupper($account);

	$error = '';
	if(!check_account_name($tmp, $error))
		error_($error);

	$_account = new OTS_Account();
	$_account->find($tmp);
	if($_account->isLoaded())
		error_('Account with this name already exist.');

	success_('Good account name ( ' . $account . ' ).');
}
else if(isset($_GET['email']))
{
	$email = trim($_GET['email']);
	if(strlen($email) >= 255)
		error_('E-mail is too long (max. 255 chars).');

	if(!Validator::email($email))
		error_('Invalid e-mail format.');

	if($config['account_mail_unique'])
	{
		$account = new OTS_Account();
		$account->findByEMail($email);
		if($account->isLoaded())
			error_('Account with this e-mail already exist.');
	}

	success_('Good e-mail.');
}
else if(isset($_GET['name']))
{
	$name = strtolower(stripslashes(trim($_GET['name'])));
	$error = '';
	if(!check_name($name, $error))
		error_($error);

	// check if this name wasn't namelocked
	if(tableExist('player_namelocks') && fieldExist('name', 'player_namelocks')) {
		$query = $db->query('SELECT `player_id` FROM `player_namelocks` WHERE name = ' . $db->quote($name));
		if($query->rowCount() > 0)
			error_('Character with this name has been namelocked.');
	}

	$player = new OTS_Player();
	$player->find($name);
	if($player->isLoaded())
		error_('Player with this name already exist.</b>');

	success_('Good. Your name will be:<br /><b>' . ucwords($name) . '</b>');
}
else
	error_('Error: no input specified.');

/**
 * Output message & exit.
 *
 * @param string $desc Description
 */
function success_($desc) {
	echo '<font color="green">' . $desc . '</font>';
	exit();
}
function error_($desc) {
	echo '<font color="red">' . $desc . '</font>';
	exit();
}

?>