<?php

use MyAAC\Cache\Cache;
use MyAAC\Models\News;
use MyAAC\Settings;

defined('MYAAC') or die('Direct access not allowed!');

ini_set('max_execution_time', 300);
if(isset($config['installed']) && $config['installed'] && !isset($_SESSION['saved'])) {
	warning($locale['already_installed']);
	return;
}

$cache = Cache::getInstance();
if ($cache->enabled()) {
	// clear plugin_hooks to have fresh hooks
	$cache->delete('plugins_hooks');
}

require SYSTEM . 'init.php';
if($error) {
	return;
}

if(USE_ACCOUNT_NAME || USE_ACCOUNT_NUMBER)
	$account = $_SESSION['var_account'] ?? null;
else
	$account_id = $_SESSION['var_account_id'] ?? null;

$password = $_SESSION['var_password'];

if(USE_ACCOUNT_SALT)
{
	$salt = generateRandomString(10, false, true, true);
	$password = $salt . $password;
}

$account_db = new OTS_Account();
if(isset($account))
	$account_db->find($account);
else
	$account_db->load($account_id);

if ($db->hasTable('players')) {
	$player_name = $_SESSION['var_player_name'];
	$player_db = new OTS_Player();
	$player_db->find($player_name);

	if(!$player_db->isLoaded())
	{
		$player = new OTS_Player();
		$player->setName($player_name);

		$player_used = &$player;
	}
	else {
		$player_used = &$player_db;
	}

	$groups = new OTS_Groups_List();
	$player_used->setGroupId($groups->getHighestId());
}

$email = $_SESSION['var_email'];
if($account_db->isLoaded()) {
	$account_db->setPassword(encrypt($password));
	$account_db->setEMail($email);
	$account_db->save();

	$account_used = &$account_db;
}
else {
	$new_account = new OTS_Account();
	if(USE_ACCOUNT_NAME) {
		$new_account->create($account);
	}
	else {
		$new_account->create(null, $account_id);
	}

	$new_account->setPassword(encrypt($password));
	$new_account->setEMail($email);

	$new_account->save();

	$new_account->setCustomField('created', time());
	$new_account->logAction('Account created.');

	$account_used = &$new_account;
}

if(USE_ACCOUNT_SALT)
	$account_used->setCustomField('salt', $salt);

$account_used->setCustomField('web_flags', FLAG_ADMIN + FLAG_SUPER_ADMIN);
$account_used->setCustomField('country', 'us');
$account_used->setCustomField('email_verified', 1);

if($db->hasColumn('accounts', 'group_id'))
	$account_used->setCustomField('group_id', $groups->getHighestId());
if($db->hasColumn('accounts', 'type'))
	$account_used->setCustomField('type', 6);

if ($db->hasTable('players')) {
	if(!$player_db->isLoaded()) {
		$player->setAccountId($account_used->getId());
		$player->save();
	}
	else {
		$player_db->setAccountId($account_used->getId());
		$player_db->save();
	}
}

success($locale['step_database_created_account']);

setSession('account', $account_used->getId());
setSession('password', encrypt($password));
setSession('remember_me', true);

if(!News::all()->count()) {
	$player_id = 0;

	if ($db->hasTable('players')) {
		$tmpNewsPlayer = \MyAAC\Models\Player::where('name', $player_name)->first();
		if($tmpNewsPlayer) {
			$player_id = $tmpNewsPlayer->id;
		}
	}

	News::create([
		'type' => 1,
		'date' => time(),
		'category' => 2,
		'title' => 'Hello!',
		'body' => 'MyAAC is just READY to use!',
		'player_id' => $player_id,
		'comments' => 'https://my-aac.org',
		'hide' => 0,
	]);

	News::create([
		'type' => 2,
		'date' => time(),
		'category' => 4,
		'title' => 'Hello tickers!',
		'body' => 'https://my-aac.org',
		'player_id' => $player_id,
		'comments' => '',
		'hide' => 0,
	]);

	success($locale['step_database_created_news']);
}

$settings = Settings::getInstance();
foreach($_SESSION as $key => $value) {
	if (in_array($key, ['var_usage', 'var_date_timezone', 'var_client'])) {
		if ($key == 'var_usage') {
			$key = 'anonymous_usage_statistics';
			$value = ((int)$value == 1 ? 'true' : 'false');
		} elseif ($key == 'var_date_timezone') {
			$key = 'date_timezone';
		} elseif ($key == 'var_client') {
			$key = 'client';
		}

		$settings->updateInDatabase('core', $key, $value);
	}
}
success('Settings saved.');

$twig->display('install.installer.html.twig', array(
	'url' => 'tools/7-finish.php',
	'message' => $locale['importing_spinner']
));

if(!isset($_SESSION['installed'])) {
	if (!array_key_exists('CI', getenv())) {
		$report_url = 'https://my-aac.org/report_install.php?v=' . MYAAC_VERSION . '&b=' . urlencode(BASE_URL);
		if (function_exists('curl_version'))
		{
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $report_url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_exec($curl);
			curl_close($curl);
		}
		else if (ini_get('allow_url_fopen') ) {
			file_get_contents($report_url);
		}
	}

	$_SESSION['installed'] = true;
}

foreach($_SESSION as $key => $value) {
	if(strpos($key, 'var_') !== false)
		unset($_SESSION[$key]);
}
unset($_SESSION['saved']);
if(file_exists(CACHE . 'install.txt')) {
	unlink(CACHE . 'install.txt');
}

$hooks->trigger(HOOK_INSTALL_FINISH_END);
