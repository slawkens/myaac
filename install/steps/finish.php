<?php
$locale['step_finish_desc'] = str_replace('$ADMIN_PANEL$', generateLink(ADMIN_URL, $locale['step_finish_admin_panel'], true), $locale['step_finish_desc']);
$locale['step_finish_desc'] = str_replace('$HOMEPAGE$', generateLink(BASE_URL, $locale['step_finish_homepage'], true), $locale['step_finish_desc']);
$locale['step_finish_desc'] = str_replace('$LINK$', generateLink('http://my-aac.org', 'http://my-aac.org', true), $locale['step_finish_desc']);
?>
<p class="success"><?php echo $locale['step_finish_desc']; ?></p>

<?php

if(!isset($_SESSION['installed'])) {
	file_get_contents('http://my-aac.org/report_install.php?v=' . MYAAC_VERSION);
	$_SESSION['installed'] = false;
}

foreach($_SESSION as $key => $value) {
	if(strpos($key, 'var_') !== false)
		unset($_SESSION[$key]);
}
unset($_SESSION['saved']);
?>