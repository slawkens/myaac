<?php
/**
 * Change info
 *
 * @package   MyAAC
 * @author    Gesior <jerzyskalski@wp.pl>
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2019 MyAAC
 * @link      https://my-aac.org
 */

use MyAAC\Models\Account;

defined('MYAAC') or die('Direct access not allowed!');

$title = 'Change Info';
require __DIR__ . '/base.php';

if(!$logged) {
	return;
}

if($config['account_country'])
	require SYSTEM . 'countries.conf.php';

$account = Account::find($account_logged->getId());

$show_form = true;
$new_rlname = isset($_POST['info_rlname']) ? htmlspecialchars(stripslashes($_POST['info_rlname'])) : NULL;
$new_location = isset($_POST['info_location']) ? htmlspecialchars(stripslashes($_POST['info_location'])) : NULL;
$new_country = isset($_POST['info_country']) ? htmlspecialchars(stripslashes($_POST['info_country'])) : NULL;
if(isset($_POST['changeinfosave']) && $_POST['changeinfosave'] == 1) {
	if(!isset($config['countries'][$new_country]))
		$errors[] = 'Country is not correct.';

	if(empty($errors)) {
		//save data from form
		$account->rlname = $new_rlname;
		$account->location = $new_location;
		$account->country = $new_country;
		$account->save();
		$account_logged->logAction('Changed Real Name to <b>' . $new_rlname . '</b>, Location to <b>' . $new_location . '</b> and Country to <b>' . $config['countries'][$new_country] . '</b>.');
		$twig->display('success.html.twig', array(
			'title' => 'Public Information Changed',
			'description' => 'Your public information has been changed.'
		));
		$show_form = false;
	}
	else {
		$twig->display('error_box.html.twig', array('errors' => $errors));
	}
}

//show form
if($show_form) {
	$account_rlname = $account->rlname;
	$account_location = $account->location;
	if ($config['account_country']) {
		$account_country = $account->country;

		$countries = array();
		foreach (array('pl', 'se', 'br', 'us', 'gb',) as $country)
			$countries[$country] = $config['countries'][$country];

		$countries['--'] = '----------';

		foreach ($config['countries'] as $code => $country)
			$countries[$code] = $country;
	}

	$twig->display('account.change_info.html.twig', array(
		'countries' => $countries ?? [],
		'account_rlname' => $account_rlname,
		'account_location' => $account_location,
		'account_country' => $account_country ?? ''
	));
}
?>
