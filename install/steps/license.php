<?php
defined('MYAAC') or die('Direct access not allowed!');

echo $twig->render('install.license.html.twig', array(
	'license' => file_get_contents(BASE . 'LICENSE'),
	'buttons' => next_buttons()
));
?>
