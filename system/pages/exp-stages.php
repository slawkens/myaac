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

if((!isset($config['lua']['experienceStages']) || !getBoolean($config['lua']['experienceStages']))
	&& (!isset($config['lua']['rateUseStages']) || !getBoolean($config['lua']['rateUseStages']))
	) {
	$enabled = false;

	if(file_exists($config['data_path'] . 'XML/stages.xml')) {
		$stages = new DOMDocument();
		$stages->load($config['data_path'] . 'XML/stages.xml');

		foreach($stages->getElementsByTagName('config') as $node) {
			/** @var DOMElement $node */
			if($node->getAttribute('enabled')) {
				$enabled = true;
			}
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

$stages = new MyAAC\Server\ExpStages();
$stagesArray = $stages->get();

if (empty($stagesArray)) {
	echo 'Error when loading experience stages.';
	return;
}

$twig->display('experience_stages.html.twig', ['stages' => $stagesArray]);
