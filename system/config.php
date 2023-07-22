<?php
/**
 * This is MyAAC's Main Configuration file
 *
 * All the default values are kept here, you should not modify it but use
 * a config.local.php file instead to override the settings from here.
 *
 * This is a piece of PHP code so PHP syntax applies!
 * For boolean values please use true/false.
 *
 * Minimally 'server_path' directive have to be filled, other options are optional.
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2019 MyAAC
 * @link      https://my-aac.org
 */

// TODO:
// this file will be deleted, once all migrated to settings
$config = array(
	'account_mail_block_plus_sign' => true, // block email with '+' signs like test+box@gmail.com (help protect against spamming accounts)
	'account_change_character_name' => false, // can user change their character name for premium points?
	'account_change_character_name_points' => 30, // cost of name change
	'account_change_character_sex' => false, // can user change their character sex for premium points?
	'account_change_character_sex_points' => 30, // cost of sex change
	'characters_per_account' => 10,	// max. number of characters per account

	//
	'generate_new_reckey' => true,				// let player generate new recovery key, he will receive e-mail with new rec key (not display on page, hacker can't generate rec key)
	'generate_new_reckey_price' => 20,			// price for new recovery key

	// new character config
	'character_samples' => array( // vocations, format: ID_of_vocation => 'Name of Character to copy'
		//0 => 'Rook Sample',
		1 => 'Sorcerer Sample',
		2 => 'Druid Sample',
		3 => 'Paladin Sample',
		4 => 'Knight Sample'
	),

	'use_character_sample_skills' => false,

	// it must show limited number of players after using search in character page
	'characters_search_limit' => 15,

	// town list used when creating character
	// won't be displayed if there is only one item (rookgaard for example)
	'character_towns' => array(1),

	// characters length
	// This is the minimum and the maximum length that a player can create a character. It is highly recommend the maximum length to be 21.
	'character_name_min_length' => 4,
	'character_name_max_length' => 21,
	'character_name_npc_check' => true,

	// list of towns
	// if you use TFS 1.3 with support for 'towns' table in database, then you can ignore this - it will be configured automatically (from MySQL database - Table - towns)
	// otherwise it will try to load from your .OTBM map file
	// if you don't see towns on website, then you need to fill this out
	'towns' => array(
		0 => 'No town',
		1 => 'Sample town'
	),

	// characters page
	'characters' => array( // what things to display on character view page (true/false in each option)
		'level' => true,
		'experience' => false,
		'magic_level' => false,
		'balance' => false,
		'marriage_info' => true, // only 0.3
		'outfit' => true,
		'creation_date' => true,
		'quests' => true,
		'skills' => true,
		'equipment' => true,
		'frags' => false,
		'deleted' => false, // should deleted characters from same account be still listed on the list of characters? When enabled it will show that character is "[DELETED]"
	),
	'quests' => array(
		//'Some Quest' => 123,
		//'Some Quest Two' => 456,
	), // quests list (displayed in character view), name => storage

	// other
	'email_lai_sec_interval' => 60, // time in seconds between e-mails to one account from lost account interface, block spam
	'google_analytics_id' => '', // e.g.: UA-XXXXXXX-X

	'npc' => array()
);
