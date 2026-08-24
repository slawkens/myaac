<?php
$topPlayers = Cache::remember('tibiacom_highscores_top_players', 10 * 60, function() {
	$topPlayers = getTopPlayers(5);
	foreach($topPlayers as &$player) {
		$player['outfit'] = $player['outfit_url'];
		$player['link'] = getPlayerLink($player['id'], false);
	}

	return $topPlayers;
});

$twig->display('highscores.html.twig', array(
	'topPlayers' => $topPlayers
));
