<?php
/**
 * Server info
 *
 * @package   MyAAC
 * @author    Gesior <jerzyskalski@wp.pl>
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2017 MyAAC
 * @link      http://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');
$title = 'Server info';

if(isset($config['lua']['experience_stages']))
	$config['lua']['experienceStages'] = $config['lua']['experience_stages'];
?>

<center>
	<h1><?php echo $config['lua']['serverName']; ?></h1>
	<h3>
		<?php if(isset($config['lua']['experienceStages']) && getBoolean($config['lua']['experienceStages'])): ?>
		Experience stages: <a href="<?php echo getLink('experienceStages'); ?>">Look here</a><br/>
		<?php endif; ?>
		Commands: <a href="<?php echo getLink('commands'); ?>">Look here</a><br/>
		Team: <a href="<?php echo getLink('team'); ?>">Look here</a><br/>
	</h3>
</center>

<ul>
	<h2>Server Info:</h2>
	<li>IP: <b><?php echo str_replace('/', '', str_replace('http://', '', $config['lua']['url'])); ?></b> (Port: <b><?php echo $config['lua']['loginPort']; ?></b>)</li>
<?php
	if($status['online'])
	echo '<li>Client: <b>' . $status['clientVersion'] . '</b></li>';
?>
	<li>Online: <b>24/7</b></li>
<?php
	if(isset($config['lua']['globalSaveEnabled']) && getBoolean($config['lua']['globalSaveEnabled']))
		echo '<li>Global save: <b>' . $config['lua']['globalSaveHour'] . ':00</b></li>';

	if(isset($config['lua']['min_pvp_level'])){
		$config['lua']['protectionLevel'] = $config['lua']['min_pvp_level'];
	}
	
	if(isset($config['lua']['protectionLevel'])):
?>
	<li>World type: <b>PVP <i>(Protection level: ><?php echo $config['lua']['protectionLevel']; ?>)</i></b></li>
<?php
	endif;
	$rent = trim(strtolower($config['lua']['houseRentPeriod']));
	if($rent != 'yearly' && $rent != 'monthly' && $rent != 'weekly' && $rent != 'daily')
		$rent = 'never';

	echo '<li>House rent: ' . ($rent == 'never' ? 'disabled' : $rent) . '.</li>';

	if(isset($config['lua']['houseCleanOld'])) {
		$cleanOld = (int)(eval('return ' . $config['lua']['houseCleanOld'] . ';') / (24 * 60 * 60));
		if($cleanOld > 0)
			echo '<li>Houses with inactive owners are cleaned after 30 days.</li>';
	}
	
	if(isset($config['lua']['rate_exp']))
		$config['lua']['rateExp'] = $config['lua']['rate_exp'];
	if(isset($config['lua']['rate_mag']))
		$config['lua']['rateMagic'] = $config['lua']['rate_mag'];
	if(isset($config['lua']['rate_skill']))
		$config['lua']['rateSkill'] = $config['lua']['rate_skill'];
	if(isset($config['lua']['rate_loot']))
		$config['lua']['rateLoot'] = $config['lua']['rate_loot'];
	if(isset($config['lua']['rate_spawn']))
		$config['lua']['rateSpawn'] = $config['lua']['rate_spawn'];
?>
	<br/>

	<h2>Rates</h2>
	<?php if(isset($config['lua']['rateExp'])): ?>
	<li>Exp Rate: <b>x<?php echo $config['lua']['rateExp']; ?></b></li>
	<?php endif;
	if(isset($config['lua']['rateMagic'])): ?>
	<li>Magic Level: <b>x<?php echo $config['lua']['rateMagic']; ?></b></li>
	<?php endif;
	if(isset($config['lua']['rateSkill'])): ?>
	<li>Skills: <b>x<?php echo $config['lua']['rateSkill']; ?></b></li>
	<?php endif;
	if(isset($config['lua']['rateLoot'])): ?>
	<li>Loot: <b>x<?php echo $config['lua']['rateLoot']; ?></b></li>
	<?php endif;
	if(isset($config['lua']['rateSpawn'])): ?>
	<li>Spawn: <b>x<?php echo $config['lua']['rateSpawn']; ?></b></li>
	<?php endif; ?>
<?php
	$house_level = NULL;
	if(isset($config['lua']['levelToBuyHouse']))
		$house_level = $config['lua']['levelToBuyHouse'];
	else if(isset($config['lua']['house_level']))
		$house_level = $config['lua']['house_level'];

	if(isset($house_level)):
?>
	<li>Houses: <b><?php echo $house_level; ?> level</b></li>
	<?php endif; ?>
	<li>Guilds: <b><?php echo $config['guild_need_level']; ?> level</b> (Create via website)</li>
	<br>

<?php
	if(isset($config['lua']['in_fight_duration']))
		$config['lua']['pzLocked'] = $config['lua']['in_fight_duration'];

	$pzLocked = eval('return ' . $config['lua']['pzLocked'] . ';');
	$whiteSkullTime = isset($config['lua']['whiteSkullTime']) ? $config['lua']['whiteSkullTime'] : NULL;
	if(!isset($whiteSkullTime) && isset($config['lua']['unjust_skull_duration']))
		$whiteSkullTime = $config['lua']['unjust_skull_duration'];

	if(isset($whiteSkullTime))
		$whiteSkullTime = eval('return ' . $whiteSkullTime . ';');

	$redSkullLength = isset($config['lua']['redSkullLength']) ? $config['lua']['redSkullLength'] : NULL;
	if(!isset($redSkullLength) && isset($config['lua']['red_skull_duration']))
		$redSkullLength = $config['lua']['red_skull_duration'];

	if(isset($redSkullLength))
		$redSkullLength = eval('return ' . $redSkullLength . ';');

	$blackSkull = false;
	$blackSkullLength = NULL;
	if(isset($config['lua']['useBlackSkull']) && getBoolean($config['lua']['useBlackSkull']))
	{
		$blackSkullLength = $config['lua']['blackSkullLength'];
		$blackSkull = true;
	}
	else if(isset($config['lua']['black_skull_duration'])) {
		$blackSkullLength = eval('return ' . $config['lua']['blackSkullLength'] . ';');
		$blackSkull = true;
	}
?>
	<h2>Frags & Skull system</h2>
	<li>PZ Lock: <b><?php echo ($pzLocked / (60 * 1000)); ?> min</b></li>
	<?php if(isset($whiteSkullTime)):?>
	<li>White Skull Time: <b><?php echo ($whiteSkullTime / (60 * 1000)); ?> min</b></li>
	<?php endif; ?>
	<li>Red skull length: <b><?php echo ($redSkullLength / (24 * 60 * 60)); ?> days</b></li>
	<?php if($blackSkull): ?>
	<li>Black skull length: <b><?php echo ($blackSkullLength / (24 * 60 * 60)); ?> days</b></li>
	<?php endif;
	if(isset($config['killsToRedSkull'])): ?>
	<li>Kills to red skull: <b><?php echo $config['lua']['killsToRedSkull']; ?></b></li>
	<?php elseif(isset($config['lua']['dailyFragsToRedSkull']) || isset($config['lua']['kills_per_day_red_skull'])): ?>
	<li>
		<h3>Red skull</h3>
		<ul>
			<li><?php echo (isset($config['lua']['dailyFragsToRedSkull']) ? $config['lua']['dailyFragsToRedSkull'] : $config['lua']['kills_per_day_red_skull']); ?> frags daily</li>
			<li><?php echo (isset($config['lua']['weeklyFragsToRedSkull']) ? $config['lua']['weeklyFragsToRedSkull'] : $config['lua']['kills_per_week_red_skull']); ?> frags weekly</li>
			<li><?php echo (isset($config['lua']['monthlyFragsToRedSkull']) ? $config['lua']['monthlyFragsToRedSkull'] : $config['lua']['kills_per_month_red_skull']); ?> frags monthly</li>
		</ul>
		<?php if($blackSkull && (isset($config['lua']['dailyFragsToBlackSkull']) || isset($config['lua']['kills_per_day_black_skull']))): ?>
		<h3>Black skull</h3>
		<ul>
			<li><?php echo (isset($config['lua']['dailyFragsToBlackSkull']) ? $config['lua']['dailyFragsToBlackSkull'] : $config['lua']['kills_per_day_black_skull']); ?> frags daily</li>
			<li><?php echo (isset($config['lua']['weeklyFragsToBlackSkull']) ? $config['lua']['weeklyFragsToBlackSkull'] : $config['lua']['kills_per_week_black_skull']); ?> frags weekly</li>
			<li><?php echo (isset($config['lua']['monthlyFragsToBlackSkull']) ? $config['lua']['monthlyFragsToBlackSkull'] : $config['lua']['kills_per_month_black_skull']); ?> frags monthly</li>
		</ul>
		<?php else:
			if(isset($config['lua']['dailyFragsToBanishment'])): ?>
		<h3>Banishment</h3>
		<ul>
			<li><?php echo $config['lua']['dailyFragsToBanishment']; ?> frags daily</li>
			<li><?php echo $config['lua']['weeklyFragsToBanishment']; ?> frags weekly</li>
			<li><?php echo $config['lua']['monthlyFragsToBanishment']; ?> frags monthly</li>
		</ul>
		<?php endif; 
		endif; ?>
	</li>
	<?php
	endif;
	if(isset($config['lua']['banishment_length'])): ?>
	<li>Banishment length: <b><?php echo eval('return (' . $config['lua']['banishment_length'] . ') / (24 * 60 * 60);'); ?> days</b></li>
	<?php endif;
	if(isset($config['lua']['final_banishment_length'])): ?>
	<li>Final banishment length: <b><?php echo eval('return (' . $config['lua']['final_banishment_length'] . ') / (24 * 60 * 60);'); ?> days</b></li>
	<?php endif;
	if(isset($config['lua']['ip_banishment_length'])): ?>
	<li>IP banishment length: <b><?php echo eval('return (' . $config['lua']['ip_banishment_length'] . ') / (24 * 60 * 60);'); ?> days</b></li>
	<?php endif; ?>
	<br/>
	<h2>Other</h2>
	<li>Respect our <a href="<?php echo getLink('rules'); ?>">rules</a>.</li>
	<li>Please report rule violations (Botters, players breaking rules etc) with <b>CTRL + R</b>.</li>
</ul>
