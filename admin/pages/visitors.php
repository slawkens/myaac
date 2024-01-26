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
use MyAAC\Visitors;

$title = 'Visitors';
$use_datatable = true;

if (!setting('core.visitors_counter')): ?>
	Visitors counter is disabled.<br/>
	You can enable it by editing this configurable in <b>config.local.php</b> file:<br/>
	<p style="margin-left: 3em;"><b>$config['visitors_counter'] = true;</b></p>
	<?php
	return;
endif;

$visitors = new Visitors(setting('core.visitors_counter_ttl'));

function compare($a, $b): int {
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
			$bot = $dd->getBot();
			$message = '(Bot) %s, <a href="%s" target="_blank">%s</a>';
			$browser = sprintf($message, $bot['category'], $bot['url'], $bot['name']);
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
	'config_visitors_counter_ttl' => setting('core.visitors_counter_ttl'),
	'visitors' => $tmp
));
?>
