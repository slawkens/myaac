<?php
/**
 * Version check
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2017 MyAAC
 * @version   0.3.0
 * @link      http://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');
$title = 'Version check';

// fetch version
//$file = @fopen('http://my-aac.org/VERSION', 'r') or die('Error while fetching version.');
//$myaac_version = fgets($file);
$myaac_version = file_get_contents('http://my-aac.org/VERSION');

// compare them
if(version_compare($myaac_version, MYAAC_VERSION) <= 0)
	echo '<p class="success">MyAAC latest version is ' . $myaac_version . '. You\'re using the latest version.
	<br/>View CHANGELOG ' . generateLink(ADMIN_URL . '?p=changelog', 'here') . '</p>';
else
	echo '<p class="warning">You\'re using outdated version.<br/>
		Your version: <b>' . MYAAC_VERSION . '</b><br/>
		Latest version: <b>' . $myaac_version . '</b><br/>
		Download available at: <a href="http://my-aac.org" target="_blank">www.my-aac.org</a></p>';

/*
function version_revert($version)
{
	$major = floor($version / 10000);
	$version -= $major * 10000;

	$minor = floor($version / 100);
	$version -= $minor * 100;

	$release = $version;
	return $major . '.' . $minor . '.' . $release;
}*/
?>
