<?php
/**
 * Example of using getTopPlayers() function
 * to display the best players for each skill
 */
defined('MYAAC') or die('Direct access not allowed!');

$skills = [
	'magic', 'level',
	'balance', 'frags',
	POT::SKILL_FIST, POT::SKILL_CLUB,
	POT::SKILL_SWORD, POT::SKILL_AXE,
	POT::SKILL_DISTANCE, POT::SKILL_SHIELD,
	POT::SKILL_FISH
];

foreach ($skills as $skill) {?>
<ul>
<?php
	echo '<strong>' . ucwords(is_string($skill) ? $skill : getSkillName($skill)) . '</strong>';
	foreach (getTopPlayers(5, $skill) as $player) {?>
		<li><?= $player['rank'] . '. ' . $player['name'] . ' - ' . $player['value']; ?></li>
		<?php
	}
	?>
</ul>
<?php
}

