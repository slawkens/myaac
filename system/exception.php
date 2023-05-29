<?php
/**
 * Whoops exception handler
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2023 MyAAC
 * @link      https://my-aac.org
 */

$whoops = new \Whoops\Run;

if(IS_CLI) {
	$whoops->pushHandler(new \Whoops\Handler\PlainTextHandler);
}
else {
	$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
}

$whoops->register();
