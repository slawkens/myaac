<?php
/**
 * Project: MyAAC
 *     Automatic Account Creator for Open Tibia Servers
 * File: common.php
 *
 * This is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This software is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2017 MyAAC
 * @version   0.2.3
 * @link      http://my-aac.org
 */
session_start();

define('MYAAC', true);
define('MYAAC_VERSION', '0.2.3');
define('DATABASE_VERSION', 4);
define('TABLE_PREFIX', 'myaac_');
define('START_TIME', microtime(true));
define('MYAAC_OS', (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') ? 'WINDOWS' : 'LINUX');

// account flags
define('FLAG_ADMIN', 1);
define('FLAG_SUPER_ADMIN', 2);
define('FLAG_CONTENT_PAGES', 4);
define('FLAG_CONTENT_MAILER', 8);
define('FLAG_CONTENT_NEWS', 16);
define('FLAG_CONTENT_FORUM', 32);
define('FLAG_CONTENT_COMMANDS', 64);
define('FLAG_CONTENT_SPELLS', 128);
define('FLAG_CONTENT_MONSTERS', 256);

// news
define('NEWS', 1);
define('TICKET', 2);
define('ARTICLE', 3);

// directories
define('BASE', dirname(__FILE__) . '/');
define('ADMIN', BASE . 'admin/');
define('SYSTEM', BASE . 'system/');
define('CACHE', SYSTEM . 'cache/');
define('LOCALE', SYSTEM . 'locale/');
define('LIBS', SYSTEM . 'libs/');
define('LOGS', SYSTEM . 'logs/');
define('PLUGINS', BASE . 'plugins/');
define('TEMPLATES', BASE . 'templates/');

// otserv versions
define('OTSERV', 1);
define('OTSERV_06', 2);
define('OTSERV_FIRST', OTSERV);
define('OTSERV_LAST', OTSERV_06);
define('TFS_02', 3);
define('TFS_03', 4);
define('TFS_FIRST', TFS_02);
define('TFS_LAST', TFS_03);

// basedir
$basedir = '';
$tmp = explode('/', $_SERVER['SCRIPT_NAME']);
$size = sizeof($tmp) - 1;
for($i = 1; $i < $size; $i++)
	$basedir .= '/' . $tmp[$i];

$basedir = str_replace('/admin', '', $basedir);
$basedir = str_replace('/install', '', $basedir);
define('BASE_DIR', $basedir);

if(isset($_SERVER['HTTPS'][0]) && $_SERVER['HTTPS'] == 'on')
	define('SERVER_URL', 'https://' . $_SERVER['HTTP_HOST']);
else
	define('SERVER_URL', 'http://' . $_SERVER['HTTP_HOST']);

define('BASE_URL', SERVER_URL . BASE_DIR . '/');
define('ADMIN_URL', SERVER_URL . BASE_DIR . '/admin/');
//define('CURRENT_URL', BASE_URL . $_SERVER['REQUEST_URI']);
?>
