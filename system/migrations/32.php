<?php
// Increase size of page in myaac_visitors table

$up = function () use ($db) {
	$db->exec('ALTER TABLE `' . TABLE_PREFIX . "visitors` MODIFY `page` VARCHAR(2048) NOT NULL;");
};

$down = function () {
	// nothing to be done, as we have just extended the size of a column
};
