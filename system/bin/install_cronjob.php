<?php

require_once __DIR__ . '/../../common.php';
require_once SYSTEM . 'functions.php';
require_once SYSTEM . 'init.php';

if(!IS_CLI) {
	echo 'This script can be run only in command line mode.' . PHP_EOL;
	exit(1);
}

if (MYAAC_OS !== 'LINUX') {
	echo 'This script can be run only on linux.' . PHP_EOL;
	exit(1);
}

$job = '* * * * * /usr/bin/php ' . SYSTEM . 'bin/cronjob.php >> ' . SYSTEM . 'logs/cron.log 2>&1';

if (cronjob_exists($job)) {
	echo 'MyAAC cronjob already installed.' . PHP_EOL;
	exit(0);
}

exec ('crontab -l', $content);

$content = implode(' ', $content);
$content .= PHP_EOL . $job;

file_put_contents(CACHE . 'cronjob', $content . PHP_EOL);
exec('crontab ' . CACHE. 'cronjob');

echo 'Installed crontab successfully.' . PHP_EOL;

function cronjob_exists($command)
{
	$cronjob_exists=false;

	exec('crontab -l', $crontab);
	if(isset($crontab)&&is_array($crontab)) {

		$crontab = array_flip($crontab);

		if(isset($crontab[$command])){
			$cronjob_exists = true;
		}

	}

	return $cronjob_exists;
}
