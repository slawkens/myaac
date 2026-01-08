<?php

use MyAAC\Settings;

defined('MYAAC') or die('Direct access not allowed!');

//ini_set('display_errors', false);
ini_set('max_execution_time', 300);
$error = false;

if(!isset($_SESSION['var_server_path'])) {
	error($locale['step_database_error_path']);
	$error = true;
}

if(!$error) {
	$configToSave = [
		// by default, set env to prod
		// user can disable when he wants
		'env' => 'prod',
	];

	foreach($_SESSION as $key => $value)
	{
		if(strpos($key, 'var_') !== false)
		{
			if($key === 'var_server_path')
			{
				$value = str_replace("\\", "/", $value);
				if($value[strlen($value) - 1] !== '/')
					$value .= '/';
			}

			if(!in_array($key, ['var_usage', 'var_date_timezone', 'var_client', 'var_account', 'var_account_id', 'var_password', 'var_password_confirm', 'var_step', 'var_email', 'var_player_name'], true)) {
				$configToSave[str_replace('var_', '', $key)] = $value;
			}
		}
	}

	$configToSave['gzip_output'] = false;
	$configToSave['cache_engine'] = 'auto';
	$configToSave['cache_prefix'] = 'myaac_' . generateRandomString(8, true, false, true);
	$configToSave['database_auto_migrate'] = true;

	$content = '';
	$saved = Settings::saveConfig($configToSave, CONFIG_DIR . 'config.local.php', $content);
	if ($saved || file_exists(CONFIG_DIR . 'config.local.php')) {
		success($locale['step_database_config_saved']);
		$_SESSION['saved'] = true;

		require CONFIG_DIR . 'config.local.php';
		require BASE . 'install/includes/config.php';

		if (!$error) {
			require BASE . 'install/includes/database.php';

			if (isset($database_error)) { // we failed connect to the database
				error($database_error);
			}
			else {
				if (!$db->hasTable('accounts')) {
					$tmp = str_replace('$TABLE$', 'accounts', $locale['step_database_error_table']);
					error($tmp);
					$error = true;
				}

				if (!$error) {
					$twig->display('install.installer.html.twig', array(
						'url' => 'tools/5-database.php',
						'message' => $locale['loading_spinner']
					));
				}
			}
		}
	} else {
		$error = true;
		$_SESSION['config_content'] = $content;
		unset($_SESSION['saved']);

		$locale['step_database_error_file'] = str_replace('$FILE$', '<b>' . BASE . 'config.local.php</b>', $locale['step_database_error_file']);
		error($locale['step_database_error_file'] . '<br/>
			<textarea cols="70" rows="10">' . $content . '</textarea>');
	}
}
?>

<div class="text-center m-3">
	<form action="<?php echo BASE_URL; ?>install/" method="post">
		<input type="hidden" name="step" id="step" value="admin" />
		<?php echo next_buttons(true, !$error);
		?>
	</form>
</div>
