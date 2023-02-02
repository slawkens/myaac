<?php
define('MYAAC_ADMIN', true);

require '../../common.php';
require SYSTEM . 'functions.php';
require SYSTEM . 'init.php';
require SYSTEM . 'login.php';

if(!admin())
	die('Access denied.');

// Don't attempt to process the upload on an OPTIONS request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
	header('Access-Control-Allow-Methods: POST, OPTIONS');
	return;
}

$imageFolder = BASE . EDITOR_IMAGES_DIR;

reset ($_FILES);
$temp = current($_FILES);
if (is_uploaded_file($temp['tmp_name'])) {
	header('Access-Control-Allow-Credentials: true');
	header('P3P: CP="There is no P3P policy."');

	// Sanitize input
	if (preg_match("/([^\w\s\d\-_~,;:\[\]\(\).])|([\.]{2,})/", $temp['name'])) {
		header('HTTP/1.1 400 Invalid file name.');
		return;
	}

	// Verify extension
	$ext = strtolower(pathinfo($temp['name'], PATHINFO_EXTENSION));
	if (!in_array($ext, ['gif', 'jpg', 'png'])) {
		header('HTTP/1.1 400 Invalid extension.');
		return;
	}

	do {
		$randomName = generateRandomString(8). ".$ext";
		$fileToWrite = $imageFolder . $randomName;
	} while (file_exists($fileToWrite));

	move_uploaded_file($temp['tmp_name'], $fileToWrite);

	$returnPathToImage = BASE_URL . EDITOR_IMAGES_DIR . $randomName;
	echo json_encode(['location' => $returnPathToImage]);
} else {
	// Notify editor that the upload failed
	header('HTTP/1.1 500 Server Error');
}


