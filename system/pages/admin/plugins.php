<?php
/**
 * Plugins
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2017 MyAAC
 * @version   0.1.3
 * @link      http://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');
$title = 'Plugin manager';

require(SYSTEM . 'hooks.php');
?>
<form enctype="multipart/form-data" method="post">
	<input type="hidden" name="upload_plugin" />
	<table cellspacing="3" border="0">
		<tr>
			<td colspan="2">Install plugin:</td>
		</tr>
		<tr>
			<td>
				<input type="file" name="plugin" />
			</td>
			<td>
				<input type="submit" value="Upload" />
			</td>
		</tr>
	</table>
</form>
<br/><br/>

<?php
$message = '';
if(isset($_FILES["plugin"]["name"]))
{
	$file = $_FILES["plugin"];
	$filename = $file["name"];
	$tmp_name = $file["tmp_name"];
	$type = $file["type"];

	$name = explode(".", $filename);
	$accepted_types = array('application/zip', 'application/x-zip-compressed', 'multipart/x-zip', 'application/x-compressed');

	if(isset($file['error'])) {
        $error = 'Error uploading file';
        switch( $file['error'] ) {
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
			if(in_array($type, $accepted_types) && strtolower($name[1]) == 'zip') // check if it is zipped/compressed file
			{
				$targetdir = BASE;
				$targetzip = BASE . 'plugins/' . $name[0] . '.zip';

				if(move_uploaded_file($tmp_name, $targetzip)) { // move uploaded file
					$zip = new ZipArchive();
					$x = $zip->open($targetzip);  // open the zip file to extract
					if ($x === true) {
						if($zip->extractTo($targetdir)) { // place in the directory with same name  
							$string = file_get_contents(BASE . 'plugins/' . $name[0] . '.json');
							$plugin = json_decode($string, true);
							if($plugin == NULL) {
								warning('Cannot load ' . BASE . 'plugins/' . $name[0] . '.json. File might be not valid json code.');
							}
							
							if(isset($plugin['install'])) {
								if(file_exists(BASE . $plugin['install']))
									require(BASE . $plugin['install']);
								else
									warning('Cannot load install script. Your plugin might be not working correctly.');
							}

							if(isset($plugin['hooks'])) {
								foreach($plugin['hooks'] as $_name => $info) {
									if(isset($hook_types[$info['type']])) {
										$query = $db->query('SELECT `id` FROM `' . TABLE_PREFIX . 'hooks` WHERE `name` = ' . $db->quote($_name) . ';');
										if($query->rowCount() == 1) { // found something
											$query = $query->fetch();
											$db->query('UPDATE `' . TABLE_PREFIX . 'hooks` SET `type` = ' . $hook_types[$info['type']] . ', `file` = ' . $db->quote($info['file']) . ' WHERE `id` = ' . (int)$query['id'] . ';');
										}
										else {
											$db->query('INSERT INTO `' . TABLE_PREFIX . 'hooks` (`id`, `name`, `type`, `file`) VALUES (NULL, ' . $db->quote($_name) . ', ' . $hook_types[$info['type']] . ', ' . $db->quote($info['file']) . ');');
										}
									}
									else
										warning('Unknown event type: ' . $info['type']);
								}
							}
							success('<strong>' . $plugin['name'] . '</strong> plugin has been successfully installed.');
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

echo $message;
?>
<b>Installed plugins:</b>
<table class="table" border="0" align="center">
<tr>
	<th>Plugin name (Description on hover)</th>
	<th>Filename</th>
	<th>Version</th>
	<th>Author</th>
	<th>Contact</th>
<?php
	$plugins = array();

	$path = PLUGINS;
	foreach(scandir($path) as $file)
	{
		$file_info = explode('.', $file);
		if($file == '.' || $file == '..' || $file == 'disabled' || $file == 'example.json' || is_dir($path . $file) || !$file_info[1] || $file_info[1] != 'json')
			continue;
		
		$string = file_get_contents(BASE . 'plugins/' . $file_info[0] . '.json');
		$plugin_info = json_decode($string, true);
		echo '<tr>
			<td><div title="' . $plugin_info['description'] . '">' . $plugin_info['name'] . '</div></td>
			<td>' . $file . '</td>
			<td>' . $plugin_info['version'] . '</td>
			<td>' . $plugin_info['author'] . '</td>
			<td>' . $plugin_info['contact'] . '</td>
		</tr>';
	}

?>
</table>