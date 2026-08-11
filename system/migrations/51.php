<?php

$up = function () use ($db) {
	$db->modifyColumn(TABLE_PREFIX . 'config', 'name', "varchar(255) NOT NULL");
	$db->modifyColumn(TABLE_PREFIX . 'config', 'value', "varchar(10000) NOT NULL");
};

$down = function () {
	// nothing to do, to not lose data
};
