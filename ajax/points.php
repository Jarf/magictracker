<?php
header('Content-Type: text/html');
require_once(dirname(__DIR__) . '/include/config.php');
require_once(dirname(__DIR__) . '/include/autoload.php');
if(isset($_GET) && isset($_GET['do'])){
	switch ($_GET['do']) {
		case 'addPoints':
			if(isset($_GET['points']) && !empty($_GET['points']) && isset($_GET['game']) && is_numeric($_GET['game'])){
				$gameid = intval($_GET['game']);
				$game = new Game($gameid);
				$game->updateGamePoints($_GET['points']);
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