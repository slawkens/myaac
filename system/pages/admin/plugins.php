<?php
/**
 * Plugins
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2017 MyAAC
 * @version   0.0.1
 * @link      http://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');
$title = 'Plugin manager';
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

	if(in_array($type, $accepted_types) && strtolower($name[1]) == 'zip') // check if it is zipped/compressed file
	{
		$targetdir = BASE;
		$targetzip = BASE . 'plugins/' . $name[0] . '.zip';

		if(move_uploaded_file($tmp_name, $targetzip)) { // move uploaded file
		$zip = new ZipArchive();
		$x = $zip->open($targetzip);  // open the zip file to extract
		if ($x === true) {
			$zip->extractTo($targetdir); // place in the directory with same name  
			$zip->close();
				unlink($targetzip); // delete the Zipped file
	  
				$string = file_get_contents(BASE . 'plugins/' . $name[0] . '.json');
				$plugin_info = json_decode($string, true);
				$message = '<p class="success"><strong>' . $plugin_info['name'] . '</strong> plugin has been successfully installed.</p>';
		}
	}
		else
			$message = '<p class="error">There was a problem with the upload. Please try again.</p>';
}
	else
		$message = '<p class="error">The file you are trying to upload is not a .zip file. Please try again.</p>';
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
		if($file == '.' || $file == '..' || $file == 'disabled' || is_dir($file) || !$file_info[1] || $file_info[1] != 'json')
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