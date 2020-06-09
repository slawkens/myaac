<?php

return [
	'donation_type' => [
		'name' => 'Donation Type',
		'type' => 'options',
		'options' => ['points' => 'Points', 'coins' => 'Coins'],
		'default' => 'points',
		'desc' => 'What should be added to player account?',
	],
	'default_outfit_colors' => [
		'name' => 'Default Outfit Colors',
		'type' => 'text',
		'default' => '4,38,87,114',
		'desc' => "Default colors of outfits/addons in addons category<br/>
		doesn't matter for othire and other servers without addons<br/>
		you can use this outfit generator: http://sleqqus.idl.pl/tlg<br/>
		Format: head,body,legs,feet",
	],
	'section_1' => [
		'type' => 'section',
		'title' => 'Section Test',
	],
	'just_testing_boolean' => [
		'name' => 'Just Testing Boolean',
		'type' => 'boolean',
		'default' => false,
		'desc' => "Some description.",
	],
	'just_testing_number' => [
		'name' => 'Just Testing Number',
		'type' => 'number',
		'default' => 999,
		'desc' => "Some description.",
	],
];