<?php
defined('MYAAC') or die('Direct access not allowed!');

require(BASE . 'install/includes/config.php');
if(!$error) {
	require(BASE . 'install/includes/database.php');
	
	echo $twig->render('install.admin.html.twig', array(
		'locale' => $locale,
		'session' => $_SESSION,
		'buttons' => next_buttons(true, $error ? false : true)
	));
}
?>