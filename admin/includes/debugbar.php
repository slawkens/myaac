<?php

$hooks->register('debugbar_admin_head_end', HOOK_ADMIN_HEAD_END, function ($params) {
	global $debugBar;

	if (!isset($debugBar)) {
		return;
	}

	$debugBarRenderer = $debugBar->getJavascriptRenderer();
	echo $debugBarRenderer->renderHead();
});
$hooks->register('debugbar_admin_body_end', HOOK_ADMIN_BODY_END, function ($params) {
	global $debugBar;

	if (!isset($debugBar)) {
		return;
	}

	$debugBarRenderer = $debugBar->getJavascriptRenderer();
	echo $debugBarRenderer->render();
});
