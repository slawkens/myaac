<?php
/**
 * Create account
 *
 * @package   MyAAC
 * @author    Gesior <jerzyskalski@wp.pl>
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2019 MyAAC
 * @link      https://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');
$title = 'Create Account';

if (setting('core.account_country'))
	require SYSTEM . 'countries.conf.php';

if($logged)
{
	echo 'Please logout before attempting to create a new account.';
	return;
}

if(setting('core.account_create_character_create')) {
	require_once LIBS . 'CreateCharacter.php';
	$createCharacter = new CreateCharacter();
}

$account_type = 'number';
if (config('account_login_by_email')) {
	$account_type = 'Email Address';
}
else {
	if(USE_ACCOUNT_NAME) {
		$account_type = 'name';
	}
}

$errors = array();
$save = isset($_POST['save']) && $_POST['save'] == 1;
if($save)
{
	if(!config('account_login_by_email')) {
		if(USE_ACCOUNT_NAME) {
			$account_name = $_POST['account'];
		}
		else {
			$account_id = $_POST['account'];
		}
	}

	$email = $_POST['email'];
	$password = $_POST['password'];
	$password_confirm = $_POST['password_confirm'];

	// account
	if(!config('account_login_by_email')) {
		if (isset($account_id)) {
			if (!Validator::accountId($account_id)) {
				$errors['account'] = Validator::getLastError();
			}
		} else if (!Validator::accountName($account_name))
			$errors['account'] = Validator::getLastError();
	}

	// email
	if(!Validator::email($email))
		$errors['email'] = Validator::getLastError();

	// country
	$country = '';
	if (setting('core.account_country'))
	{
		$country = $_POST['country'];
		if(!isset($country))
			$errors['country'] = 'Country is not set.';
		elseif(!isset($config['countries'][$country]))
			$errors['country'] = 'Country is invalid.';
	}

	// password
	if(empty($password)) {
		$errors['password'] = 'Please enter the password for your new account.';
	}
	elseif($password != $password_confirm) {
		$errors['password'] = 'Passwords are not the same.';
	}
	else if(!Validator::password($password)) {
		$errors['password'] = Validator::getLastError();
	}

	// check if account name is not equal to password
	if(!config('account_login_by_email') && USE_ACCOUNT_NAME && strtoupper($account_name) == strtoupper($password)) {
		$errors['password'] = 'Password may not be the same as account name.';
	}

	if(setting('core.account_mail_unique'))
	{
		$test_email_account = new OTS_Account();
		$test_email_account->findByEMail($email);
		if($test_email_account->isLoaded())
			$errors['email'] = 'Account with this e-mail address already exist.';
	}

	$account_db = new OTS_Account();
	if (config('account_login_by_email')) {
		$account_db->findByEMail($email);
	}
	else {
		if(USE_ACCOUNT_NAME) {
			$account_db->find($account_name);
		}
		else {
			$account_db->load($account_id);
		}
	}

	if($account_db->isLoaded()) {
		if (config('account_login_by_email') && !setting('core.account_mail_unique')) {
			$errors['account'] = 'Account with this email already exist.';
		}
		else if (!config('account_login_by_email')) {
			if (USE_ACCOUNT_NAME)
				$errors['account'] = 'Account with this name already exist.';
			else
				$errors['account'] = 'Account with this id already exist.';
		}
	}

	if(!isset($_POST['accept_rules']) || $_POST['accept_rules'] !== 'true')
		$errors['accept_rules'] = 'You have to agree to the ' . $config['lua']['serverName'] . ' Rules in order to create an account!';

	$params = array(
		'account' => $account_db,
		'email' => $email,
		'country' => $country,
		'password' => $password,
		'password_confirm' => $password_confirm,
		'accept_rules' => isset($_POST['accept_rules']) ? $_POST['accept_rules'] === 'true' : false,
	);

	if (!config('account_login_by_email')) {
		if (USE_ACCOUNT_NAME) {
			$params['account_name'] = $_POST['account'];
		} else {
			$params['account_id'] = $_POST['account'];
		}
	}

	if (!$hooks->trigger(HOOK_ACCOUNT_CREATE_POST, $params)) {
		return;
	}

	if(setting('core.account_create_character_create')) {
		$character_name = isset($_POST['name']) ? stripslashes(ucwords(strtolower($_POST['name']))) : null;
		$character_sex = isset($_POST['sex']) ? (int)$_POST['sex'] : null;
		$character_vocation = isset($_POST['vocation']) ? (int)$_POST['vocation'] : null;
		$character_town = isset($_POST['town']) ? (int)$_POST['town'] : null;

		$createCharacter->check($character_name, $character_sex, $character_vocation, $character_town, $errors);
	}

	if(empty($errors))
	{
		$hasBeenCreatedByEMail = false;

		$new_account = new OTS_Account();
		if (config('account_login_by_email')) {
			$new_account->createWithEmail($email);
			$hasBeenCreatedByEMail = true;
		}
		else {
			if(USE_ACCOUNT_NAME)
				$new_account->create($account_name);
			else
				$new_account->create(NULL, $account_id);
		}

		if(USE_ACCOUNT_SALT)
		{
			$salt = generateRandomString(10, false, true, true);
			$password = $salt . $password;
		}

		$new_account->setPassword(encrypt($password));
		$new_account->setEMail($email);
		$new_account->save();

		if(USE_ACCOUNT_SALT)
			$new_account->setCustomField('salt', $salt);

		$new_account->setCustomField('created', time());
		$new_account->logAction('Account created.');

		if(setting('core.account_country')) {
			$new_account->setCustomField('country', $country);
		}

		$settingAccountPremiumDays = setting('core.account_premium_days');
		if($settingAccountPremiumDays && $settingAccountPremiumDays > 0) {
			if($db->hasColumn('accounts', 'premend')) { // othire
				$new_account->setCustomField('premend', time() + $settingAccountPremiumDays * 86400);
			}
			else { // rest
				if ($db->hasColumn('accounts', 'premium_ends_at')) { // TFS 1.4+
					$new_account->setCustomField('premium_ends_at', time() + $settingAccountPremiumDays * (60 * 60 * 24));
				}
				else {
					$new_account->setCustomField('premdays', $settingAccountPremiumDays);
					$new_account->setCustomField('lastday', time());
				}
			}
		}

		if(setting('core.account_premium_points') && setting('core.account_premium_points') > 0) {
			$new_account->setCustomField('premium_points', setting('core.account_premium_points'));
		}

		$tmp_account = $email;
		if (!config('account_login_by_email')) {
			$tmp_account = (USE_ACCOUNT_NAME ? $account_name : $account_id);
		}

		if(setting('core.mail_enabled') && setting('core.account_mail_verify'))
		{
			$hash = md5(generateRandomString(16, true, true) . $email);
			$new_account->setCustomField('email_hash', $hash);

			$verify_url = getLink('account/confirm_email/' . $hash);
			$body_html = $twig->render('mail.account.verify.html.twig', array(
				'account' => $tmp_account,
				'verify_url' => generateLink($verify_url, $verify_url, true)
			));

			if(_mail($email, 'New account on ' . $config['lua']['serverName'], $body_html))
			{
				echo 'Your account has been created.<br/><br/>';
				$twig->display('success.html.twig', array(
					'title' => 'Account Created',
					'description' => 'Your account ' . $account_type . ' is <b>' . $tmp_account . '</b><br/>You will need the account ' . $account_type . ' and your password to play on ' . configLua('serverName') . '.
						Please keep your account ' . $account_type . ' and password in a safe place and
						never give your account ' . $account_type . ' or password to anybody.',
					'custom_buttons' => setting('core.account_create_character_create') ? '' : null
				));
			}
			else
			{
				error('An error occorred while sending email! Account not created. Try again. For Admin: More info can be found in system/logs/mailer-error.log');
				$new_account->delete();
			}
		}
		else
		{
			if(setting('core.account_create_character_create')) {
				// character creation
				$character_created = $createCharacter->doCreate($character_name, $character_sex, $character_vocation, $character_town, $new_account, $errors);
				if (!$character_created) {
					error('There was an error creating your character. Please create your character later in account management page.');
					error(implode(' ', $errors));
				}
			}

			if(setting('core.account_create_auto_login')) {
				if ($hasBeenCreatedByEMail) {
					$_POST['account_login'] = $email;
				}
				else {
					$_POST['account_login'] = USE_ACCOUNT_NAME ? $account_name : $account_id;
				}

				$_POST['password_login'] = $password_confirm;

				require PAGES . 'account/login.php';
				header('Location: ' . getLink('account/manage'));
			}

			echo 'Your account';
			if(setting('core.account_create_character_create')) {
				echo ' and character have';
			}
			else {
				echo ' has';
			}

			echo ' been created.';
			if(!setting('core.account_create_character_create')) {
				echo ' Now you can login and create your first character.';
			}

			echo ' See you in Tibia!<br/><br/>';
			$twig->display('success.html.twig', array(
				'title' => 'Account Created',
				'description' => 'Your account ' . $account_type . ' is <b>' . $tmp_account . '</b><br/>You will need the account ' . $account_type . ' and your password to play on ' . configLua('serverName') . '.
						Please keep your account ' . $account_type . ' and password in a safe place and
						never give your account ' . $account_type . ' or password to anybody.',
				'custom_buttons' => setting('core.account_create_character_create') ? '' : null
			));

			if(setting('core.mail_enabled') && setting('core.account_welcome_mail'))
			{
				$mailBody = $twig->render('account.welcome_mail.html.twig', array(
					'account' => $tmp_account
				));

				if(_mail($email, 'Your account on ' . $config['lua']['serverName'], $mailBody))
					echo '<br /><small>These informations were send on email address <b>' . $email . '</b>.';
				else {
					error('An error occurred while sending email. For Admin: More info can be found in system/logs/mailer-error.log');
				}
			}
		}

		return;
	}
}

$country_recognized = null;
if(setting('core.account_country_recognize')) {
	$country_session = getSession('country');
	if($country_session !== false) { // get from session
		$country_recognized = $country_session;
	}
	else {
		$info = json_decode(@file_get_contents('http://ipinfo.io/' . $_SERVER['REMOTE_ADDR'] . '/geo'), true);
		if(isset($info['country'])) {
			$country_recognized = strtolower($info['country']);
			setSession('country', $country_recognized);
		}
	}
}

if(!empty($errors))
	$twig->display('error_box.html.twig', array('errors' => $errors));

if (setting('core.account_country')) {
	$countries = array();
	foreach (array('pl', 'se', 'br', 'us', 'gb') as $c)
		$countries[$c] = $config['countries'][$c];

	$countries['--'] = '----------';
	foreach ($config['countries'] as $code => $c)
		$countries[$code] = $c;
}

$twig->display('account.create.js.html.twig');

$params = array(
	'account' => isset($_POST['account']) ? $_POST['account'] : '',
	'email' => isset($_POST['email']) ? $_POST['email'] : '',
	'countries' => isset($countries) ? $countries : null,
	'accept_rules' => isset($_POST['accept_rules']) ? $_POST['accept_rules'] : false,
	'country_recognized' => $country_recognized,
	'country' => isset($country) ? $country : null,
	'errors' => $errors,
	'save' => $save
);

if($save && setting('core.account_create_character_create')) {
	$params = array_merge($params, array(
		'name' => $character_name,
		'sex' => $character_sex,
		'vocation' => $character_vocation,
		'town' => $character_town
	));
}

$twig->display('account.create.html.twig', $params);
