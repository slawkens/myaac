<?php
/**
 * Spells
 *
 * @package   MyAAC
 * @author    Gesior <jerzyskalski@wp.pl>
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2019 MyAAC
 * @link      https://my-aac.org
 */

use MyAAC\Models\Spell;

defined('MYAAC') or die('Direct access not allowed!');
$title = 'Spells';

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

$order = 'name';
$spells = array();
$spells_db = Spell::where('hidden', '!=', 1)->where('type', '<', 4)->orderBy($order)->get();

if((string)$vocation_id != 'all') {
	foreach($spells_db as $spell) {
		$spell_vocations = json_decode($spell['vocations'], true);
		if(in_array($vocation_id, $spell_vocations) || count($spell_vocations) == 0) {
			$spell['vocations'] = null;
			$spells[] = $spell;
		}
	}
}
else {
	foreach($spells_db as $spell) {
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

?>
<link rel="stylesheet" href="<?php echo BASE_URL; ?>tools/css/datatables.min.css">
<?php
$twig->display('spells.html.twig', array(
	'post_vocation_id' => $vocation_id,
	'post_vocation' => $vocation,
	'spells' => $spells,
	'item_path' => setting('core.item_images_url'),
));
?>

<script>
	$(document).ready( function () {
		$("#tb_instantSpells").DataTable();
		$("#tb_conjureSpells").DataTable();
		$("#tb_runeSpells").DataTable();
	} );

</script>
<script src="<?php echo BASE_URL; ?>tools/js/datatables.min.js"></script>
