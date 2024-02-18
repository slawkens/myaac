<?php
/**
 * Project: MyAAC
 *     Automatic Account Creator for Open Tibia Servers
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
 * @copyright 2020 MyAAC
 * @link      https://my-aac.org
 */

use MyAAC\DataLoader;

const MYAAC_ADMIN = true;

require '../../common.php';
require SYSTEM . 'functions.php';
require SYSTEM . 'init.php';
require SYSTEM . 'login.php';

if (!admin())
	die('Access denied.');

ini_set('max_execution_time', 300);
ob_implicit_flush();
@ob_end_flush();
header('X-Accel-Buffering: no');

require LOCALE . 'en/main.php';
require LOCALE . 'en/install.php';

DataLoader::setLocale($locale);
DataLoader::load();
