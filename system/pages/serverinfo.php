<?php
/**
 * Server info
 *
 * @package   MyAAC
 * @author    Gesior <jerzyskalski@wp.pl>
 * @author    Slawkens <slawkens@gmail.com>
 * @author    whiteblXK
 * @copyright 2019 MyAAC
 * @link      https://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');
$title = 'Server info';

if(isset($config['experience_stages']))
    $config['experienceStages'] = $config['experience_stages'];

if(isset($config['min_pvp_level']))
    $config['protectionLevel'] = $config['min_pvp_level'];

$rent = trim(strtolower($config['houseRentPeriod']));
if($rent != 'yearly' && $rent != 'monthly' && $rent != 'weekly' && $rent != 'daily')
    $rent = 'never';

if(isset($config['houseCleanOld']))
    $cleanOld = (int)(eval('return ' . $config['houseCleanOld'] . ';') / (24 * 60 * 60));

if(isset($config['rate_exp']))
    $config['rateExp'] = $config['rate_exp'];
if(isset($config['rateExperience']))
    $config['rateExp'] = $config['rateExperience'];
if(isset($config['rate_mag']))
    $config['rateMagic'] = $config['rate_mag'];
if(isset($config['rate_skill']))
    $config['rateSkill'] = $config['rate_skill'];
if(isset($config['rate_loot']))
    $config['rateLoot'] = $config['rate_loot'];
if(isset($config['rate_spawn']))
    $config['rateSpawn'] = $config['rate_spawn'];

$house_level = NULL;
if(isset($config['levelToBuyHouse']))
    $house_level = $config['levelToBuyHouse'];
else if(isset($config['house_level']))
    $house_level = $config['house_level'];

if(isset($config['in_fight_duration']))
    $config['pzLocked'] = $config['in_fight_duration'];

$pzLocked = eval('return ' . $config['pzLocked'] . ';');
$whiteSkullTime = isset($config['whiteSkullTime']) ? $config['whiteSkullTime'] : NULL;
if(!isset($whiteSkullTime) && isset($config['unjust_skull_duration']))
    $whiteSkullTime = $config['unjust_skull_duration'];

if(isset($whiteSkullTime))
    $whiteSkullTime = eval('return ' . $whiteSkullTime . ';');

$redSkullLength = isset($config['redSkullLength']) ? $config['redSkullLength'] : NULL;
if(!isset($redSkullLength) && isset($config['red_skull_duration']))
    $redSkullLength = $config['red_skull_duration'];

if(isset($redSkullLength))
    $redSkullLength = eval('return ' . $redSkullLength . ';');

$blackSkull = false;
$blackSkullLength = NULL;
if(isset($config['useBlackSkull']) && getBoolean($config['useBlackSkull']))
{
    $blackSkullLength = $config['blackSkullLength'];
    $blackSkull = true;
}
else if(isset($config['black_skull_duration'])) {
    $blackSkullLength = eval('return ' . $config['blackSkullLength'] . ';');
    $blackSkull = true;
}

$clientVersion = NULL;
if(isset($status['online']))
    $clientVersion = isset($status['clientVersion']) ? $status['clientVersion'] : null;

$twig->display('serverinfo.html.twig', array(
    'experienceStages' => isset($config['experienceStages']) && getBoolean($config['experienceStages']) ? $config['experienceStages'] : null,
    'serverIp' => str_replace('/', '', str_replace('http://', '', $config['url'])),
    'clientVersion' => $clientVersion,
    'globalSaveHour' => isset($config['globalSaveEnabled']) && getBoolean($config['globalSaveEnabled']) ? $config['globalSaveHour'] : null,
    'protectionLevel' => $config['protectionLevel'],
    'houseRent' => $rent == 'never' ? 'disabled' : $rent,
    'houseOld' => isset($cleanOld) ? $cleanOld : null,
    'rateExp' => $config['rateExp'],
    'rateExpFromPlayers' => isset($config['rateExperienceFromPlayers']) ? $config['rateExperienceFromPlayers'] : null,
    'rateMagic' => $config['rateMagic'],
    'rateSkill' => $config['rateSkill'],
    'rateLoot' => $config['rateLoot'],
    'rateSpawn' => $config['rateSpawn'],
    'houseLevel' => $house_level,
    'pzLocked' => $pzLocked,
    'whiteSkullTime' => $whiteSkullTime,
    'redSkullLength' => $redSkullLength,
    'blackSkull' => $blackSkull,
    'blackSkullLength' => $blackSkullLength,
    'dailyFragsToRedSkull' => isset($config['dailyFragsToRedSkull']) ? $config['dailyFragsToRedSkull'] : (isset($config['kills_per_day_red_skull']) ? $config['kills_per_day_red_skull'] : null),
    'weeklyFragsToRedSkull' => isset($config['weeklyFragsToRedSkull']) ? $config['weeklyFragsToRedSkull'] : (isset($config['kills_per_week_red_skull']) ? $config['kills_per_week_red_skull'] : null),
    'monthlyFragsToRedSkull' => isset($config['monthlyFragsToRedSkull']) ? $config['monthlyFragsToRedSkull'] : (isset($config['kills_per_month_red_skull']) ? $config['kills_per_month_red_skull'] : null),
    'dailyFragsToBlackSkull' => isset($config['dailyFragsToBlackSkull']) ? $config['dailyFragsToBlackSkull'] : (isset($config['kills_per_day_black_skull']) ? $config['kills_per_day_black_skull'] : null),
    'weeklyFragsToBlackSkull' => isset($config['weeklyFragsToBlackSkull']) ? $config['weeklyFragsToBlackSkull'] : (isset($config['kills_per_week_black_skull']) ? $config['kills_per_week_black_skull'] : null),
    'monthlyFragsToBlackSkull' => isset($config['monthlyFragsToBlackSkull']) ? $config['monthlyFragsToBlackSkull'] : (isset($config['kills_per_month_black_skull']) ? $config['kills_per_month_black_skull'] : null),
    'banishmentLength' => isset($config['banishment_length']) ? eval('return (' . $config['banishment_length'] . ') / (24 * 60 * 60);') : null,
    'finalBanishmentLength' => isset($config['final_banishment_length']) ? eval('return (' . $config['final_banishment_length'] . ') / (24 * 60 * 60);') : null,
    'ipBanishmentLength' => isset($config['ip_banishment_length']) ? eval('return (' . $config['ip_banishment_length'] . ') / (24 * 60 * 60);') : null,
));
