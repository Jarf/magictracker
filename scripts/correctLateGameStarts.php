<?php
require_once(dirname(__DIR__) . '/include/config.php');
require_once(dirname(__DIR__) . '/include/autoload.php');

$db = new db();
$db->query('SELECT game.id, game.date, TIMESTAMPDIFF(MINUTE, game.date, MIN(kills.timestamp)) AS firstKill, MAX(kills.timestamp) AS lastKill FROM game LEFT JOIN kills ON game.id = kills.gameId GROUP BY game.id, kills.gameId');
$db->execute();
$games = $db->fetchAll();
$prevgamelastkill = $prevgamestart = null;
foreach($games as $game){
	if($prevgamelastkill !== null && $game->firstKill !== null && $game->firstKill < 10){
		$newstart = null;
		if(substr($game->date, 0, 10) === substr($prevgamelastkill, 0, 10)){
			$newstart = strtotime($prevgamelastkill) + 600;
			$newstart = date('Y-m-d H:i:s', $newstart);
		}else{
			$newstart = substr($game->date, 0, 10) . ' 19:00:00';
		}
		if($newstart !== null && $newstart > $prevgamelastkill && $newstart > $prevgamestart){
			$db->query('UPDATE game SET game.date = :newstart WHERE game.id = :gameid');
			$db->bind('newstart', $newstart);
			$db->bind('gameid', $game->id);
			$db->execute();
			print 'Game ID: ' . $game->id . PHP_EOL;
			print 'Start changed from ' . $game->date . ' to ' . $newstart . PHP_EOL . PHP_EOL;
		}

	}
	$prevgamelastkill = $game->lastKill;
	$prevgamestart = $game->date;
}
?>