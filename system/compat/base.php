<?php
/**
 * Deprecated functions (compat)
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2019 MyAAC
 * @link      https://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');

function getCreatureImgPath($creature): string {
	return getMonsterImgPath($creature);
}
