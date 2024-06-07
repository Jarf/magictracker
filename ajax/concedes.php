<?php
header('Content-Type: text/html');
require_once(dirname(__DIR__) . '/include/config.php');
require_once(dirname(__DIR__) . '/include/autoload.php');
if(isset($_GET) && isset($_GET['do'])){
	switch ($_GET['do']) {
		case 'updateConcedes':
			if(isset($_GET['game']) && is_numeric($_GET['game'])){
				$gameid = intval($_GET['game']);
				$game = new Game($gameid);
				$concedes = (isset($_GET['concede']) && !empty($_GET['concede'])) ? $_GET['concede'] : array();
				$game->updateGameConcedes($concedes);
			}
			break;
		
		default:
			display404();
			break;
	}
}else{
	display404();
}
?>