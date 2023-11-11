<?php
/**
 * Players editor
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2019 MyAAC
 * @link      https://my-aac.org
 */

use MyAAC\Models\Player;

defined('MYAAC') or die('Direct access not allowed!');

$title = 'Player editor';

csrfProtect();

$player_base = ADMIN_URL . '?p=players';

$use_datatable = true;
require_once LIBS . 'forum.php';

$skills = array(
	POT::SKILL_FIST => array('Fist fighting', 'fist'),
	POT::SKILL_CLUB => array('Club fighting', 'club'),
	POT::SKILL_SWORD => array('Sword fighting', 'sword'),
	POT::SKILL_AXE => array('Axe fighting', 'axe'),
	POT::SKILL_DIST => array('Distance fighting', 'dist'),
	POT::SKILL_SHIELD => array('Shielding', 'shield'),
	POT::SKILL_FISH => array('Fishing', 'fish')
);

$hasBlessingsColumn = $db->hasColumn('players', 'blessings');
$hasBlessingColumn = $db->hasColumn('players', 'blessings1');
$hasLookAddons = $db->hasColumn('players', 'lookaddons');

$skull_type = array("None", "Yellow", "Green", "White", "Red", "Black", "Orange");
?>

<link rel="stylesheet" type="text/css" href="<?php echo BASE_URL; ?>tools/css/jquery.datetimepicker.css"/ >
<script src="<?php echo BASE_URL; ?>tools/js/jquery.datetimepicker.js"></script>

<?php
$id = 0;
$search_player = '';
if (isset($_REQUEST['id']))
	$id = (int)$_REQUEST['id'];
else if (isset($_REQUEST['search'])) {
	$search_player = $_REQUEST['search'];
	if (strlen($search_player) < 3 && !Validator::number($search_player)) {
		echo_error('Player name is too short.');
	} else {
		$query = $db->query('SELECT `id` FROM `players` WHERE `name` = ' . $db->quote($search_player));
		if ($query->rowCount() == 1) {
			$query = $query->fetch();
			$id = (int)$query['id'];
		} else {
			$query = $db->query('SELECT `id`, `name` FROM `players` WHERE `name` LIKE ' . $db->quote('%' . $search_player . '%'));
			if ($query->rowCount() > 0 && $query->rowCount() <= 10) {
				$str_construct = 'Do you mean?<ul>';
				foreach ($query as $row)
					$str_construct .= '<li><a href="' . $player_base . '&id=' . $row['id'] . '">' . $row['name'] . '</a></li>';
				$str_construct .= '</ul>';
				echo_error($str_construct);
			} else if ($query->rowCount() > 10)
				echo_error('Specified name resulted with too many players.');
			else
				echo_error('No entries found.');
		}
	}
}
?>
<div class="row">
	<?php
	$groups = new OTS_Groups_List();
	if ($id > 0) {
		$player = new OTS_Player();
		$player->load($id);

		if ($player->isLoaded() && isset($_POST['save'])) {// we want to save
			$error = false;

			if ($player->isOnline())
				echo_error('This player is actually online. You can\'t edit online players.');

			$name = $_POST['name'];
			$_error = '';
			if (!Validator::characterName($name))
				echo_error(Validator::getLastError());

			//if(!Validator::newCharacterName($name)
			//	echo_error(Validator::getLastError());

			$player_db = new OTS_Player();
			$player_db->find($name);
			if ($player_db->isLoaded() && $player->getName() != $name)
				echo_error('This name is already used. Please choose another name!');

			$account_id = $_POST['account_id'];
			verify_number($account_id, 'Account id', 11);

			$account_db = new OTS_Account();
			$account_db->load($account_id);
			if (!$account_db->isLoaded())
				echo_error('Account with this id doesn\'t exist.');

			$group = $_POST['group'];
			if ($groups->getGroup($group) == false)
				echo_error('Group with this id doesn\'t exist');

			$level = $_POST['level'];
			verify_number($level, 'Level', 11);

			$experience = $_POST['experience'];
			verify_number($experience, 'Experience', 20);

			$vocation = $_POST['vocation'];
			verify_number($vocation, 'Vocation id', 11);

			if (!isset($config['vocations'][$vocation])) {
				echo_error("Vocation with this id doesn't exist.");
			}

			// health
			$health = $_POST['health'];
			verify_number($health, 'Health', 11);
			$health_max = $_POST['health_max'];
			verify_number($health_max, 'Health max', 11);

			// mana
			$magic_level = $_POST['magic_level'];
			verify_number($magic_level, 'Magic_level', 11);
			$mana = $_POST['mana'];
			verify_number($mana, 'Mana', 11);
			$mana_max = $_POST['mana_max'];
			verify_number($mana_max, 'Mana max', 11);
			$mana_spent = $_POST['mana_spent'];
			verify_number($mana_spent, 'Mana spent', 11);

			// look
			$look_body = $_POST['look_body'];
			verify_number($look_body, 'Look body', 11);
			$look_feet = $_POST['look_feet'];
			verify_number($look_feet, 'Look feet', 11);
			$look_head = $_POST['look_head'];
			verify_number($look_head, 'Look head', 11);
			$look_legs = $_POST['look_legs'];
			verify_number($look_legs, 'Look legs', 11);
			$look_type = $_POST['look_type'];
			verify_number($look_type, 'Look type', 11);
			if ($hasLookAddons) {
				$look_addons = $_POST['look_addons'];
				verify_number($look_addons, 'Look addons', 11);
			}

			// pos
			$pos_x = $_POST['pos_x'];
			verify_number($pos_x, 'Position x', 11);
			$pos_y = $_POST['pos_y'];
			verify_number($pos_y, 'Position y', 11);
			$pos_z = $_POST['pos_z'];
			verify_number($pos_z, 'Position z', 11);

			$soul = $_POST['soul'];
			verify_number($soul, 'Soul', 10);

			$town = $_POST['town'];
			verify_number($town, 'Town', 11);

			$capacity = $_POST['capacity'];
			verify_number($capacity, 'Capacity', 11);
			$sex = $_POST['sex'];
			verify_number($sex, 'Sex', 1);

			$lastlogin = strtotime($_POST['lastlogin']);
			verify_number($lastlogin, 'Last login', 20);
			$lastlogout = strtotime($_POST['lastlogout']);
			verify_number($lastlogout, 'Last logout', 20);

			$skull = $_POST['skull'];
			verify_number($skull, 'Skull', 1);
			$skull_time = $_POST['skull_time'];
			verify_number($skull_time, 'Skull time', 11);

			if ($db->hasColumn('players', 'loss_experience')) {
				$loss_experience = $_POST['loss_experience'];
				verify_number($loss_experience, 'Loss experience', 11);
				$loss_mana = $_POST['loss_mana'];
				verify_number($loss_mana, 'Loss mana', 11);
				$loss_skills = $_POST['loss_skills'];
				verify_number($loss_skills, 'Loss skills', 11);
				$loss_containers = $_POST['loss_containers'];
				verify_number($loss_containers, 'Loss loss_containers', 11);
				$loss_items = $_POST['loss_items'];
				verify_number($loss_items, 'Loss items', 11);
			}
			if ($db->hasColumn('players', 'offlinetraining_time')) {
				$offlinetraining = $_POST['offlinetraining'];
				verify_number($offlinetraining, 'Offline Training time', 11);
			}

			if ($hasBlessingsColumn) {
				$blessings = $_POST['blessings'];
				verify_number($blessings, 'Blessings', 2);
			}

			$balance = $_POST['balance'];
			verify_number($balance, 'Balance', 20);
			if ($db->hasColumn('players', 'stamina')) {
				$stamina = $_POST['stamina'];
				verify_number($stamina, 'Stamina', 20);
			}

			$deleted = (isset($_POST['deleted']) && $_POST['deleted'] == 'true');
			$hidden = (isset($_POST['hidden']) && $_POST['hidden'] == 'true');

			$created = strtotime($_POST['created']);
			verify_number($created, 'Created', 11);

			$comment = isset($_POST['comment']) ? htmlspecialchars(stripslashes(substr($_POST['comment'], 0, 2000))) : NULL;

			foreach ($_POST['skills'] as $skill => $value)
				verify_number($value, $skills[$skill][0], 10);
			foreach ($_POST['skills_tries'] as $skill => $value)
				verify_number($value, $skills[$skill][0] . ' tries', 10);

			if ($hasBlessingColumn) {
				$bless_count = $_POST['blesscount'];
				for ($i = 1; $i <= $bless_count; $i++) {
					$a = 'blessing' . $i;
					${'blessing' . $i} = (isset($_POST[$a]) && $_POST[$a] == 'true');
				}
			}

			if (!$error) {
				$player->setName($name);
				$player->setAccount($account_db);
				$player->setGroup($groups->getGroup($group));
				$player->setLevel($level);
				$player->setExperience($experience);
				$player->setVocation($vocation);
				$player->setHealth($health);
				$player->setHealthMax($health_max);
				$player->setMagLevel($magic_level);
				$player->setMana($mana);
				$player->setManaMax($mana_max);
				$player->setManaSpent($mana_spent);
				$player->setLookBody($look_body);
				$player->setLookFeet($look_feet);
				$player->setLookHead($look_head);
				$player->setLookLegs($look_legs);
				$player->setLookType($look_type);
				if ($hasLookAddons)
					$player->setLookAddons($look_addons);
				if ($db->hasColumn('players', 'offlinetraining_time'))
					$player->setCustomField('offlinetraining_time', $offlinetraining);
				$player->setPosX($pos_x);
				$player->setPosY($pos_y);
				$player->setPosZ($pos_z);
				$player->setSoul($soul);
				$player->setTownId($town);
				$player->setCap($capacity);
				$player->setSex($sex);
				$player->setLastLogin($lastlogin);
				$player->setLastLogout($lastlogout);
				//$player->setLastIP(ip2long($lastip));
				$player->setSkull($skull);
				$player->setSkullTime($skull_time);
				if ($db->hasColumn('players', 'loss_experience')) {
					$player->setLossExperience($loss_experience);
					$player->setLossMana($loss_mana);
					$player->setLossSkills($loss_skills);
					$player->setLossContainers($loss_containers);
					$player->setLossItems($loss_items);
				}
				if ($db->hasColumn('players', 'blessings'))
					$player->setBlessings($blessings);

				if ($hasBlessingColumn) {
					for ($i = 1; $i <= $bless_count; $i++) {
						$a = 'blessing' . $i;
						$player->setCustomField('blessings' . $i, ${'blessing' . $i} ? '1' : '0');
					}
				}
				$player->setBalance($balance);
				if ($db->hasColumn('players', 'stamina'))
					$player->setStamina($stamina);
				if ($db->hasColumn('players', 'deletion'))
					$player->setCustomField('deletion', $deleted ? '1' : '0');
				else
					$player->setCustomField('deleted', $deleted ? '1' : '0');
				$player->setCustomField('hidden', $hidden ? '1' : '0');
				$player->setCustomField('created', $created);
				if (isset($comment))
					$player->setCustomField('comment', $comment);

				foreach ($_POST['skills'] as $skill => $value) {
					$player->setSkill($skill, $value);
				}
				foreach ($_POST['skills_tries'] as $skill => $value) {
					$player->setSkillTries($skill, $value);
				}
				$player->save();
				echo_success('Player saved at: ' . date('G:i'));
				$player->load($id);
			}
		}
	} else if ($id == 0) {
		$players_db = $db->query('SELECT `id`, `name`, `level` FROM `players` ORDER BY `id` asc');
		?>
		<div class="col-12 col-sm-12 col-lg-10">
			<div class="card card-info card-outline">
				<div class="card-header">
					<h5 class="m-0">Players</h5>
				</div>
				<div class="card-body">
					<table class="player_datatable table table-striped table-bordered table-responsive d-md-table">
						<thead>
						<tr>
							<th>ID</th>
							<th>Name</th>
							<th>Level</th>
							<th style="width: 40px">Edit</th>
						</tr>
						</thead>
						<tbody>
						<?php foreach ($players_db as $player_db): ?>
							<tr>
								<th><?php echo $player_db['id']; ?></th>
								<td><?php echo $player_db['name']; ?></a></td>
								<td><?php echo $player_db['level']; ?></a></td>

								<td><a href="?p=players&id=<?php echo $player_db['id']; ?>" class="btn btn-success btn-sm" title="Edit">
										<i class="fas fa-pencil-alt"></i>
									</a>
								</td>
							</tr>
						<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	<?php } ?>

	<?php
	if (isset($player) && $player->isLoaded()) {
		$account = $player->getAccount();
		?>
		<div class="col-12 col-sm-12 col-lg-10">
			<div class="card card-primary card-outline card-outline-tabs">
				<div class="card-header p-0 border-bottom-0">
					<ul class="nav nav-tabs" id="tabs-tab" role="tablist">
						<li class="nav-item">
							<a class="nav-link active" id="tabs-home-tab" data-toggle="pill" href="#tabs-home">Player</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" id="tabs-home-tab" data-toggle="pill" href="#tabs-stats">Stats</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" id="tabs-home-tab" data-toggle="pill" href="#tabs-skills">Skills</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" id="tabs-home-tab" data-toggle="pill" href="#tabs-pos">Pos/Look</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" id="tabs-home-tab" data-toggle="pill" href="#tabs-misc">Misc</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" id="tabs-posts-tab" data-toggle="pill" href="#tabs-posts">Posts</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" id="tabs-chars-tab" data-toggle="pill" href="#tabs-chars">Characters</a>
						</li>
					</ul>
				</div>
				<form action="<?php echo $player_base . ((isset($id) && $id > 0) ? '&id=' . $id : ''); ?>" method="post">
					<?php csrf(); ?>
					<div class="card-body">
						<div class="tab-content" id="tabs-tabContent">
							<div class="tab-pane fade active show" id="tabs-home">
								<div class="form-group row">
									<div class="col-12 col-sm-12 col-lg-6">
										<label for="name" class="control-label">Name</label>
										<input type="text" class="form-control" id="name" name="name" autocomplete="off" value="<?php echo $player->getName(); ?>"/>
									</div>
									<div class="col-12 col-sm-12 col-lg-6">
										<label for="account_id">Account id:</label>
										<input type="text" class="form-control" id="account_id" name="account_id" autocomplete="off" size="8" maxlength="11" value="<?php echo $account->getId(); ?>"/>
									</div>
								</div>
								<div class="form-group row">
									<div class="col-12 col-sm-12 col-lg-6">
										<label for="group">Group:</label>
										<select name="group" id="group" class="form-control custom-select">
											<?php foreach ($groups->getGroups() as $_id => $group): ?>
												<option value="<?php echo $_id; ?>" <?php echo($player->getGroup()->getId() == $_id ? 'selected' : ''); ?>><?php echo $group->getName(); ?></option>
											<?php endforeach; ?>
										</select>
									</div>
									<div class="col-12 col-sm-12 col-lg-6">
										<label for="vocation">Vocation</label>
										<select name="vocation" id="vocation" class="form-control custom-select">
											<?php
											foreach ($config['vocations'] as $_id => $name) {
												echo '<option value=' . $_id . ($_id == $player->getVocation() ? ' selected' : '') . '>' . $name . '</option>';
											}
											?>
										</select>
									</div>
								</div>
								<div class="form-group row">
									<div class="col-12 col-sm-12 col-lg-6">
										<label for="sex">Sex:</label>
										<select name="sex" id="sex" class="form-control custom-select">>
											<?php foreach ($config['genders'] as $_id => $sex): ?>
												<option value="<?php echo $_id; ?>" <?php echo($player->getSex() == $_id ? 'selected' : ''); ?>><?php echo strtolower($sex); ?></option>
											<?php endforeach; ?>
										</select>
									</div>
									<div class="col-12 col-sm-12 col-lg-6">
										<label for="town">Town:</label>
										<select name="town" id="town" class="form-control">
											<?php
											$configTowns = config('towns');
											if (!isset($configTowns[$player->getTownId()])) {
												$configTowns[$player->getTownId()] = 'Unknown Town';
											}

											foreach ($configTowns as $_id => $town): ?>
												<option value="<?php echo $_id; ?>" <?php echo($player->getTownId() == $_id ? 'selected' : ''); ?>><?php echo $town; ?></option>
											<?php endforeach; ?>
										</select>
									</div>
								</div>
								<div class="form-group row">
									<div class="col-12 col-sm-12 col-lg-6">
										<label for="skull">Skull:</label>
										<select name="skull" id="skull" class="form-control custom-select">
											<?php

											foreach ($skull_type as $_id => $s_name) {
												echo '<option value=' . $_id . ($_id == $player->getSkull() ? ' selected' : '') . '>' . $s_name . '</option>';
											}
											?>
										</select>
									</div>
									<div class="col-12 col-sm-12 col-lg-6">
										<label for="skull_time">Skull time:</label>
										<input type="text" class="form-control" id="skull_time" name="skull_time"
											   autocomplete="off" maxlength="11"
											   value="<?php echo $player->getSkullTime(); ?>"/>
									</div>
								</div>
								<div class="form-group row">
									<?php if ($hasBlessingColumn):
										$bless_count = $player->countBlessings();
										$bless = $player->checkBlessings($bless_count); ?>
										<input type="hidden" name="blesscount" value="<?php echo $bless_count; ?>"/>
										<div class="col-12 col-sm-12 col-lg-6">
											<label>Blessings:</label><br/>
											<?php for ($i = 1; $i <= $bless_count; $i++): ?>
												<label><input class="" type="checkbox" name="blessing<?php echo $i; ?>" id="blessing<?php echo $i; ?>" value="true"<?php echo(($bless[$i - 1] == 1) ? ' checked' : '') ?>/><?php echo $i; ?></label>
											<?php endfor ?>
										</div>
									<?php endif; ?>
									<?php if ($hasBlessingsColumn): ?>
										<div class="col-12 col-sm-12 col-lg-6">
											<label for="blessings">Blessings:</label>
											<input type="text" class="form-control" id="blessings" name="blessings" autocomplete="off" maxlength="11" value="<?php echo $player->getBlessings(); ?>"/>
										</div>
									<?php endif; ?>
									<div class="col-12 col-sm-12 col-lg-6">
										<label for="balance" class="control-label">Bank Balance:</label>
										<input type="text" class="form-control" id="balance" name="balance" autocomplete="off" maxlength="20" value="<?php echo $player->getBalance(); ?>"/>
									</div>
								</div>
								<div class="form-group row">
									<div class="col-12 col-sm-12 col-lg-6">
										<div class="custom-control custom-switch custom-switch-on-danger">
											<input type="checkbox" class="custom-control-input" name="deleted" id="deleted" value="true" <?php echo($player->getCustomField($db->hasColumn('players', 'deletion') ? 'deletion' : 'deleted') == '1' ? ' checked' : ''); ?>>
											<label class="custom-control-label" for="deleted">Deleted</label>
										</div>
									</div>
									<div class="col-12 col-sm-12 col-lg-6">
										<div class="custom-control custom-switch custom-switch-on-success">
											<input type="checkbox" class="custom-control-input" name="hidden" id="hidden" value="true" <?php echo($player->isHidden() ? ' checked' : ''); ?>>
											<label class="custom-control-label" for="hidden">Hidden</label>
										</div>
									</div>
								</div>
							</div>
							<div class="tab-pane fade" id="tabs-stats">
								<div class="form-group row">
									<div class="col-12 col-sm-12 col-lg-6">
										<label for="level" class="control-label">Level:</label>
										<input type="text" class="form-control" id="level" name="level" autocomplete="off" value="<?php echo $player->getLevel(); ?>"/>
									</div>
									<div class="col-12 col-sm-12 col-lg-6">
										<label for="experience" class="control-label">Experience:</label>
										<input type="text" class="form-control" id="experience" name="experience" autocomplete="off" value="<?php echo $player->getExperience(); ?>"/>
									</div>
								</div>
								<div class="form-group row">
									<div class="col-12 col-sm-12 col-lg-6">
										<label for="magic_level" class="control-label">Magic level:</label>
										<input type="text" class="form-control" id="magic_level" name="magic_level" autocomplete="off" size="8" maxlength="11" value="<?php echo $player->getMagLevel(); ?>"/>
									</div>
									<div class="col-12 col-sm-12 col-lg-6">
										<label for="mana_spent" class="control-label">Mana spent:</label>
										<input type="text" class="form-control" id="mana_spent" name="mana_spent" autocomplete="off" size="3" maxlength="11" value="<?php echo $player->getManaSpent(); ?>"/>
									</div>
								</div>
								<div class="form-group row">
									<div class="col-12 col-sm-12 col-lg-6">
										<label for="health" class="control-label">Health:</label>
										<input type="text" class="form-control" id="health" name="health" autocomplete="off" size="5" maxlength="11" value="<?php echo $player->getHealth(); ?>"/>
									</div>
									<div class="col-12 col-sm-12 col-lg-6">
										<label for="health_max" class="control-label">Health max:</label>
										<input type="text" class="form-control" id="health_max" name="health_max" autocomplete="off" size="5" maxlength="11" value="<?php echo $player->getHealthMax(); ?>"/>
									</div>
								</div>
								<div class="form-group row">
									<div class="col-12 col-sm-12 col-lg-6">
										<label for="mana" class="control-label">Mana:</label>
										<input type="text" class="form-control" id="mana" name="mana" autocomplete="off" size="3" maxlength="11" value="<?php echo $player->getMana(); ?>"/>
									</div>
									<div class="col-12 col-sm-12 col-lg-6">
										<label for="mana_max" class="control-label">Mana max:</label>
										<input type="text" class="form-control" id="mana_max" name="mana_max" autocomplete="off" size="3" maxlength="11" value="<?php echo $player->getManaMax(); ?>"/>
									</div>
								</div>
								<div class="form-group row">
									<div class="col-12 col-sm-12 col-lg-6">
										<label for="capacity" class="control-label">Capacity:</label>
										<input type="text" class="form-control" id="capacity" name="capacity" autocomplete="off" size="3" maxlength="11" value="<?php echo $player->getCap(); ?>"/>
									</div>
									<div class="col-12 col-sm-12 col-lg-6">
										<label for="soul" class="control-label">Soul:</label>
										<input type="text" class="form-control" id="soul" name="soul" autocomplete="off" size="3" maxlength="10" value="<?php echo $player->getSoul(); ?>"/>
									</div>
									<?php if ($db->hasColumn('players', 'stamina')): ?>
										<div class="col-12 col-sm-12 col-lg-6">
											<label for="stamina" class="control-label">Stamina:</label>
											<input type="text" class="form-control" id="stamina" name="stamina" autocomplete="off" maxlength="20" value="<?php echo $player->getStamina(); ?>"/>
										</div>
									<?php endif; ?>
									<?php if ($db->hasColumn('players', 'offlinetraining_time')): ?>
										<div class="col-12 col-sm-12 col-lg-6">
											<label for="offlinetraining" class="control-label">Offline Training
												Time:</label>
											<input type="text" class="form-control" id="offlinetraining" name="offlinetraining" autocomplete="off" maxlength="11" value="<?php echo $player->getCustomField('offlinetraining_time'); ?>"/>
										</div>
									<?php endif; ?>
								</div>
							</div>
							<div class="tab-pane fade" id="tabs-skills">
								<?php
								foreach ($skills as $_id => $info) {
									?>
									<div class="form-group row">
										<div class="col-12 col-sm-12 col-lg-6">
											<?php echo '<label for="skills[' . $_id . ']" class="control-label">' . $info[0] . '</label>
									<input type="text" class="form-control" id="skills[' . $_id . ']" name="skills[' . $_id . ']" maxlength="10" autocomplete="off" value="' . $player->getSkill($_id) . '"/>'; ?>
										</div>
										<div class="col-12 col-sm-12 col-lg-6">
											<?php echo '<label for="skills_tries[' . $_id . ']" class="control-label">' . $info[0] . ' tries</label>
									<input type="text" class="form-control" id="skills_tries[' . $_id . ']" name="skills_tries[' . $_id . ']" maxlength="10" autocomplete="off" value="' . $player->getSkillTries($_id) . '"/>'; ?>
										</div>
									</div>
								<?php } ?>
							</div>
							<div class="tab-pane fade" id="tabs-pos">
								<?php $outfit = setting('core.outfit_images_url') . '?id=' . $player->getLookType() . ($hasLookAddons ? '&addons=' . $player->getLookAddons() : '') . '&head=' . $player->getLookHead() . '&body=' . $player->getLookBody() . '&legs=' . $player->getLookLegs() . '&feet=' . $player->getLookFeet(); ?>
								<div id="imgchar" style="width:64px;height:64px;position:absolute; top:30px; right:30px">
									<img id="player_outfit" style="margin-left:0;margin-top:0;width:64px;height:64px;" src="<?php echo $outfit; ?>" alt="player outfit"/>
								</div>
								<td>Position:</td>
								<div class="form-group row">
									<div class="col-12 col-sm-12 col-lg-4">
										<label for="pos_x" class="control-label">X:</label>
										<input type="text" class="form-control" id="pos_x" name="pos_x" autocomplete="off" maxlength="11" value="<?php echo $player->getPosX(); ?>"/>
									</div>
									<div class="col-12 col-sm-12 col-lg-4">
										<label for="pos_y" class="control-label">Y:</label>
										<input type="text" class="form-control" id="pos_y" name="pos_y" autocomplete="off" maxlength="11" value="<?php echo $player->getPosY(); ?>"/>
									</div>
									<div class="col-12 col-sm-12 col-lg-4">
										<label for="pos_z" class="control-label">Z:</label>
										<input type="text" class="form-control" id="pos_z" name="pos_z" autocomplete="off" maxlength="11" value="<?php echo $player->getPosZ(); ?>"/>
									</div>
								</div>
								<td>Look:</td>
								<div class="form-group row">
									<div class="col-12 col-sm-12 col-lg-3">
										<label for="look_head" class="control-label">Head: <span id="look_head_val" class="font-weight-bold text-primary"></span></label>
										<input class="custom-range" type="range" min="0" max="132" id="look_head" name="look_head" value="<?php echo $player->getLookHead(); ?>"/>
									</div>
									<div class="col-12 col-sm-12 col-lg-3">
										<label for="look_body" class="control-label">Body: <span id="look_body_val" class="font-weight-bold text-primary"></span></label>
										<input type="range" min="0" max="132"
											   value="<?php echo $player->getLookBody(); ?>"
											   class="custom-range" id="look_body" name="look_body">
									</div>
									<div class="col-12 col-sm-12 col-lg-3">
										<label for="look_legs" class="control-label">Legs: <span id="look_legs_val" class="font-weight-bold text-primary"></span></label>
										<input type="range" min="0" max="132"
											   value="<?php echo $player->getLookLegs(); ?>"
											   class="custom-range" id="look_legs" name="look_legs">
									</div>
									<div class="col-12 col-sm-12 col-lg-3">
										<label for="look_feet" class="control-label">Feet: <span id="look_feet_val" class="font-weight-bold text-primary"></span></label>
										<input type="range" min="0" max="132"
											   value="<?php echo $player->getLookBody(); ?>"
											   class="custom-range" id="look_feet" name="look_feet">
									</div>
								</div>
								<div class="form-group row">
									<div class="col-12 col-sm-12 col-lg-6">
										<label for="look_type" class="control-label">Type:</label>
										<?php
										$outfitlist = null;
										$outfitlist = Outfits_loadfromXML();
										if ($outfitlist) { ?>
											<select name="look_type" id="look_type" class="form-control custom-select">
												<?php
												foreach ($outfitlist as $_id => $outfit) {
													if ($outfit['enabled'] == 'yes') ;
													echo '<option value=' . $outfit['id'] . ($outfit['id'] == $player->getLookType() ? ' selected' : '') . '>' . $outfit['name'] . ' - ' . ($outfit['type'] == 1 ? 'Male' : 'Female') . '</option>';
												}
												?>
											</select>
										<?php } else { ?>
											<input type="text" class="form-control" id="look_type" name="look_type" autocomplete="off" maxlength="11" value="<?php echo $player->getLookType(); ?>"/>
										<?php } ?>
									</div>
									<?php if ($hasLookAddons): ?>
										<div class="col-12 col-sm-12 col-lg-6">
											<label for="look_addons" class="control-label">Addons:</label>
											<select name="look_addons" id="look_addons" class="form-control custom-select">
												<?php
												$addon_type = array("None", "First", "Second", "Both");
												foreach ($addon_type as $_id => $s_name) {
													echo '<option value=' . $_id . ($_id == $player->getLookAddons() ? ' selected' : '') . '>' . $s_name . '</option>';
												}
												?>
											</select>
										</div>
									<?php endif; ?>
								</div>
							</div>
							<div class="tab-pane fade" id="tabs-misc">
								<div class="form-group row">
									<div class="col-12 col-sm-12 col-lg-6">
										<label for="created" class="control-label">Created:</label>
										<input type="text" class="form-control" id="created" name="created"
											   autocomplete="off"
											   maxlength="10"
											   value="<?php echo date("M d Y, H:i:s", $player->getCustomField('created')); ?>"/>
									</div>
									<div class="col-12 col-sm-12 col-lg-6">
										<label for="lastlogin" class="control-label">Last login:</label>
										<input type="text" class="form-control" id="lastlogin" name="lastlogin" autocomplete="off" maxlength="20" value="<?php echo date("M d Y, H:i:s", $player->getLastLogin()); ?>"/>
									</div>
									<div class="col-12 col-sm-12 col-lg-6">
										<label for="lastlogout" class="control-label">Last logout:</label>
										<input type="text" class="form-control" id="lastlogout" name="lastlogout" autocomplete="off" maxlength="20" value="<?php echo date("M d Y, H:i:s", $player->getLastLogout()); ?>"/>
									</div>
									<div class="col-12 col-sm-12 col-lg-6">
										<label for="lastip" class="control-label">Last IP:</label>
										<input type="text" class="form-control" id="lastip" name="lastip" autocomplete="off" maxlength="10" value="<?php
										if (strlen($player->getLastIP()) > 11) {
											echo inet_ntop($player->getLastIP());
										}
										else {
											echo longToIp($player->getLastIP());
										}
										?>" readonly/>
									</div>
								</div>
								<?php if ($db->hasColumn('players', 'loss_experience')): ?>
									<div class="form-group row">
										<div class="col-12 col-sm-12 col-lg-6">
											<label for="loss_experience" class="control-label">Experience
												Loss:</label>
											<input type="text" class="form-control" id="loss_experience" name="loss_experience" autocomplete="off" maxlength="11" value="<?php echo $player->getLossExperience(); ?>"/>
										</div>
										<div class="col-12 col-sm-12 col-lg-6">
											<label for="loss_mana" class="control-label">Mana Loss:</label>
											<input type="text" class="form-control" id="loss_mana" name="loss_mana" autocomplete="off" maxlength="11" value="<?php echo $player->getLossMana(); ?>"/>
										</div>
										<div class="col-12 col-sm-12 col-lg-6">
											<label for="loss_skills" class="control-label">Skills Loss:</label>
											<input type="text" class="form-control" id="loss_skills" name="loss_skills" autocomplete="off" maxlength="11" value="<?php echo $player->getLossSkills(); ?>"/>
										</div>
										<div class="col-12 col-sm-12 col-lg-6">
											<label for="loss_containers" class="control-label">Containers Loss:</label>
											<input type="text" class="form-control" id="loss_containers" name="loss_containers" autocomplete="off" maxlength="11" value="<?php echo $player->getLossContainers(); ?>"/>
										</div>
										<div class="col-12 col-sm-12 col-lg-6">
											<label for="loss_items" class="control-label">Items Loss:</label>
											<input type="text" class="form-control" id="loss_items" name="loss_items" autocomplete="off" maxlength="11" value="<?php echo $player->getLossItems(); ?>"/>
										</div>
									</div>
								<?php endif; ?>
								<div class="form-group row">
									<div class="col-12">
										<label for="comment" class="control-label">Comment:</label>
										<textarea class="form-control" id="comment" name="comment" rows="10" cols="50" wrap="virtual"><?php echo $player->getCustomField("comment"); ?></textarea>
										<small>[max. length: 2000 chars, 50 lines (ENTERs)]</small>
									</div>
								</div>
							</div>
							<div class="tab-pane fade" id="tabs-posts">
								<table class="table table-striped table-condensed table-responsive d-md-table">
									<thead>
									<tr>
										<th class="w-25">Topic</th>
										<th>Content</th>
									</tr>
									</thead>
									<tbody>
									<?php
									$posts = $db->query('SELECT `author_guid`,`section`,`first_post`,`post_text`,`post_date`, `post_topic`,`post_html`,`post_smile`,`' . TABLE_PREFIX . 'forum_boards`.`name` AS `forum_Name` FROM `' .
										TABLE_PREFIX . 'forum` LEFT JOIN `' . TABLE_PREFIX . 'forum_boards` ON `' .
										TABLE_PREFIX . 'forum`.section = `' . TABLE_PREFIX . 'forum_boards`.id WHERE `author_guid` = "' . $player->getId() . '" ORDER BY `post_date` DESC LIMIT 10');
									if ($posts->rowCount() > 0) {
										$posts = $posts->fetchAll();
										foreach ($posts as $post) {
											$text = ($post['post_html'] > 0 ? $post['post_text'] : htmlspecialchars($post['post_text']));
											$post['content'] = ($post['post_html'] > 0 ? $text : Forum::parseBBCode(nl2br($text), $post['post_smile'] == 0));
											?>
											<tr>
												<th><?php echo htmlspecialchars($post['post_topic']); ?><br/><small><?php echo date('d M y H:i:s', $post['post_date']); ?></small><br/>
													Topic: <a href="<?php echo getForumThreadLink($post['first_post']); ?>" class="link-black text-sm"><i class="fa fa-share margin-r-5"></i> Link</a><br/>
													Forum: <a href="<?php echo getForumBoardLink($post['section']); ?>" class="link-black text-sm"><i class="fa fa-share margin-r-5"></i> <?php echo $post['forum_Name']; ?></a></th>
												<th><?php echo $post['content']; ?></th>
											</tr>
											<?php
										}
										unset($post);
									} else {
										echo '<tr><td colspan="2">This user has no posts</td></tr>';
									}; ?>
									</tbody>
								</table>
							</div>
							<div class="tab-pane fade" id="tabs-chars">
								<div class="row">
									<?php
									if (isset($account) && $account->isLoaded()) {
										$account_players = Player::where('account_id', $account->getId())->orderBy('id')->get();
										if (isset($account_players)) { ?>
											<table class="table table-striped table-condensed table-responsive d-md-table">
												<thead>
												<tr>
													<th>#</th>
													<th>Name</th>
													<th>Level</th>
													<th>Vocation</th>
													<th style="width: 40px">Edit</th>
												</tr>
												</thead>
												<tbody>
												<?php foreach ($account_players as $i => $player): ?>
													<tr>
														<th><?php echo $i + 1; ?></th>
														<td><?php echo $player->name; ?></td>
														<td><?php echo $player->level; ?></td>
														<td><?php echo $player->vocation_name; ?></td>
														<td><a href="?p=players&id=<?php echo $player->getKey() ?>" class=" btn btn-success btn-sm" title="Edit"><i class="fas fa-pencil-alt"></i></a></td>
													</tr>
												<?php endforeach ?>
												</tbody>
											</table>
											<?php
										}
									} ?>
								</div>
							</div>
						</div>
					</div>
					<div class="card-footer text-center">
						<input type="hidden" name="save" value="yes"/>
						<button type="submit" class="btn btn-info float-left"><i class="fas fa-update"></i> Update</button>
						<a href="<?php echo ADMIN_URL; ?>?p=accounts&id=<?php echo $account->getId(); ?>" class="btn btn-secondary">Edit Account</a>
						<a href="<?php echo ADMIN_URL; ?>?p=players" class="btn btn-danger float-right"><i class="fas fa-cancel"></i> Cancel</a>
					</div>
				</form>
			</div>
		</div>

		<script type="text/javascript">
			$('#lastlogin').datetimepicker({format: "M d Y, H:i:s",});
			$('#lastlogout').datetimepicker({format: "M d Y, H:i:s",});
			$('#created').datetimepicker({format: "M d Y, H:i:s",});

			$(document).ready(function () {
				const $headSpan = $('#look_head_val');
				const $headvalue = $('#look_head');
				$headSpan.html($headvalue.val());
				$headvalue.on('input', () => {
					$headSpan.html($headvalue.val());
				});
				$headvalue.on('change', () => {
					updateOutfit();
				});

				const $bodySpan = $('#look_body_val');
				const $bodyvalue = $('#look_body');
				$bodySpan.html($bodyvalue.val());
				$bodyvalue.on('input', () => {
					$bodySpan.html($bodyvalue.val());
				});
				$bodyvalue.on('change', () => {
					updateOutfit();
				});

				const $legsSpan = $('#look_legs_val');
				const $legsvalue = $('#look_legs');
				$legsSpan.html($legsvalue.val());
				$legsvalue.on('input', () => {
					$legsSpan.html($legsvalue.val());
				});
				$legsvalue.on('change', () => {
					updateOutfit();
				});

				const $feetSpan = $('#look_feet_val');
				const $feetvalue = $('#look_feet');
				$feetSpan.html($feetvalue.val());
				$feetvalue.on('input', () => {
					$feetSpan.html($feetvalue.val());
				});
				$feetvalue.on('change', () => {
					updateOutfit();
				});

				const $lookvalue = $('#look_type');
				$lookvalue.on('change', () => {
					updateOutfit();
				});

				<?php if($hasLookAddons): ?>
				const $addonvalue = $('#look_addons');
				$addonvalue.on('change', () => {
					updateOutfit();
				});
				<?php endif; ?>
			});

			function updateOutfit() {
				const look_head = $('#look_head').val();
				const look_body = $('#look_body').val();
				const look_legs = $('#look_legs').val();
				const look_feet = $('#look_feet').val();
				const look_type = $('#look_type').val();

				let look_addons = '';
				<?php if($hasLookAddons): ?>
				look_addons = '&addons=' + $('#look_addons').val();
				<?php endif; ?>
				$("#player_outfit").attr("src", '<?= setting('core.outfit_images_url'); ?>?id=' + look_type + look_addons + '&head=' + look_head + '&body=' + look_body + '&legs=' + look_legs + '&feet=' + look_feet);
			}
		</script>
	<?php } ?>
	<div class="col-12 col-sm-12 col-lg-2">
		<div class="card card-info card-outline">
			<div class="card-header">
				<h5 class="m-0">Search Player</h5>
			</div>
			<div class="card-body row">
				<div class="col-6 col-lg-12">
					<form action="<?php echo $player_base; ?>" method="post">
						<?php csrf(); ?>
						<label for="search">Player Name:</label>
						<div class="input-group input-group-sm">
							<input type="text" class="form-control" id="search" name="search" value="<?= escapeHtml($search_player); ?>" maxlength="32" size="32">
							<span class="input-group-append"><button type="submit" class="btn btn-info btn-flat">Search</button></span>
						</div>
					</form>
				</div>
				<div class="col-6 col-lg-12">
					<form action="<?php echo $player_base; ?>" method="post">
						<?php csrf(); ?>
						<label for="id">Player ID:</label>
						<div class="input-group input-group-sm">
							<input type="text" class="form-control" id="id" name="id" value="<?= $id; ?>" maxlength="32" size="32">
							<span class="input-group-append"><button type="submit" class="btn btn-info btn-flat">Search</button></span>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
	$(function () {
		$('.player_datatable').DataTable({
			"order": [[0, "asc"]]
		});
	});
</script>
