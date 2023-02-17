<?php
/**
 * Visitors viewer
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2019 MyAAC
 * @link      https://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');

use DeviceDetector\DeviceDetector;
use DeviceDetector\Parser\Client\Browser;
use DeviceDetector\Parser\OperatingSystem;

$title = 'Visitors';
$use_datatable = true;

if (!$config['visitors_counter']): ?>
	Visitors counter is disabled.<br/>
	You can enable it by editing this configurable in <b>config.local.php</b> file:<br/>
	<p style="margin-left: 3em;"><b>$config['visitors_counter'] = true;</b></p>
	<?php
	return;
endif;

require SYSTEM . 'libs/visitors.php';
$visitors = new Visitors($config['visitors_counter_ttl']);

function compare($a, $b)
{
	return $a['lastvisit'] > $b['lastvisit'] ? -1 : 1;
}

$tmp = $visitors->getVisitors();
usort($tmp, 'compare');

foreach ($tmp as &$visitor) {
	$userAgent = $visitor['user_agent'] ?? '';
	if (!strlen($userAgent) || $userAgent == 'unknown') {
		$browser = 'Unknown';
	}
	else {
		$dd = new DeviceDetector($userAgent);
		$dd->parse();
		if ($dd->isBot()) {
			$browser = '(Bot) ' . $dd->getBot();
		}
		else {
			$osFamily = OperatingSystem::getOsFamily($dd->getOs('name'));
			$browserFamily = Browser::getBrowserFamily($dd->getClient('name'));

			$browser = $osFamily . ', ' . $browserFamily;
		}
	}

	$visitor['browser'] = $browser;
}

$twig->display('admin.visitors.html.twig', array(
	'config_visitors_counter_ttl' => $config['visitors_counter_ttl'],
	'visitors' => $tmp
));
?>
