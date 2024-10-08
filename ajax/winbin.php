<?php
header('Content-Type: text/html');
require_once(dirname(__DIR__) . '/include/config.php');
require_once(dirname(__DIR__) . '/include/autoload.php');
if(isset($_GET) && isset($_GET['winbin'])){
	$player = new Player();
	$player->updatePlayersWinBins($_GET['winbin']);
}
?>