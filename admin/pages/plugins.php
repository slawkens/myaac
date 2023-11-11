<?php
/**
 * Plugins
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2019 MyAAC
 * @link      https://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');
$title = 'Plugin manager';

csrfProtect();

$use_datatable = true;

require_once LIBS . 'plugins.php';

if (!getBoolean(setting('core.admin_plugins_manage_enable'))) {
	warning('Plugin installation and management is disabled in Settings.<br/>If you wish to enable, go to Settings and enable <strong>Enable Plugins Manage</strong>.');
}
else {
	$twig->display('admin.plugins.form.html.twig');

	if (isset($_POST['uninstall'])) {
		$uninstall = $_POST['uninstall'];

		if (Plugins::uninstall($uninstall)) {
			success('Successfully uninstalled plugin ' . $uninstall);
		} else {
			error('Error while uninstalling plugin ' . $uninstall . ': ' . Plugins::getError());
		}
	} else if (isset($_POST['enable'])) {
		$enable = $_POST['enable'];
		if (Plugins::enable($enable)) {
			success('Successfully enabled plugin ' . $enable);
		} else {
			error('Error while enabling plugin ' . $enable . ': ' . Plugins::getError());
		}
	} else if (isset($_POST['disable'])) {
		$disable = $_POST['disable'];
		if (Plugins::disable($disable)) {
			success('Successfully disabled plugin ' . $disable);
		} else {
			error('Error while disabling plugin ' . $disable . ': ' . Plugins::getError());
		}
	} else if (isset($_FILES['plugin']['name'])) {
		$file = $_FILES['plugin'];
		$filename = $file['name'];
		$tmp_name = $file['tmp_name'];
		$type = $file['type'];

		$name = explode('.', $filename);
		$accepted_types = array('application/zip', 'application/x-zip-compressed', 'multipart/x-zip', 'application/x-compressed', 'application/octet-stream', 'application/zip-compressed');

		if (isset($file['error'])) {
			$error = 'Error uploading file';
			switch ($file['error']) {
				case UPLOAD_ERR_OK:
					$error = false;
					break;
				case UPLOAD_ERR_INI_SIZE:
				case UPLOAD_ERR_FORM_SIZE:
					$error .= ' - file too large (limit of ' . ini_get('upload_max_filesize') . ' bytes). You can enlarge the limits by changing "upload_max_filesize" in php.ini';
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

		if (isset($error) && $error != false) {
			error($error);
		} else {
			if (is_uploaded_file($file['tmp_name'])) {
				$filetype = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
				if ($filetype == 'zip') // check if it is zipped/compressed file
				{
					$tmp_filename = pathinfo($filename, PATHINFO_FILENAME);
					$targetzip = BASE . 'plugins/' . $tmp_filename . '.zip';

					if (move_uploaded_file($tmp_name, $targetzip)) { // move uploaded file
						if (Plugins::install($targetzip)) {
							foreach (Plugins::getWarnings() as $warning) {
								warning($warning);
							}

							$info = Plugins::getPluginJson();
							success((isset($info['name']) ? '<strong>' . $info['name'] . '</strong> p' : 'P') . 'lugin has been successfully installed.');
						} else {
							$error = Plugins::getError();
							error(!empty($error) ? $error : 'Unexpected error happened while installing plugin. Please try again later.');
						}

						unlink($targetzip); // delete the Zipped file
					} else
						error('There was a problem with the upload. Please try again.');
				} else {
					error('The file you are trying to upload is not a .zip file. Please try again.');
				}
			} else {
				error('Error uploading file - unknown error.');
			}
		}
	}
}

$plugins = array();
foreach (get_plugins(true) as $plugin) {
	$string = file_get_contents(BASE . 'plugins/' . $plugin . '.json');
	$plugin_info = json_decode($string, true);

	if (!$plugin_info) {
		warning('Cannot load plugin info ' . $plugin . '.json');
	} else {
		$disabled = (str_contains($plugin, 'disabled.'));
		$pluginOriginal = ($disabled ? str_replace('disabled.', '', $plugin) : $plugin);
		$plugins[] = array(
			'name' => $plugin_info['name'] ?? '',
			'description' => $plugin_info['description'] ?? '',
			'version' => $plugin_info['version'] ?? '',
			'author' => $plugin_info['author'] ?? '',
			'contact' => $plugin_info['contact'] ?? '',
			'file' => $pluginOriginal,
			'enabled' => !$disabled,
			'uninstall' => isset($plugin_info['uninstall'])
		);
	}
}

$twig->display('admin.plugins.html.twig', array(
	'plugins' => $plugins
));
