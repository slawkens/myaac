<?php
/**
 * CHANGELOG modifier
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @author    Lee
 * @copyright 2020 MyAAC
 * @link      https://my-aac.org
 */

use MyAAC\Models\Changelog as ModelsChangelog;

defined('MYAAC') or die('Direct access not allowed!');

if (!hasFlag(FLAG_CONTENT_PAGES) && !superAdmin()) {
	echo 'Access denied.';
	return;
}

$title = 'Changelog';
$use_datatable = true;
const CL_LIMIT = 600; // maximum changelog body length
?>

<link rel="stylesheet" type="text/css" href="<?php echo BASE_URL; ?>tools/css/jquery.datetimepicker.css"/ >
<script src="<?php echo BASE_URL; ?>tools/js/jquery.datetimepicker.js"></script>
<?php
$id = $_GET['id'] ?? 0;
require_once LIBS . 'changelog.php';

if(!empty($action))
{
	$id = $_REQUEST['id'] ?? null;
	$body = isset($_REQUEST['body']) ? stripslashes($_REQUEST['body']) : null;
	$create_date = isset($_REQUEST['createdate']) ? (int)strtotime($_REQUEST['createdate'] ): null;
	$player_id = isset($_REQUEST['player_id']) ? (int)$_REQUEST['player_id'] : null;
	$type = isset($_REQUEST['type']) ? (int)$_REQUEST['type'] : null;
	$where = isset($_REQUEST['where']) ? (int)$_REQUEST['where'] : null;

	$errors = array();

	if($action == 'new') {

		if(isset($body) && Changelog::add($body, $type, $where, $player_id, $create_date, $errors)) {
			$body = '';
			$type = $where = $player_id = $create_date = 0;

			success("Added successful.");
		}
	}
	else if($action == 'delete') {
		Changelog::delete($id, $errors);
		success("Deleted successful.");
	}
	else if($action == 'edit')
	{
		if(isset($id) && !isset($body)) {
			$cl = Changelog::get($id);
			$body = $cl['body'];
			$type = $cl['type'];
			$where = $cl['where'];
			$create_date = $cl['date'];
			$player_id = $cl['player_id'];
		}
		else {
			if(Changelog::update($id, $body, $type, $where, $player_id, $create_date,$errors)) {
				$action = $body = '';
				$type = $where = $player_id = $create_date = 0;

				success("Updated successful.");
			}
		}
	}
	else if($action == 'hide') {
		Changelog::toggleHidden($id, $errors, $status);
		success(($status == 1 ? 'Show' : 'Hide') . " successful.");
	}

	if(!empty($errors))
		error(implode(", ", $errors));
}

$changelogs = ModelsChangelog::orderBy('id')->get()->toArray();

$i = 0;

$log_type = [
	['id' => 1, 'icon' => 'added'],
	['id' => 2, 'icon' => 'removed'],
	['id' => 3, 'icon' => 'changed'],
	['id' => 4, 'icon' => 'fixed'],
];

$log_where = [
	['id' => 1, 'icon' => 'server'],
	['id' => 2, 'icon' => 'website'],
];

foreach($changelogs as $key => &$log)
{
	$log['type'] = getChangelogType($log['type']);
	$log['where'] = getChangelogWhere($log['where']);
}

if($action == 'edit' || $action == 'new') {
	if($action == 'edit') {
		$player = new OTS_Player();
		$player->load($player_id);
	}

	$account_players = $account_logged->getPlayersList();
	$account_players->orderBy('group_id', POT::ORDER_DESC);
	$twig->display('admin.changelog.form.html.twig', array(
		'action' => $action,
		'cl_link_form' => constant('ADMIN_URL').'?p=changelog&action=' . ($action == 'edit' ? 'edit' : 'new'),
		'cl_id' => $id ?? null,
		'body' => isset($body) ? escapeHtml($body) : '',
		'create_date' => $create_date ?? '',
		'player_id' => $player_id ?? null,
		'account_players' => $account_players,
		'type' => $type ?? 0,
		'where' => $where ?? 0,
		'log_type' => $log_type,
		'log_where' => $log_where,
	));
}
$twig->display('admin.changelog.html.twig', array(
	'changelogs' => $changelogs,
));

?>
<script>
	$(document).ready(function () {
		$('#createdate').datetimepicker({format: "M d Y, H:i:s",});

		$('.tb_datatable').DataTable({
			"order": [[0, "desc"]],
			"columnDefs": [{targets: [1, 2,4,5],orderable: false}]
		});
	});
</script>
