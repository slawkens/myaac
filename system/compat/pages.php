<?php
/**
 * Compat pages (backward support for Gesior AAC)
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2019 MyAAC
 * @link      https://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');
switch($page)
{
	case 'adminpanel':
		header('Location: ' . ADMIN_URL);
		die;

	case 'createaccount':
		$page = 'account/create';
		break;

	case 'accountmanagement':
		$page = 'account/manage';
		break;

	case 'lostaccount':
		$page = 'account/lost';
		break;

	case 'whoisonline':
		$page = 'online';
		break;

	case 'latestnews':
		$page = 'news';
		break;

	case 'archive':
	case 'newsarchive':
		$page = 'news/archive';
		break;

	case 'tibiarules':
		$page = 'rules';
		break;

	case 'killstatistics':
		$page = 'last-kills';
		break;

	case 'buypoints':
		$page = 'points';
		break;

	case 'shopsystem':
		$page = 'gifts';
		break;

	default:
		break;
}
