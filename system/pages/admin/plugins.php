<?php
/**
 * Plugins
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2017 MyAAC
 * @version   0.5.1
 * @link      http://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');
$title = 'Plugin manager';

require(SYSTEM . 'hooks.php');

echo $twig->render('admin.plugins.form.html.twig');

function deleteDirectory($dir) {
	if(!file_exists($dir)) {
		return true;
	}
	
	if(!is_dir($dir)) {
		return unlink($dir);
	}
	
	foreach(scandir($dir) as $item) {
		if($item == '.' || $item == '..') {
			continue;
		}
		
		if(!deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
			return false;
		}
	}
	
	return rmdir($dir);
}

if(isset($_REQUEST['uninstall'])){
	$uninstall = $_REQUEST['uninstall'];
	
	$filename = BASE . 'plugins/' . $uninstall . '.json';
	if(!file_exists($filename)) {
		error('Plugin ' . $uninstall . ' does not exist.');
	}
	else {
		$string = file_get_contents($filename);
		$plugin_info = json_decode($string, true);
		if($plugin_info == false) {
			warning('Cannot load plugin info ' . $uninstall . '.json');
		}
		else {
			$success = true;
			foreach($plugin_info['uninstall'] as $file) {
				$file = BASE . $file;
				if(!deleteDirectory($file)) {
					$success = false;
				}
			}
			
			if($success) {
				if($cache->enabled()) {
					$cache->delete('templates');
				}
				success('Successfully uninstalled plugin ' . $uninstall);
			}
			else {
				error('Error while uninstalling plugin ' . $uninstall . ': ' . error_get_last());
			}
		}
	}
}
else if(isset($_FILES["plugin"]["name"]))
{
	$file = $_FILES["plugin"];
	$filename = $file["name"];
	$tmp_name = $file["tmp_name"];
	$type = $file["type"];

	$name = explode(".", $filename);
	$accepted_types = array('application/zip', 'application/x-zip-compressed', 'multipart/x-zip', 'application/x-compressed', 'application/octet-stream', 'application/zip-compressed');

	if(isset($file['error'])) {
		$error = 'Error uploading file';
		switch($file['error']) {
			case UPLOAD_ERR_OK:
				$error = false;
				break;
			case UPLOAD_ERR_INI_SIZE:
			case UPLOAD_ERR_FORM_SIZE:
				$error .= ' - file too large (limit of '.ini_get('upload_max_filesize').' bytes).';
				break;
			case UPLOAD_ERR_PARTIAL:
				$error .= ' - file upload was not completed.';
				break;
			case UPLOAD_ERR_NO_FILE:
				$error .= ' - zero-length file uploaded.';
				break;
			default:
				$error .= ' - internal error #' . $file['error'];
				break;
		}
	}

	if(isset($error) && $error != false) {
		error($error);
	}
	else {
		if(is_uploaded_file($file['tmp_name']) ) {
			$filetype = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
			if($filetype == 'zip') // check if it is zipped/compressed file
			{
				$tmp_filename = pathinfo($filename, PATHINFO_FILENAME);
				$targetdir = BASE;
				$targetzip = BASE . 'plugins/' . $tmp_filename;

				if(move_uploaded_file($tmp_name, $targetzip)) { // move uploaded file
					$zip = new ZipArchive();
					$x = $zip->open($targetzip);  // open the zip file to extract
					if ($x === true) {
						for ($i = 0; $i < $zip->numFiles; $i++) {
							$tmp = $zip->getNameIndex($i);
							if(pathinfo($tmp, PATHINFO_DIRNAME) == 'plugins' && pathinfo($tmp, PATHINFO_EXTENSION) == 'json')
								$json_file = $tmp;
						}
						
						if(!isset($json_file)) {
							error('Cannot find plugin info .json file. Installation is discontinued.');
						}
						else if($zip->extractTo($targetdir)) { // place in the directory with same name
							$file_name = BASE . $json_file;
							if(!file_exists($file_name))
								warning("Cannot load " . $file_name . ". File doesn't exist.");
							else {
								$string = file_get_contents($file_name);
								$plugin = json_decode($string, true);
								if ($plugin == null) {
									warning('Cannot load ' . $file_name . '. File might be not a valid json code.');
								}
								else {
									$continue = true;
									
									if(isset($plugin['require'])) {
										$require = $plugin['require'];
										if(isset($require['myaac'])) {
											$require_myaac = $require['myaac'];
											if(version_compare(MYAAC_VERSION, $require_myaac, '<')) {
												warning("This plugin requires MyAAC version " . $require_myaac . ", you're using version " . MYAAC_VERSION . " - please update.");
												$continue = false;
											}
										}
										
										if(isset($require['php'])) {
											$require_php = $require['php'];
											if(version_compare(phpversion(), $require_php, '<')) {
												warning("This plugin requires PHP version " . $require_php . ", you're using version " . phpversion() . " - please update.");
												$continue = false;
											}
										}
										
										if(isset($require['database'])) {
											$require_database = $require['database'];
											if($require_database < DATABASE_VERSION) {
												warning("This plugin requires database version " . $require_database . ", you're using version " . DATABASE_VERSION . " - please update.");
												$continue = false;
											}
										}
									}
									
									if($continue) {
										if (isset($plugin['install'])) {
											if (file_exists(BASE . $plugin['install']))
												require(BASE . $plugin['install']);
											else
												warning('Cannot load install script. Your plugin might be not working correctly.');
										}
										
										if (isset($plugin['hooks'])) {
											foreach ($plugin['hooks'] as $_name => $info) {
												if (isset($hook_types[$info['type']])) {
													$query = $db->query('SELECT `id` FROM `' . TABLE_PREFIX . 'hooks` WHERE `name` = ' . $db->quote($_name) . ';');
													if ($query->rowCount() == 1) { // found something
														$query = $query->fetch();
														$db->query('UPDATE `' . TABLE_PREFIX . 'hooks` SET `type` = ' . $hook_types[$info['type']] . ', `file` = ' . $db->quote($info['file']) . ' WHERE `id` = ' . (int)$query['id'] . ';');
													} else {
														$db->query('INSERT INTO `' . TABLE_PREFIX . 'hooks` (`id`, `name`, `type`, `file`) VALUES (NULL, ' . $db->quote($_name) . ', ' . $hook_types[$info['type']] . ', ' . $db->quote($info['file']) . ');');
													}
												} else
													warning('Unknown event type: ' . $info['type']);
											}
										}
										
										if($cache->enabled()) {
											$cache->delete('templates');
										}
										success('<strong>' . $plugin['name'] . '</strong> plugin has been successfully installed.');
									}
								}
							}
						}
						else {
							error('There was a problem with extracting zip archive.');
						}
						
						$zip->close();
						unlink($targetzip); // delete the Zipped file
					}
					else {
						error('There was a problem with opening zip archive.');
					}
				}
				else
					error('There was a problem with the upload. Please try again.');
			}
			else {
				error('The file you are trying to upload is not a .zip file. Please try again.');
			}
		}
		else {
			error('Error uploading file - unknown error.');
		}
	}
}

$plugins = array();
$rows = array();

$path = PLUGINS;
foreach(scandir($path) as $file)
{
	$file_ext = pathinfo($file, PATHINFO_EXTENSION);
	$file_name = pathinfo($file, PATHINFO_FILENAME);
	if($file == '.' || $file == '..' || $file == 'disabled' || $file == 'example.json' || is_dir($path . $file) || $file_ext != 'json')
		continue;
	
	$file_info = str_replace('.json', '', $file_name);
	$string = file_get_contents(BASE . 'plugins/' . $file_info . '.json');
	$plugin_info = json_decode($string, true);
	if($plugin_info == false) {
		warning('Cannot load plugin info ' . $file);
	}
	else {
		$rows[] = array(
			'name' => $plugin_info['name'],
			'description' => $plugin_info['description'],
			'version' => $plugin_info['version'],
			'author' => $plugin_info['author'],
			'contact' => $plugin_info['contact'],
			'file' => $file_info,
			'uninstall' => isset($plugin_info['uninstall'])
		);
	}
}

echo $twig->render('admin.plugins.html.twig', array(
	'plugins' => $rows
));
?>