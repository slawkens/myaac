<?php
/**
 * Logs
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2020 MyAAC
 * @link      https://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');
$title = 'Logs Viewer';
$use_datatable = true;

$files = [];
$allowedFiles = [];

$normalizePath = static function ($path) {
	return str_replace('\\', '/', $path);
};

$isPathInsideDirectory = static function ($path, $directory) use ($normalizePath) {
	$normalizedPath = $normalizePath($path);
	$normalizedDirectory = rtrim($normalizePath($directory), '/') . '/';

	return strpos($normalizedPath, $normalizedDirectory) === 0;
};

$addLogFile = static function ($name, $basePath, $baseRealPath) use (&$files, &$allowedFiles, $isPathInsideDirectory) {
	$path = $basePath . $name;
	$realPath = realpath($path);
	if ($realPath === false || !is_file($realPath) || !$isPathInsideDirectory($realPath, $baseRealPath)) {
		return;
	}

	$mtime = filemtime($realPath);
	if ($mtime === false) {
		return;
	}

	$files[] = [
		0 => $name,
		1 => $basePath,
		'mtime' => $mtime,
		'name' => $name,
	];

	if (!array_key_exists($name, $allowedFiles)) {
		$allowedFiles[$name] = [
			'path' => $realPath,
			'base' => $baseRealPath,
		];
	}
};

$aac_path_logs = BASE . 'system/logs/';
$aac_path_logs_real = realpath($aac_path_logs);
if ($aac_path_logs_real !== false) {
	$aac_log_files = scandir($aac_path_logs, SCANDIR_SORT_ASCENDING);
	if ($aac_log_files !== false) {
		foreach ($aac_log_files as $f) {
			if ($f[0] === '.' || $f === 'index.html') {
				continue;
			}

			$addLogFile($f, $aac_path_logs, $aac_path_logs_real);
		}
	}
}

$server_path_logs = $config['server_path'] . 'logs/';
if (!file_exists($server_path_logs)) {
	$server_path_logs = $config['data_path'] . 'logs/';
}

$server_path_logs_real = realpath($server_path_logs);
if ($server_path_logs_real !== false) {
	$server_log_files = scandir($server_path_logs, SCANDIR_SORT_ASCENDING);
	if ($server_log_files !== false) {
		foreach ($server_log_files as $f) {
			if ($f[0] === '.') {
				continue;
			}

			$path = $server_path_logs . $f;
			$realPath = realpath($path);
			if ($realPath !== false && is_dir($realPath) && $isPathInsideDirectory($realPath, $server_path_logs_real)) {
				$sub_files = scandir($path, SCANDIR_SORT_ASCENDING);
				if ($sub_files === false) {
					continue;
				}

				foreach ($sub_files as $f2) {
					if ($f2[0] === '.') {
						continue;
					}

					$addLogFile($f . '/' . $f2, $server_path_logs, $server_path_logs_real);
				}

				continue;
			}

			$addLogFile($f, $server_path_logs, $server_path_logs_real);
		}
	}
}

$file = isset($_GET['file']) && is_string($_GET['file']) ? $_GET['file'] : null;
if ($file !== null && $file !== '') {
	if (array_key_exists($file, $allowedFiles)) {
		$allowedFile = $allowedFiles[$file];
		$realPath = realpath($allowedFile['path']);

		if ($realPath === false || !is_file($realPath) || !$isPathInsideDirectory($realPath, $allowedFile['base'])) {
			error('Invalid file specified.');
		} else {
			$file_content = file_get_contents($realPath);
			if ($file_content === false) {
				error('Unable to read specified file.');
			} else {
				$twig->display('admin.logs.view.html.twig', ['file' => $file, 'content' => $file_content]);
			}
		}
	} else {
		error('Invalid file specified.');
	}
}

$twig->display('admin.logs.html.twig', ['files' => $files]);
