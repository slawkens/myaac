<?php
/**
 * Create account
 *
 * @package   MyAAC
 * @author    Gesior <jerzyskalski@wp.pl>
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2017 MyAAC
 * @version   0.4.2
 * @link      http://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');
$title = 'Create Account';

if($config['account_country'])
	require(SYSTEM . 'countries.conf.php');

if($logged)
{
	echo 'Please logout before attempting to create a new account.';
	return;
}

$errors = array();
$step = isset($_POST['step']) ? $_POST['step'] : '';
if($step == 'save')
{
	if(USE_ACCOUNT_NAME) {
		$account_name = $_POST['account'];
		$account_name_up = strtoupper($account_name);
	}
	else
		$account_id = $_POST['account'];

	$email = $_POST['email'];
	$password = $_POST['password'];
	$password2 = $_POST['password2'];

	// account
	if(isset($account_id)) {
		if(empty($account_id))
			$errors['account'] = 'Please enter your account number!';
		else if(!check_number($account_id))
			$errors['account'] = 'Invalid account number format. Please use only numbers 0-9.';
	}
	else {
		if(empty($account_name))
			$errors['account'] = 'Please enter your account name!';
		else if(!check_account_name($account_name_up))
			$errors['account'] = 'Invalid account name format. Please use only A-Z and numbers 0-9.';
	}

	// email
	if(empty($email))
		$errors['email'] = 'Please enter your email address!';
	else if(!check_mail($email))
		$errors['email'] = 'Email address is not correct.';

	// country
	$country = '';
	if($config['account_country'])
	{
		$country = $_POST['country'];
		if(!isset($country))
			$errors['country'] = 'Country is not set.';
		elseif(!isset($config['countries'][$country]))
			$errors['country'] = 'Country is invalid.';
	}

	if($config['recaptcha_enabled'])
	{
		if(isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response']))
		{
			$verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$config['recaptcha_secret_key'].'&response='.$_POST['g-recaptcha-response']);
			$responseData = json_decode($verifyResponse);
			if(!$responseData->success)
				$errors['verification'] = "Please confirm that you're not a robot.";
		}
		else
			$errors['verification'] = "Please confirm that you're not a robot.";
	}

	// password
	if(empty($password))
		$errors['password'] = 'Please enter the password for your new account.';
	elseif($password != $password2)
		$errors['password'] = 'Passwords are not the same.';
	else
	{
		if(!check_password($password))
			$errors['password'] = 'Password contains illegal chars (a-z, A-Z and 0-9 only!). Minimum password length is 7 characters and maximum 32.';
	}

	// check if account name is not equal to password
	if(USE_ACCOUNT_NAME && $account_name_up == strtoupper($password))
	{
		$errors['password'] = 'Password may not be the same as account name.';
	}

	if(empty($errors))
	{
		if($config['account_mail_unique'])
		{
			$test_email_account = $ots->createObject('Account');
			$test_email_account->findByEmail($email);
			if($test_email_account->isLoaded())
				$errors['email'] = 'Account with this e-mail address already exist.';
		}

		$account_db = new OTS_Account();
		if(USE_ACCOUNT_NAME)
			$account_db->find($account_name);
		else
			$account_db->load($account_id);

		if($account_db->isLoaded()) {
			if(USE_ACCOUNT_NAME)
				$errors['account'] = 'Account with this name already exist.';
			else
				$errors['account'] = 'Account with this id already exist.';
		}
	}

	if(!isset($_POST['accept_rules']) || $_POST['accept_rules'] != 'true')
		$errors['accept_rules'] = 'You have to agree to the ' . $config['lua']['serverName'] . ' Rules in order to create an account!';

	if(empty($errors))
	{
		$new_account = $ots->createObject('Account');
		if(USE_ACCOUNT_NAME)
			$new_account->create($account_name);
		else
			$new_account->create(NULL, $account_id);

		$config_salt_enabled = fieldExist('salt', 'accounts');
		if($config_salt_enabled)
		{
			$salt = generateRandomString(10, false, true, true);
			$password = $salt . $password;
		}

		$new_account->setPassword(encrypt($password));
		$new_account->setEMail($email);
		$new_account->unblock();
		$new_account->save();
		
		if($config_salt_enabled)
			$new_account->setCustomField('salt', $salt);

		$new_account->setCustomField('created', time());
		$new_account->logAction('Account created.');

		if($config['account_country']) {
			$new_account->setCustomField('country', $country);
		}

		if($config['account_premium_days'] && $config['account_premium_days'] > 0) {
			$new_account->setCustomField('premdays', $config['account_premium_days']);
			$new_account->setCustomField('lastday', time());
		}

		if($config['account_premium_points']) {
			$new_account->setCustomField('premium_points', $config['account_premium_points']);
		}
	
		$tmp_account = (USE_ACCOUNT_NAME ? $account_name : $account_id);
		if($config['mail_enabled'] && $config['account_mail_verify'])
		{
			$hash = md5(generateRandomString(16, true, true) . $email);
			$new_account->setCustomField('email_hash', $hash);

			$verify_url = BASE_URL . '?p=account&action=confirm_email&v=' . $hash;
			$server_name = $config['lua']['serverName'];
			
			$body_plain = "Hello!

Thank you for registering on $server_name!

Here are the details of your account:
Account" . (USE_ACCOUNT_NAME ? ' Name' : '') . ": $tmp_account
Password: ************ (hidden for security reasons)

To verify your email address please click the link below:
$verify_url
If you haven't registered on $server_name please ignore this email.";

			$body_html = 'Hello!<br/>
<br/>
Thank you for registering on ' . $config['lua']['serverName'] . '!<br/>
<br/>
Here are the details of your account:<br/>
Account' . (USE_ACCOUNT_NAME ? ' Name' : '') . ': ' . $tmp_account . '<br/>
Password: ************ (hidden for security reasons)<br/>
<br/>
To verify your email address please click the link below:<br/>
' . generateLink($verify_url, $verify_url, true) . '<br/>
If you haven\'t registered on ' . $config['lua']['serverName'] . ' please ignore this email.';

			if(_mail($email, 'New account on ' . $config['lua']['serverName'], $body_html, $body_plain))
			{
?>
				Your account has been created.<br/><br/>
				<table width="100%" border="0" cellspacing="1" cellpadding="4">
					<tr><td bgcolor="<?php echo $config['vdarkborder']; ?>" class="white"><b>Account Created</b></td></tr>
					<tr><td bgcolor="<?php echo $config['darkborder']; ?>">
				  <table border="0" cellpadding="1"><tr><td>
				    <br/>Your account<?php echo (USE_ACCOUNT_NAME ? 'name' : 'number'); ?> is <b><?php echo $tmp_account; ?></b>.

				You will need the account <?php echo (USE_ACCOUNT_NAME ? 'name' : 'number'); ?> and your password to play on <?php echo $config['lua']['serverName']; ?>.
				    Please keep your account <?php echo (USE_ACCOUNT_NAME ? 'name' : 'number'); ?> and password in a safe place and
				    never give your account <?php echo (USE_ACCOUNT_NAME ? 'name' : 'number'); ?> or password to anybody.<br/><br/>
<?php
			}
			else
			{
				echo '<br /><p class="error">An error occorred while sending email! Account not created. Try again. Error:<br/>' . $mailer->ErrorInfo . '</p>';
				$new_account->delete();
			}
		}
		else
		{
			echo 'Your account has been created. Now you can login and create your first character. See you in Tibia!<br/><br/>';
			echo '<TABLE WIDTH=100% BORDER=0 CELLSPACING=1 CELLPADDING=4>
			<TR><TD BGCOLOR="'.$config['vdarkborder'].'" class="white"><B>Account Created</B></TD></TR>
			<TR><TD BGCOLOR="'.$config['darkborder'].'">
			  <TABLE BORDER=0 CELLPADDING=1><TR><TD>
			    <br/>Your account ' . (USE_ACCOUNT_NAME ? 'name' : 'number') . ' is <b>'.$tmp_account.'</b><br/>You will need the account ' . (USE_ACCOUNT_NAME ? 'name' : 'number') . ' and your password to play on '.$config['lua']['serverName'].'.
			    Please keep your account ' . (USE_ACCOUNT_NAME ? 'name' : 'number') . ' and password in a safe place and
			    never give your account ' . (USE_ACCOUNT_NAME ? 'name' : 'number') . ' or password to anybody.<br/><br/>';

			if($config['mail_enabled'] && $config['account_welcome_mail'])
			{
				$mailBody = '
					<h3>Dear player,</h3>
					<p>Thanks for your registration at <a href=" ' . BASE_URL . '"><b>' . $config['lua']['serverName'] . '</b></a></p>
					<br/><br/>
					Your login details:
					<p>Account' . (USE_ACCOUNT_NAME ? ' name' : '') . ': <b>' . $tmp_account . '</b></p>
					<p>Password: <b>' . str_repeat('*', strlen(trim($password))) . '</b> (hidden for security reasons)</p>
					<p>Kind Regards,</p>';

				if(_mail($email, 'Your account on ' . $config['lua']['serverName'], $mailBody))
					echo '<br /><small>These informations were send on email address <b>' . $email . '</b>.';
				else
					echo '<br /><p class="error">An error occorred while sending email (<b>' . $email . '</b>)! Error:<br/>' . $mailer->ErrorInfo . '</p>';
			}
		}
		echo '</TD></TR></TABLE></TD></TR></TABLE><br/><br/>';

		return;
	}
}

	$country_recognized = null;
	if($config['account_country_recognize']) {
		$info = json_decode(@file_get_contents('http://ipinfo.io/' . $_SERVER['REMOTE_ADDR'] . '/geo'), true);
		if(isset($info['country'])) {
			$country_recognized = strtolower($info['country']);
		}
	}

	if(!empty($errors))
		echo $twig->render('error_box.html.twig', array('errors' => $errors));

	if($config['account_country']) {
		$countries = array();
		foreach (array('pl', 'se', 'br', 'us', 'gb') as $c)
			$countries[$c] = $config['countries'][$c];
		
		$countries['--'] = '----------';
		foreach ($config['countries'] as $code => $c)
			$countries[$code] = $c;
	}

	echo $twig->render('account.create.js.html.twig');
	echo $twig->render('account.create.html.twig', array(
		'account' => isset($_POST['account']) ? $_POST['account'] : '',
		'email' => isset($_POST['email']) ? $_POST['email'] : '',
		'countries' => isset($countries) ? $countries : null,
		'accept_rules' => isset($_POST['accept_rules']) ? $_POST['accept_rules'] : false,
		'country_recognized' => $country_recognized,
		'country' => isset($country) ? $country : null,
		'errors' => $errors
	));
?>