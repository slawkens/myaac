<?php
/**
 * Experience stages
 *
 * @package   MyAAC
 * @author    Gesior <jerzyskalski@wp.pl>
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2019 MyAAC
 * @link      https://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');
$title = 'Experience Stages';

if(file_exists($config['data_path'] . 'XML/stages.xml')) {
	$stages = new DOMDocument();
	$stages->load($config['data_path'] . 'XML/stages.xml');
}

if(!isset($config['lua']['experienceStages']) || !getBoolean($config['lua']['experienceStages']))
{
	$enabled = false;

	if(isset($stages)) {
		foreach($stages->getElementsByTagName('config') as $node) {
			/** @var DOMElement $node */
			if($node->getAttribute('enabled'))
				$enabled = true;
		}
	}

	if(!$enabled) {
		$rate_exp = 'not set';
		if(isset($config['lua']['rateExperience']))
			$rate_exp = $config['lua']['rateExperience'];
		else if(isset($config['lua']['rateExp']))
			$rate_exp = $config['lua']['rateExp'];
		else if(isset($config['lua']['rate_exp']))
			$rate_exp = $config['lua']['rate_exp'];

		echo 'Server is not configured to use experience stages.<br/>Current experience rate is: <b>x' .  $rate_exp . '</b>';
		return;
	}
}

if(!$stages)
{
	echo 'Error: cannot load <b>stages.xml</b>!';
	return;
}

$stagesArray = [];
foreach($stages->getElementsByTagName('stage') as $stage)
{
	/** @var DOMElement $stage */
	$maxLevel = $stage->getAttribute('maxlevel');
	$stagesArray[] = [
		'levels' => $stage->getAttribute('minlevel') . (isset($maxLevel[0]) ? '-' . $maxLevel : '+'),
		'multiplier' => $stage->getAttribute('multiplier')
	];
}

$twig->display('experience_stages.html.twig', ['stages' => $stagesArray]);
