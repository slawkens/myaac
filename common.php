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
 * @copyright 2024 MyAAC
 * @link      https://my-aac.org
 */
if (version_compare(phpversion(), '8.1', '<')) die('PHP version 8.1 or higher is required.');

const MYAAC = true;
const MYAAC_VERSION = '1.8.5-dev';
const DATABASE_VERSION = 46;
const TABLE_PREFIX = 'myaac_';
define('START_TIME', microtime(true));
define('MYAAC_OS', stripos(PHP_OS, 'WIN') === 0 ? 'WINDOWS' : (strtoupper(PHP_OS) === 'DARWIN' ? 'MAC' : 'LINUX'));
define('IS_CLI', in_array(php_sapi_name(), ['cli', 'phpdb']));

// account flags
const FLAG_NONE = 0;
const FLAG_ADMIN = 1;
const FLAG_SUPER_ADMIN = 2;
const FLAG_SUPER_BOTH = 3;
const FLAG_CONTENT_PAGES = 4;
const FLAG_CONTENT_MAILER = 8;
const FLAG_CONTENT_NEWS = 16;
const FLAG_CONTENT_FORUM = 32;
const FLAG_CONTENT_COMMANDS = 64;
const FLAG_CONTENT_SPELLS = 128;
const FLAG_CONTENT_MONSTERS = 256;
const FLAG_CONTENT_GALLERY = 512;
const FLAG_CONTENT_VIDEOS = 1024;
const FLAG_CONTENT_FAQ = 2048;
const FLAG_CONTENT_MENUS = 4096;
const FLAG_CONTENT_PLAYERS = 8192;

// account access types
const ACCOUNT_WEB_FLAGS = [
	FLAG_NONE => 'None',
	FLAG_ADMIN =>'Admin',
	FLAG_SUPER_ADMIN => 'Super Admin',
	FLAG_SUPER_BOTH =>'(Admin + Super Admin)',
];

// news
const NEWS = 1;
const TICKER = 2;
const ARTICLE = 3;

// here you can change location of admin panel
// you need also to rename folder "admin"
// this may improve security
const ADMIN_PANEL_FOLDER = 'admin';

// directories
const BASE = __DIR__ . '/';
const ADMIN = BASE . ADMIN_PANEL_FOLDER . '/';
const SYSTEM = BASE . 'system/';
const CACHE = SYSTEM . 'cache/';
const LOCALE = SYSTEM . 'locale/';
const LIBS = SYSTEM . 'libs/';
const LOGS = SYSTEM . 'logs/';
const PAGES = SYSTEM . 'pages/';
const PLUGINS = BASE . 'plugins/';
const TEMPLATES = BASE . 'templates/';
const TOOLS = BASE . 'tools/';
const VENDOR = BASE . 'vendor/';

// other dirs
const SESSIONS_DIR = SYSTEM . 'php_sessions';
const GUILD_IMAGES_DIR = 'images/guilds/';
const EDITOR_IMAGES_DIR = 'images/editor/';
const GALLERY_DIR = 'images/gallery/';

// menu categories
const MENU_CATEGORY_NEWS = 1;
const MENU_CATEGORY_ACCOUNT = 2;
const MENU_CATEGORY_COMMUNITY = 3;
const MENU_CATEGORY_FORUM = 4;
const MENU_CATEGORY_LIBRARY = 5;
const MENU_CATEGORY_SHOP = 6;

// otserv versions
const OTSERV = 1;
const OTSERV_06 = 2;
const OTSERV_FIRST = OTSERV;
const OTSERV_LAST = OTSERV_06;
const TFS_02 = 3;
const TFS_03 = 4;
const TFS_FIRST = TFS_02;
const TFS_LAST = TFS_03;

// other definitions
const MAIL_MAIL = 0;
const MAIL_SMTP = 1;

const SMTP_SECURITY_NONE = 0;
const SMTP_SECURITY_SSL = 1;
const SMTP_SECURITY_TLS = 2;

const ACCOUNT_NUMBER_LENGTH = 8;

if (!IS_CLI) {
	session_save_path(SESSIONS_DIR);
	session_start();
}

$config_dir = getenv('MYAAC_CONFIG_DIR');
define('CONFIG_DIR', $config_dir ? rtrim($config_dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR : BASE);
if (file_exists(CONFIG_DIR . 'config.local.php')) {
	require CONFIG_DIR . 'config.local.php';
}

require SYSTEM . 'base.php';
define('BASE_DIR', $baseDir);

if(!IS_CLI) {
	if (isset($config['site_url'])) {
		$hasSlashAtEnd = ($config['site_url'][strlen($config['site_url']) - 1] == '/');

		define('SERVER_URL', $config['site_url']);
		define('BASE_URL', SERVER_URL . ($hasSlashAtEnd ? '' : '/'));
		define('ADMIN_URL', SERVER_URL . ($hasSlashAtEnd ? '' : '/') . ADMIN_PANEL_FOLDER . '/');
	}
	else {
		define('SERVER_URL', 'http' . (isHttps() ? 's' : '') . '://' . $baseHost);
		define('BASE_URL', SERVER_URL . BASE_DIR . '/');
		define('ADMIN_URL', SERVER_URL . BASE_DIR . '/' . ADMIN_PANEL_FOLDER . '/');

		//define('CURRENT_URL', BASE_URL . $_SERVER['REQUEST_URI']);
	}
}

/** @var array $config */
ini_set('log_errors', 1);
if(@$config['env'] === 'dev' || defined('MYAAC_INSTALL')) {
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
}
else {
	ini_set('display_errors', 0);
	ini_set('display_startup_errors', 0);
	error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
}

$autoloadFile = VENDOR . 'autoload.php';
if (!is_file($autoloadFile)) {
	throw new RuntimeException('The vendor folder is missing. Please download Composer: <a href="https://getcomposer.org/download">https://getcomposer.org/download</a>, install it and execute in the main MyAAC directory this command: <b>composer install</b>. Or download MyAAC from <a href="https://github.com/slawkens/myaac/releases">GitHub releases</a>, which includes Vendor folder.');
}

require $autoloadFile;

function isHttps(): bool
{
	return
		(!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https')
		|| (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
		|| (isset($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] === 443);
}
