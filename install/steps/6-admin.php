<?php
defined('MYAAC') or die('Direct access not allowed!');

require BASE . 'install/includes/config.php';
if(!$error) {
	require BASE . 'install/includes/database.php';

	if(isset($database_error)) { // we failed connect to the database
		error($database_error);
	}

	$account = 'account';
	if(!USE_ACCOUNT_NAME) {
		$account = 'account_id';
	}

	$twig->display('install.admin.html.twig', array(
		'locale' => $locale,
		'session' => $_SESSION,
		'account' => $account,
		'hasTablePlayers' => $db->hasTable('players'),
		'errors' => isset($errors) ? $errors : null,
		'buttons' => next_buttons(true, !$error)
	));
}
else {
?>
	<div class="text-center m-3">
		<form action="<?php echo BASE_URL; ?>install/" method="post">
			<input type="hidden" name="step" id="step" value="admin" />
			<?= next_buttons(true, false);?>
		</form>
	</div>
<?php
}
