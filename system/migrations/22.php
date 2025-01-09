<?php
/**
 * @var OTS_DB_MySQL $db
 */

$up = function () use ($db) {
	if (!$db->hasTable('z_polls')) {
		$db->exec(file_get_contents(__DIR__ . '/22-z_polls.sql'));
	}

	if (!$db->hasTable('z_polls_answers')) {
		$db->exec(file_get_contents(__DIR__ . '/22-z_polls_answers.sql'));
	}

	if (!$db->hasColumn('accounts', 'vote')) {
		$db->addColumn('accounts', 'vote', 'int(11) NOT NULL DEFAULT 0');
	}
	else {
		$db->modifyColumn('accounts', 'vote', 'int(11) NOT NULL DEFAULT 0');
	}
};

$down = function () use ($db) {
	if ($db->hasTable('z_polls')) {
		$db->dropTable('z_polls;');
	}

	if ($db->hasTable('z_polls_answers')) {
		$db->dropTable('z_polls_answers');
	}

	if ($db->hasColumn('accounts', 'vote')) {
		$db->dropColumn('accounts', 'vote');
	}
};
