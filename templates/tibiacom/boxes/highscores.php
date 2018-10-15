<?php

$twig->display('highscores.html.twig', array(
	'topPlayers' => getTopPlayers(5)
));
