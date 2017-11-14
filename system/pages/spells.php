<?php
/**
 * Spells
 *
 * @package   MyAAC
 * @author    Gesior <jerzyskalski@wp.pl>
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2017 MyAAC
 * @link      http://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');
$title = 'Spells';

$canEdit = hasFlag(FLAG_CONTENT_SPELLS) || admin();
if(isset($_POST['reload_spells']) && $canEdit)
{
	require LIBS . 'spells.php';
	if(!Spells::loadFromXML(true)) {
		error(Spells::getLastError());
	}
}

if(isset($_REQUEST['vocation_id'])) {
	$vocation_id = $_REQUEST['vocation_id'];
	if($vocation_id == 'all') {
		$vocation = 'all';
	}
	else {
		$vocation = $config['vocations'][$vocation_id];
	}
}
else {
	$vocation = (isset($_REQUEST['vocation']) ? urldecode($_REQUEST['vocation']) : 'all');
	
	if($vocation == 'all') {
		$vocation_id = 'all';
	}
	else {
		$vocation_ids = array_flip($config['vocations']);
		$vocation_id = $vocation_ids[$vocation];
	}
}

$order = 'words';
if(isset($_REQUEST['order']))
	$order = $_REQUEST['order'];

if(!in_array($order, array('words', 'type', 'mana', 'level', 'maglevel', 'soul')))
	$order = 'level';

$spells = array();
$spells_db = $db->query('SELECT * FROM `' . TABLE_PREFIX . 'spells` WHERE `hidden` != 1 AND `type` < 3 ORDER BY ' . $order . ', level');

if((string)$vocation_id != 'all') {
	foreach($spells_db->fetchAll() as $spell) {
		$spell_vocations = json_decode($spell['vocations'], true);
		if(in_array($vocation_id, $spell_vocations) || count($spell_vocations) == 0) {
			$spell['vocations'] = null;
			$spells[] = $spell;
		}
	}
}
else {
	foreach($spells_db->fetchAll() as $spell) {
		$vocations = json_decode($spell['vocations'], true);
		
		foreach($vocations as &$tmp_vocation) {
			if(isset($config['vocations'][$tmp_vocation]))
				$tmp_vocation = $config['vocations'][$tmp_vocation];
			else
				$tmp_vocation = 'Unknown';
		}
		
		$spell['vocations'] = implode('<br/>', $vocations);
		$spells[] = $spell;
	}
}

echo $twig->render('spells.html.twig', array(
	'canEdit' => $canEdit,
	'post_vocation_id' => $vocation_id,
	'post_vocation' => $vocation,
	'post_order' => $order,
	'spells' => $spells,
));
?>
