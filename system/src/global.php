<?php

const SKILL_FRAGS = -1;
const SKILL_BALANCE = -2;

$i = 0;

define('HOOK_STARTUP', ++$i);
define('HOOK_BEFORE_PAGE', ++$i);
define('HOOK_AFTER_PAGE', ++$i);
define('HOOK_FINISH', ++$i);
define('HOOK_TIBIACOM_ARTICLE', ++$i);
define('HOOK_TIBIACOM_BORDER_3', ++$i);
define('HOOK_CHARACTERS_BEFORE_INFORMATIONS', ++$i);
define('HOOK_CHARACTERS_AFTER_INFORMATIONS', ++$i);
define('HOOK_CHARACTERS_BEFORE_SKILLS', ++$i);
define('HOOK_CHARACTERS_AFTER_SKILLS', ++$i);
define('HOOK_CHARACTERS_AFTER_QUESTS', ++$i);
define('HOOK_CHARACTERS_AFTER_EQUIPMENT', ++$i);
define('HOOK_CHARACTERS_BEFORE_DEATHS', ++$i);
define('HOOK_CHARACTERS_BEFORE_SIGNATURE', ++$i);
define('HOOK_CHARACTERS_AFTER_SIGNATURE', ++$i);
define('HOOK_CHARACTERS_AFTER_ACCOUNT', ++$i);
define('HOOK_CHARACTERS_AFTER_CHARACTERS', ++$i);
define('HOOK_LOGIN', ++$i);
define('HOOK_LOGIN_ATTEMPT', ++$i);
define('HOOK_LOGOUT', ++$i);
define('HOOK_ACCOUNT_CHANGE_PASSWORD_POST', ++$i);
define('HOOK_ACCOUNT_CREATE_BEFORE_FORM', ++$i);
define('HOOK_ACCOUNT_CREATE_BEFORE_BOXES', ++$i);
define('HOOK_ACCOUNT_CREATE_BETWEEN_BOXES_1', ++$i);
define('HOOK_ACCOUNT_CREATE_BETWEEN_BOXES_2', ++$i);
define('HOOK_ACCOUNT_CREATE_AFTER_BOXES', ++$i);
define('HOOK_ACCOUNT_CREATE_BEFORE_ACCOUNT', ++$i);
define('HOOK_ACCOUNT_CREATE_AFTER_ACCOUNT', ++$i);
define('HOOK_ACCOUNT_CREATE_AFTER_EMAIL', ++$i);
define('HOOK_ACCOUNT_CREATE_AFTER_COUNTRY', ++$i);
define('HOOK_ACCOUNT_CREATE_AFTER_PASSWORD', ++$i);
define('HOOK_ACCOUNT_CREATE_AFTER_PASSWORDS', ++$i);
define('HOOK_ACCOUNT_CREATE_BEFORE_CHARACTER_NAME', ++$i);
define('HOOK_ACCOUNT_CREATE_AFTER_CHARACTER_NAME', ++$i);
define('HOOK_ACCOUNT_CREATE_AFTER_SEX', ++$i);
define('HOOK_ACCOUNT_CREATE_AFTER_VOCATION', ++$i);
define('HOOK_ACCOUNT_CREATE_AFTER_TOWNS', ++$i);
define('HOOK_ACCOUNT_CREATE_BEFORE_SUBMIT_BUTTON', ++$i);
define('HOOK_ACCOUNT_CREATE_AFTER_FORM', ++$i);
define('HOOK_ACCOUNT_CREATE_POST', ++$i);
define('HOOK_ACCOUNT_CREATE_AFTER_SUBMIT', ++$i);
define('HOOK_ACCOUNT_CREATE_AFTER_SAVED', ++$i);
define('HOOK_ACCOUNT_MANAGE_BEFORE_GENERAL_INFORMATION', ++$i);
define('HOOK_ACCOUNT_MANAGE_BEFORE_PUBLIC_INFORMATION', ++$i);
define('HOOK_ACCOUNT_MANAGE_BEFORE_ACCOUNT_LOGS', ++$i);
define('HOOK_ACCOUNT_MANAGE_BEFORE_CHARACTERS', ++$i);
define('HOOK_ACCOUNT_LOGIN_BEFORE_PAGE', ++$i);
define('HOOK_ACCOUNT_LOGIN_BEFORE_ACCOUNT', ++$i);
define('HOOK_ACCOUNT_LOGIN_AFTER_ACCOUNT', ++$i);
define('HOOK_ACCOUNT_LOGIN_BEFORE_PASSWORD', ++$i);
define('HOOK_ACCOUNT_LOGIN_AFTER_PASSWORD', ++$i);
define('HOOK_ACCOUNT_LOGIN_AFTER_REMEMBER_ME', ++$i);
define('HOOK_ACCOUNT_LOGIN_AFTER_PAGE', ++$i);
define('HOOK_ACCOUNT_LOGIN_POST', ++$i);
define('HOOK_ACCOUNT_CREATE_CHARACTER_AFTER', ++$i);
define('HOOK_ACCOUNT_CREATE_CHARACTER_BEFORE_FIRST_TABLE', ++$i);
define('HOOK_ACCOUNT_CREATE_CHARACTER_BEFORE_VOCATIONS', ++$i);
define('HOOK_ACCOUNT_CREATE_CHARACTER_BEFORE_TOWNS', ++$i);
define('HOOK_ACCOUNT_CREATE_CHARACTER_AFTER_TOWNS', ++$i);
define('HOOK_ACCOUNT_CREATE_CHARACTER_AFTER_SECOND_TABLE', ++$i);
define('HOOK_ADMIN_HEAD_END', ++$i);
define('HOOK_ADMIN_HEAD_START', ++$i);
define('HOOK_ADMIN_BODY_START', ++$i);
define('HOOK_ADMIN_BODY_END', ++$i);
define('HOOK_ADMIN_BEFORE_PAGE', ++$i);
define('HOOK_ADMIN_MENU', ++$i);
define('HOOK_ADMIN_NEWS_ADD_PRE', ++$i);
define('HOOK_ADMIN_NEWS_ADD', ++$i);
define('HOOK_ADMIN_NEWS_UPDATE_PRE', ++$i);
define('HOOK_ADMIN_NEWS_UPDATE', ++$i);
define('HOOK_ADMIN_NEWS_DELETE_PRE', ++$i);
define('HOOK_ADMIN_NEWS_DELETE', ++$i);
define('HOOK_ADMIN_NEWS_TOGGLE_HIDE_PRE', ++$i);
define('HOOK_ADMIN_NEWS_TOGGLE_HIDE', ++$i);
define('HOOK_ADMIN_LOGIN_AFTER_ACCOUNT', ++$i);
define('HOOK_ADMIN_LOGIN_AFTER_PASSWORD', ++$i);
define('HOOK_ADMIN_LOGIN_AFTER_SIGN_IN', ++$i);
define('HOOK_ADMIN_ACCOUNTS_SAVE_POST', ++$i);
define('HOOK_ADMIN_SETTINGS_BEFORE_SAVE', ++$i);
define('HOOK_CRONJOB', ++$i);
define('HOOK_EMAIL_CONFIRMED', ++$i);
define('HOOK_GUILDS_BEFORE_GUILD_HEADER', ++$i);
define('HOOK_GUILDS_AFTER_GUILD_HEADER', ++$i);
define('HOOK_GUILDS_AFTER_GUILD_INFORMATION', ++$i);
define('HOOK_GUILDS_AFTER_GUILD_MEMBERS', ++$i);
define('HOOK_GUILDS_AFTER_INVITED_CHARACTERS', ++$i);
define('HOOK_TWIG', ++$i);
define('HOOK_CACHE_CLEAR', ++$i);
define('HOOK_INSTALL_FINISH', ++$i);
define('HOOK_INSTALL_FINISH_END', ++$i);

// hook filters
define('HOOK_FILTER_TWIG_DISPLAY', ++$i);
define('HOOK_FILTER_TWIG_RENDER', ++$i);
define('HOOK_FILTER_THEME_FOOTER', ++$i);

const HOOK_FIRST = HOOK_STARTUP;
define('HOOK_LAST', $i);

function is_sub_dir($path = NULL, $parent_folder = BASE): bool|string
{
	//Get directory path minus last folder
	$dir = dirname($path);
	$folder = substr($path, strlen($dir));

	//Check the base dir is valid
	$dir = realpath($dir);

	//Only allow valid filename characters
	$folder = preg_replace('/[^a-z0-9\.\-_]/i', '', $folder);

	//If this is a bad path or a bad end folder name
	if( !$dir OR !$folder OR $folder === '.') {
		return false;
	}

	//Rebuild path
	$path = $dir. '/' . $folder;

	//If this path is higher than the parent folder
	if( strcasecmp($path, $parent_folder) > 0 ) {
		return $path;
	}

	return false;
}
