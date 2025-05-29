<?php

$baseDir = '';
$tmp = explode('/', $_SERVER['SCRIPT_NAME']);
$size = count($tmp) - 1;
for($i = 1; $i < $size; $i++)
	$baseDir .= '/' . $tmp[$i];

$baseDir = str_replace(['/' . ADMIN_PANEL_FOLDER, '/install', '/tools'], '', $baseDir);

if(!IS_CLI) {
	if (isset($_SERVER['HTTP_HOST'][0])) {
		$baseHost = $_SERVER['HTTP_HOST'];
	} else {
		if (isset($_SERVER['SERVER_NAME'][0])) {
			$baseHost = $_SERVER['SERVER_NAME'];
		} else {
			$baseHost = $_SERVER['SERVER_ADDR'];
		}
	}
}
