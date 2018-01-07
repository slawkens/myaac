<?php
/**
 * Players editor
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2017 MyAAC
 * @link      http://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');

$title = 'Player editor';
$base = BASE_URL . 'admin/?p=players';

function echo_success($message) {
	echo '<p class="success">' . $message . '</p>';
}
function echo_error($message) {
	global $error;
	echo '<p class="error">' . $message . '</p>';
	$error = true;
}

function verify_number($number, $name, $max_length) {
	if(!Validator::number($number))
		echo_error($name . ' can contain only numbers.');

	$number_length = strlen($number);
	if($number_length <= 0 || $number_length > $max_length)
		echo_error($name . ' cannot be longer than ' . $max_length . ' digits.');
}

$skills = array(
	POT::SKILL_FIST => array('Fist fighting', 'fist'),
	POT::SKILL_CLUB => array('Club fighting', 'club'),
	POT::SKILL_SWORD => array('Sword fighting', 'sword'),
	POT::SKILL_AXE => array('Axe fighting', 'axe'),
	POT::SKILL_DIST => array('Distance fighting', 'dist'),
	POT::SKILL_SHIELD => array('Shielding', 'shield'),
	POT::SKILL_FISH => array('Fishing', 'fish')
);
?>

<link rel="stylesheet" type="text/css" href="<?php echo BASE_URL; ?>tools/jquery.datetimepicker.css"/ >
<script src="<?php echo BASE_URL; ?>tools/jquery.datetimepicker.js"></script>

<?php
$id = 0;
if(isset($_REQUEST['id']))
	$id = (int)$_REQUEST['id'];
else if(isset($_REQUEST['search_name'])) {
	if(strlen($_REQUEST['search_name']) < 3 && !Validator::number($_REQUEST['search_name'])) {
		echo 'Player name is too short.';
	}
	else {
		if(Validator::number($_REQUEST['search_name']))
			$id = $_REQUEST['search_name'];
		else {
			$query = $db->query('SELECT `id` FROM `players` WHERE `name` = ' . $db->quote($_REQUEST['search_name']));
			if($query->rowCount() == 1) {
				$query = $query->fetch();
				$id = $query['id'];
			}
			else {
				$query = $db->query('SELECT `id`, `name` FROM `players` WHERE `name` LIKE ' . $db->quote('%' . $_REQUEST['search_name'] . '%'));
				if($query->rowCount() > 0 && $query->rowCount() <= 10) {
					echo 'Do you mean?<ul>';
					foreach($query as $row)
						echo '<li><a href="' . $base . '&id=' . $row['id'] . '">' . $row['name'] . '</a></li>';
					echo '</ul>';
				}
				else if($query->rowCount() > 10)
					echo 'Specified name resulted with too many players.';
			}
		}
	}
}

$groups = new OTS_Groups_List();
if($id > 0) {
	$player = new OTS_Player();
	$player->load($id);
	
	if(isset($player) && $player->isLoaded() && isset($_POST['save'])) {// we want to save
		$error = false;

		if($player->isOnline())
			echo_error('This player is actually online. You can\'t edit online players.');

		$name = $_POST['name'];
		$_error = '';
		if(!Validator::characterName($name))
			echo_error(Validator::getLastError());

		//if(!Validator::newCharacterName($name)
		//	echo_error(Validator::getLastError());

		$player_db = new OTS_Player();
		$player_db->find($name);
		if($player_db->isLoaded() && $player->getName() != $name)
			echo_error('This name is already used. Please choose another name!');

		
		$account_id = $_POST['account_id'];
		verify_number($account_id, 'Account id', 11);

		$account_db = new OTS_Account();
		$account_db->load($account_id);
		if(!$account_db->isLoaded())
			echo_error('Account with this id doesn\'t exist.');
	
		$group = $_POST['group'];
		if($groups->getGroup($group) == false)
			echo_error('Group with this id doesn\'t exist');

		$level = $_POST['level'];
		verify_number($level, 'Level', 11);
		
		$experience = $_POST['experience'];
		verify_number($experience, 'Experience', 20);

		$vocation = $_POST['vocation'];
		verify_number($vocation, 'Vocation id', 11);

		if(!isset($config['vocations'][$vocation])) {
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
		if($db->hasColumn('players', 'lookaddons')) {
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
		
		$lastlogin = $_POST['lastlogin'];
		verify_number($lastlogin, 'Last login', 20);
		$lastlogout = $_POST['lastlogout'];
		verify_number($lastlogout, 'Last logout', 20);
		$lastip = $_POST['lastip'];
		$exp = explode(".", $lastip);
		$lastip = $exp[3] . '.' . $exp[2] . '.' . $exp[1] . '.' . $exp[0];
		$lastip_length = strlen($lastip);
		if($lastip_length <= 0 || $lastip_length > 15)
			echo_error('IP cannot be longer than 15 digits.');

		$skull = $_POST['skull'];
		verify_number($skull, 'Skull', 1);
		$skull_time = $_POST['skull_time'];
		verify_number($skull_time, 'Skull time', 11);
	
		if($db->hasColumn('players', 'loss_experience')) {
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
		
		if($db->hasColumn('players', 'blessings')) {
			$blessings = $_POST['blessings'];
			verify_number($blessings, 'Blessings', 2);
		}
		$balance = $_POST['balance'];
		verify_number($balance, 'Balance', 20);
		if($db->hasColumn('players', 'stamina')) {
			$stamina = $_POST['stamina'];
			verify_number($stamina, 'Stamina', 20);
		}

		$deleted = (isset($_POST['deleted']) && $_POST['deleted'] == 'true');
		$hidden = (isset($_POST['hidden']) && $_POST['hidden'] == 'true');
		
		$created = $_POST['created'];
		verify_number($created, 'Created', 11);

		$comment = isset($_POST['comment']) ? htmlspecialchars(stripslashes(substr($_POST['comment'],0,2000))) : NULL;
		
		foreach($_POST['skills'] as $skill => $value)
			verify_number($value, $skills[$skill][0], 10);
		foreach($_POST['skills_tries'] as $skill => $value)
			verify_number($value, $skills[$skill][0] . ' tries', 10);

		if(!$error) {
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
			if($db->hasColumn('players', 'lookaddons'))
				$player->setLookAddons($look_addons);
			$player->setPosX($pos_x);
			$player->setPosY($pos_y);
			$player->setPosZ($pos_z);
			$player->setSoul($soul);
			$player->setTownId($town);
			$player->setCap($capacity);
			$player->setSex($sex);
			$player->setLastLogin($lastlogin);
			$player->setLastLogout($lastlogout);
			$player->setLastIP(ip2long($lastip));
			$player->setSkull($skull);
			$player->setSkullTime($skull_time);
			if($db->hasColumn('players', 'loss_experience')) {
				$player->setLossExperience($loss_experience);
				$player->setLossMana($loss_mana);
				$player->setLossSkills($loss_skills);
				$player->setLossContainers($loss_containers);
				$player->setLossItems($loss_items);
			}
			if($db->hasColumn('players', 'blessings'))
				$player->setBlessings($blessings);
			$player->setBalance($balance);
			if($db->hasColumn('players', 'stamina'))
				$player->setStamina($stamina);
			if($db->hasColumn('players', 'deletion'))
				$player->setCustomField('deletion', $deleted ? '1' : '0');
			else
				$player->setCustomField('deleted', $deleted ? '1' : '0');
			$player->setCustomField('hidden', $hidden ? '1': '0');
			$player->setCustomField('created', $created);
			if(isset($comment))
				$player->setCustomField('comment', $comment);
			foreach($_POST['skills'] as $skill => $value) {
				$player->setSkill($skill, $value);
			}
			foreach($_POST['skills_tries'] as $skill => $value) {
				$player->setSkillTries($skill, $value);
			}
			$player->save();
			echo_success('Player saved at: ' . date('G:i'));
		}
	}
}

$search_name = '';
if(isset($_REQUEST['search_name']))
	$search_name = $_REQUEST['search_name'];
else if($id > 0 && isset($player) && $player->isLoaded())
	$search_name = $player->getName();

?>
<form action="<?php echo $base; ?>" method="post">
	<input type="text" name="search_name" value="<?php echo $search_name; ?>" maxlength="32" size="32" />
	<input type="submit" class="button" value="Search" />
</form>
<?php
if(!isset($player) || !$player->isLoaded())
	return;

$account = $player->getAccount();
?>
<br/>
<form action="<?php echo $base . ((isset($id) && $id > 0) ? '&id=' . $id : ''); ?>" method="post">
<table class="table" cellspacing="1" cellpadding="4">
	<tr><th colspan="2">Edit player</th></tr>
	<tr>
		<td>Name: </td>
		<td><input type="text" name="name" value="<?php echo $player->getName(); ?>" /></td>
	</tr>
	<tr>
		<td colspan="2">
			<table>
				<tr style="background-color: transparent;">
					<td>Account id: </td>
					<td><input type="text" name="account_id" size="8" maxlength="11" value="<?php echo $account->getId(); ?>" /></td>
					
					<td>Group: </td>
					<td>
						<select name="group">
							<?php foreach($groups->getGroups() as $id => $group): ?>
								<option value="<?php echo $id; ?>" <?php echo ($player->getGroup()->getId() == $id ? 'selected' : ''); ?>><?php echo $group->getName(); ?></option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<table>
				<tr style="background-color: transparent;">
					<td>Level: </td>
					<td><input type="text" name="level" size="8" maxlength="11" value="<?php echo $player->getLevel(); ?>" /></td>
			
					<td>Experience: </td>
					<td><input type="text" name="experience" size="19" maxlength="20" value="<?php echo $player->getExperience(); ?>" /></td>
					
					<td>Health:</td>
					<td><input type="text" name="health" size="5" maxlength="11" value="<?php echo $player->getHealth(); ?>" /></td>
					
					<td>Health max:</td>
					<td><input type="text" name="health_max" size="5" maxlength="11" value="<?php echo $player->getHealthMax(); ?>" /></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>Vocation: </td>
		<td>
			<select name="vocation">
<?php
			foreach($config['vocations'] as $id => $name)
			{
				echo '<option value=' . $id;
				if($id == $player->getVocation())
					echo ' selected="selected"';
				echo '>' . $name . '</option>';
			}
					

?>
			</select>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<table>
				<tr style="background-color: transparent;">
					<td>Magic level:</td>
					<td><input type="text" name="magic_level" size="8" maxlength="11" value="<?php echo $player->getMagLevel(); ?>" /></td>
					
					<td>Mana:</td>
					<td><input type="text" name="mana" size="3" maxlength="11" value="<?php echo $player->getMana(); ?>" /></td>
					
					<td>Mana max:</td>
					<td><input type="text" name="mana_max" size="3" maxlength="11" value="<?php echo $player->getManaMax(); ?>" /></td>
					
					<td>Mana spent:</td>
					<td><input type="text" name="mana_spent" size="3" maxlength="11" value="<?php echo $player->getManaSpent(); ?>" /></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>Look: </td>
		<td>
			Body:<input type="text" name="look_body" size="2" maxlength="11" value="<?php echo $player->getLookBody(); ?>" />
			Feet:<input type="text" name="look_feet" size="2" maxlength="11" value="<?php echo $player->getLookFeet(); ?>" />
			Head:<input type="text" name="look_head" size="2" maxlength="11" value="<?php echo $player->getLookHead(); ?>" />
			Legs:<input type="text" name="look_legs" size="2" maxlength="11" value="<?php echo $player->getLookLegs(); ?>" />
			Type:<input type="text" name="look_type" size="2" maxlength="11" value="<?php echo $player->getLookType(); ?>" />
			<?php if($db->hasColumn('players', 'lookaddons')): ?>
			Addons:<input type="text" name="look_addons" size="2" maxlength="11" value="<?php echo $player->getLookAddons(); ?>" />
			<?php endif; ?>
		</td>
	</tr>
	<tr>
		<td>Position: </td>
		<td>
			X: <input type="text" name="pos_x" size="8" maxlength="11" value="<?php echo $player->getPosX(); ?>" />
			Y: <input type="text" name="pos_y" size="8" maxlength="11" value="<?php echo $player->getPosY(); ?>" />
			Z: <input type="text" name="pos_z" size="8" maxlength="11" value="<?php echo $player->getPosZ(); ?>" />
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<table>
				<tr style="background-color: transparent;">
					<td>Soul:</td>
					<td><input type="text" name="soul" size="8" maxlength="10" value="<?php echo $player->getSoul(); ?>" /></td>
					
					<td>Town:</td>
					<td>
						<select name="town">
							<?php foreach($config['towns'] as $id => $town): ?>
								<option value="<?php echo $id; ?>" <?php echo ($player->getTownId() == $id ? 'selected' : ''); ?>><?php echo $town; ?></option>
							<?php endforeach; ?>
						</select>
					</td>

					<td>Capacity:</td>
					<td><input type="text" name="capacity" size="8" maxlength="11" value="<?php echo $player->getCap(); ?>" /></td>
					
					<td>Sex:</td>
					<td>
						<select name="sex">
							<?php foreach($config['genders'] as $id => $sex): ?>
								<option value="<?php echo $id; ?>" <?php echo ($player->getSex() == $id ? 'selected' : ''); ?>><?php echo strtolower($sex); ?></option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<table>
				<tr style="background-color: transparent;">
					<td>Last login:</td>
					<td><input type="text" name="lastlogin" id="lastlogin" size="16" maxlength="20" value="<?php echo $player->getLastLogin(); ?>" /></td>

					<td>Last logout:</td>
					<td><input type="text" name="lastlogout" id="lastlogout" size="16" maxlength="20" value="<?php echo $player->getLastLogout(); ?>" /></td>
					
					<td>Last IP:</td>
					<td><input type="text" name="lastip" size="8" maxlength="10" value="<?php echo longToIp($player->getLastIP()); ?>" /></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<table>
				<tr style="background-color: transparent;">
					<td>Skull:</td>
					<td><input type="text" name="skull" size="1" maxlength="1" value="<?php echo $player->getSkull(); ?>" /></td>

					<td>Skull time:</td>
					<td><input type="text" name="skull_time" size="8" maxlength="11" value="<?php echo $player->getSkullTime(); ?>" /></td>
				</tr>
			</table>
		</td>
	</tr>
	<?php if($db->hasColumn('players', 'loss_experience')): ?>
	<tr>
		<td colspan="2">
			<table>
				<tr style="background-color: transparent;">
					<td>Loss experience:</td>
					<td><input type="text" name="loss_experience" size="8" maxlength="11" value="<?php echo $player->getLossExperience(); ?>" /></td>

					<td>Loss mana:</td>
					<td><input type="text" name="loss_mana" size="8" maxlength="11" value="<?php echo $player->getLossMana(); ?>" /></td>
					
					<td>Loss skills:</td>
					<td><input type="text" name="loss_skills" size="8" maxlength="11" value="<?php echo $player->getLossSkills(); ?>" /></td>
					
					<td>Loss containers:</td>
					<td><input type="text" name="loss_containers" size="8" maxlength="11" value="<?php echo $player->getLossContainers(); ?>" /></td>
					
					<td>Loss items:</td>
					<td><input type="text" name="loss_items" size="8" maxlength="11" value="<?php echo $player->getLossItems(); ?>" /></td>
				</tr>
			</table>
		</td>
	</tr>
	<?php endif; ?>
	<tr>
		<td colspan="2">
			<table>
				<tr style="background-color: transparent;">
					<?php if($db->hasColumn('players', 'blessings')): ?>
					<td>Blessings:</td>
					<td><input type="text" name="blessings" size="2" maxlength="2" value="<?php echo $player->getBlessings(); ?>" /></td>
					<?php endif; ?>
					<td>Balance:</td>
					<td><input type="text" name="balance" size="16" maxlength="20" value="<?php echo $player->getBalance(); ?>" /></td>
					
					<?php if($db->hasColumn('players', 'stamina')): ?>
					<td>Stamina:</td>
					<td><input type="text" name="stamina" size="16" maxlength="20" value="<?php echo $player->getStamina(); ?>" /></td>
					<?php endif; ?>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<table>
				<tr style="background-color: transparent;">
					<td><label for="deleted">Deleted:</label></td>
					<td><input type="checkbox" name="deleted" id="deleted" value="true" <?php echo ($player->getCustomField($db->hasColumn('players', 'deletion') ? 'deletion' : 'deleted') == '1' ? ' checked' : ''); ?>/></td>

					<td><label for="hidden">Hidden:</label></td>
					<td><input type="checkbox" name="hidden" id="hidden" value="true" <?php echo ($player->isHidden() ? ' checked' : ''); ?>/></td>

					<td>Created:</td>
					<td><input type="text" name="created" id="created" value="<?php echo $player->getCustomField('created'); ?>"/></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>Comment: </td>
		<td>
			<textarea name="comment" rows="10" cols="50" wrap="virtual" ><?php echo $player->getCustomField("comment"); ?></textarea><br>[max. length: 2000 chars, 50 lines (ENTERs)]
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<table>
				<?php
					$i = 0;
					foreach($skills as $id => $info) {
						if($i == 0 || $i++ == 2) {
							echo '<tr style="background-color: transparent;">';
							$i = 0;
						}
						echo '
						<td>' . $info[0] . '</td>
						<td><input type="text" name="skills[' . $id . ']" size="8" maxlength="10" value="' . $player->getSkill($id) . '" /></td>
						<td>' . $info[0] . ' tries</td>
						<td><input type="text" name="skills_tries[' . $id . ']" size="8" maxlength="10" value="' . $player->getSkill($id) . '" /></td>';
						
						if($i == 0)
							echo '</tr>';
					}					
?>
			</table>
		</td>
	</tr>
	<input type="hidden" name="save" value="yes" />
	<tr>
		<td><input type="submit" class="button" value="Save" /></td>
		<td><input type="cancel" onclick="window.location = '<?php echo ADMIN_URL; ?>&p=players';" class="button" value="Cancel" /></td>
	</tr>
</table>
</form>

<script type="text/javascript">
$('#lastlogin').datetimepicker({
	format:'unixtime'
});
$('#lastlogout').datetimepicker({
	format:'unixtime'
});
$('#created').datetimepicker({
	format:'unixtime'
});
</script>