<?php
/**
 * @var OTS_DB_MySQL $db
 */

$up = function () use ($db) {
	if(!$db->hasColumn(TABLE_PREFIX . 'monsters', 'elements')) {
		$db->addColumn(TABLE_PREFIX . 'monsters', 'elements', "TEXT NOT NULL AFTER `immunities`");
	}

	if(!$db->hasColumn(TABLE_PREFIX . 'monsters', 'pushable')) {
		$db->addColumn(TABLE_PREFIX . 'monsters', 'pushable', "TINYINT(1) NOT NULL DEFAULT '0' AFTER `convinceable`");
	}

	if(!$db->hasColumn(TABLE_PREFIX . 'monsters', 'canpushitems')) {
		$db->addColumn(TABLE_PREFIX . 'monsters', 'canpushitems', "TINYINT(1) NOT NULL DEFAULT '0' AFTER `pushable`");
	}

	if(!$db->hasColumn(TABLE_PREFIX . 'monsters', 'canpushcreatures')) {
		$db->addColumn(TABLE_PREFIX . 'monsters', 'canpushcreatures', "TINYINT(1) NOT NULL DEFAULT '0' AFTER `canpushitems`");
	}

	if(!$db->hasColumn(TABLE_PREFIX . 'monsters', 'canwalkonenergy')) {
		$db->addColumn(TABLE_PREFIX . 'monsters', 'canwalkonenergy', "TINYINT(1) NOT NULL DEFAULT '0' AFTER `canpushitems`");
	}

	if(!$db->hasColumn(TABLE_PREFIX . 'monsters', 'canwalkonpoison')) {
		$db->addColumn(TABLE_PREFIX . 'monsters', 'canwalkonpoison', "TINYINT(1) NOT NULL DEFAULT '0' AFTER `canwalkonenergy`");
	}

	if(!$db->hasColumn(TABLE_PREFIX . 'monsters', 'canwalkonfire')) {
		$db->addColumn(TABLE_PREFIX . 'monsters', 'canwalkonfire', "TINYINT(1) NOT NULL DEFAULT '0' AFTER `canwalkonpoison`");
	}

	if(!$db->hasColumn(TABLE_PREFIX . 'monsters', 'runonhealth')) {
		$db->addColumn(TABLE_PREFIX . 'monsters', 'runonhealth', "TINYINT(1) NOT NULL DEFAULT '0' AFTER `canwalkonfire`");
	}

	if(!$db->hasColumn(TABLE_PREFIX . 'monsters', 'hostile')) {
		$db->addColumn(TABLE_PREFIX . 'monsters', 'hostile', "TINYINT(1) NOT NULL DEFAULT '0' AFTER `runonhealth`");
	}

	if(!$db->hasColumn(TABLE_PREFIX . 'monsters', 'attackable')) {
		$db->addColumn(TABLE_PREFIX . 'monsters', 'attackable', "TINYINT(1) NOT NULL DEFAULT '0' AFTER `hostile`");
	}

	if(!$db->hasColumn(TABLE_PREFIX . 'monsters', 'rewardboss')) {
		$db->addColumn(TABLE_PREFIX . 'monsters', 'rewardboss', "TINYINT(1) NOT NULL DEFAULT '0' AFTER `attackable`");
	}

	if(!$db->hasColumn(TABLE_PREFIX . 'monsters', 'defense')) {
		$db->addColumn(TABLE_PREFIX . 'monsters', 'defense', "INT(11) NOT NULL DEFAULT '0' AFTER `rewardboss`");
	}

	if(!$db->hasColumn(TABLE_PREFIX . 'monsters', 'armor')) {
		$db->addColumn(TABLE_PREFIX . 'monsters', 'armor', "INT(11) NOT NULL DEFAULT '0' AFTER `defense`");
	}

	if(!$db->hasColumn(TABLE_PREFIX . 'monsters', 'summons')) {
		$db->addColumn(TABLE_PREFIX . 'monsters', 'summons', "TEXT NOT NULL AFTER `loot`");
	}
};

$down = function () use ($db) {
	if($db->hasColumn(TABLE_PREFIX . 'monsters', 'elements')) {
		$db->dropColumn(TABLE_PREFIX . 'monsters', 'elements');
	}

	if($db->hasColumn(TABLE_PREFIX . 'monsters', 'pushable')) {
		$db->dropColumn(TABLE_PREFIX . 'monsters', 'pushable');
	}

	if($db->hasColumn(TABLE_PREFIX . 'monsters', 'canpushitems')) {
		$db->dropColumn(TABLE_PREFIX . 'monsters', 'canpushitems');
	}

	if($db->hasColumn(TABLE_PREFIX . 'monsters', 'canpushcreatures')) {
		$db->dropColumn(TABLE_PREFIX . 'monsters', 'canpushcreatures');
	}

	if($db->hasColumn(TABLE_PREFIX . 'monsters', 'canwalkonenergy')) {
		$db->dropColumn(TABLE_PREFIX . 'monsters', 'canwalkonenergy');
	}

	if($db->hasColumn(TABLE_PREFIX . 'monsters', 'canwalkonpoison')) {
		$db->dropColumn(TABLE_PREFIX . 'monsters', 'canwalkonpoison');
	}

	if($db->hasColumn(TABLE_PREFIX . 'monsters', 'canwalkonfire')) {
		$db->dropColumn(TABLE_PREFIX . 'monsters', 'canwalkonfire');
	}

	if($db->hasColumn(TABLE_PREFIX . 'monsters', 'runonhealth')) {
		$db->dropColumn(TABLE_PREFIX . 'monsters', 'runonhealth');
	}

	if($db->hasColumn(TABLE_PREFIX . 'monsters', 'hostile')) {
		$db->dropColumn(TABLE_PREFIX . 'monsters', 'hostile');
	}

	if($db->hasColumn(TABLE_PREFIX . 'monsters', 'attackable')) {
		$db->dropColumn(TABLE_PREFIX . 'monsters', 'attackable');
	}

	if($db->hasColumn(TABLE_PREFIX . 'monsters', 'rewardboss')) {
		$db->dropColumn(TABLE_PREFIX . 'monsters', 'rewardboss');
	}

	if($db->hasColumn(TABLE_PREFIX . 'monsters', 'defense')) {
		$db->dropColumn(TABLE_PREFIX . 'monsters', 'defense');
	}

	if($db->hasColumn(TABLE_PREFIX . 'monsters', 'armor')) {
		$db->dropColumn(TABLE_PREFIX . 'monsters', 'armor');
	}

	if($db->hasColumn(TABLE_PREFIX . 'monsters', 'summons')) {
		$db->dropColumn(TABLE_PREFIX . 'monsters', 'summons');
	}
};

