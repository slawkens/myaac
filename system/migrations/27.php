<?php

$downloadsPage = <<<HTML
<p>&nbsp;</p>
<p>&nbsp;</p>
<div style="text-align: center;">We're using official Tibia Client <strong>{{ config.client / 100 }}</strong><br>
<p>Download Tibia Client <strong>{{ config.client / 100 }}</strong>&nbsp;for Windows <a href="https://drive.google.com/drive/folders/0B2-sMQkWYzhGSFhGVlY2WGk5czQ" target="_blank" rel="noopener">HERE</a>.</p>
<h2>IP Changer:</h2>
<a href="https://static.otland.net/ipchanger.exe" target="_blank" rel="noopener">HERE</a></div>
HTML;

$query = $db->query("SELECT `id` FROM `" . TABLE_PREFIX . "pages` WHERE `name` LIKE " . $db->quote('downloads') . " LIMIT 1;");
if($query->rowCount() === 0) {
	$db->exec("INSERT INTO `myaac_pages` (`id`, `name`, `title`, `body`, `date`, `player_id`, `php`, `access`, `hide`) VALUES
	(null, 'downloads', 'Downloads', $downloadsPage, 0, 1, 0, 0, 0);");
}

$commandsPage = <<<HTML
<table class="myaac-table" style="border-collapse: collapse; width: 100%; height: 72px; border-width: 1px;" border="1"><colgroup><col style="width: 50%;"><col style="width: 50%;"></colgroup>
<thead>
<tr style="height: 18px;">
<td style="height: 18px; border-width: 1px; text-align: center;"><span style="color: #ffffff;"><strong>Words</strong></span></td>
<td style="height: 18px; border-width: 1px; text-align: center;"><strong>Description</strong></td>
</tr>
</thead>
<tbody>
<tr style="height: 18px;">
<td style="height: 18px; border-width: 1px;">!example</td>
<td style="height: 18px; border-width: 1px;">This is just an example</td>
</tr>
<tr style="height: 18px;">
<td style="height: 18px; border-width: 1px;">!buyhouse</td>
<td style="height: 18px; border-width: 1px;">Buy house you are looking at</td>
</tr>
<tr style="height: 18px;">
<td style="height: 18px; border-width: 1px;"><em>!aol</em></td>
<td style="height: 18px; border-width: 1px;">Buy AoL</td>
</tr>
</tbody>
</table>
HTML;

$query = $db->query("SELECT `id` FROM `" . TABLE_PREFIX . "pages` WHERE `name` LIKE " . $db->quote('commands') . " LIMIT 1;");
if($query->rowCount() === 0) {
	$db->exec("INSERT INTO `myaac_pages` (`id`, `name`, `title`, `body`, `date`, `player_id`, `php`, `access`, `hide`) VALUES
(null, 'commands', 'Commands', $commandsPage, 0, 1, 0, 0, 0);");
}
