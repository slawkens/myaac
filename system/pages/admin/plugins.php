<?php
/**
 * Plugins
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2017 MyAAC
 * @version   0.6.3
 * @link      http://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');
$title = 'Plugin manager';

require(SYSTEM . 'hooks.php');
require(LIBS . 'plugins.php');

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

echo $twig->render('admin.plugins.form.html.twig');

if(isset($_REQUEST['uninstall'])){
	$uninstall = $_REQUEST['uninstall'];
	
	$filename = BASE . 'plugins/' . $uninstall . '.json';
	if(!file_exists($filename)) {
		error('Plugin ' . $uninstall . ' does not exist.');
	}
	else {
		if(!isset($plugin_info['uninstall'])) {
			error("Plugin doesn't have uninstall options defined. Skipping...");
		}
		else {
			$string = file_get_contents($filename);
			$plugin_info = json_decode($string, true);
			if($plugin_info == false) {
				error('Cannot load plugin info ' . $uninstall . '.json');
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
				$targetzip = BASE . 'plugins/' . $tmp_filename . '.zip';

				if(move_uploaded_file($tmp_name, $targetzip)) { // move uploaded file
					if(Plugins::install($targetzip)) {
						foreach(Plugins::getWarnings() as $warning) {
							warning($warning);
						}
						$info = Plugins::getPluginInfo();
						success((isset($info['name']) ? '<strong>' . $info['name'] . '</strong> p' : 'P') . 'lugin has been successfully installed.');
					}
					else
						error(Plugins::getError());
					
					unlink($targetzip); // delete the Zipped file
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
foreach(get_plugins() as $plugin)
{
	$string = file_get_contents(BASE . 'plugins/' . $plugin . '.json');
	$plugin_info = json_decode($string, true);
	if($plugin_info == false) {
		warning('Cannot load plugin info ' . $plugin . '.json');
	}
	else {
		$plugins[] = array(
			'name' => isset($plugin_info['name']) ? $plugin_info['name'] : '',
			'description' => isset($plugin_info['description']) ? $plugin_info['description'] : '',
			'version' => isset($plugin_info['version']) ? $plugin_info['version'] : '',
			'author' => isset($plugin_info['author']) ? $plugin_info['author'] : '',
			'contact' => isset($plugin_info['contact']) ? $plugin_info['contact'] : '',
			'file' => $plugin,
			'uninstall' => isset($plugin_info['uninstall'])
		);
	}
}

echo $twig->render('admin.plugins.html.twig', array(
	'plugins' => $plugins
));
?>