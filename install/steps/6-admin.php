<?php
defined('MYAAC') or die('Direct access not allowed!');

require(BASE . 'install/includes/config.php');
if(!$error) {
	require(BASE . 'install/includes/database.php');
	
	if(isset($database_error)) { // we failed connect to the database
		error($database_error);
	}

	echo $twig->render('install.admin.html.twig', array(
		'locale' => $locale,
		'session' => $_SESSION,
		'errors' => isset($errors) ? $errors : null,
		'buttons' => next_buttons(true, $error ? false : true)
	));
}
?>