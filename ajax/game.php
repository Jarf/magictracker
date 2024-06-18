<?php
header('Content-Type: text/html');
require_once(dirname(__DIR__) . '/include/config.php');
require_once(dirname(__DIR__) . '/include/autoload.php');
if(isset($_GET) && isset($_GET['do'])){
	switch ($_GET['do']) {
		case 'startNewGame':
			$game = new Game();
			$season = new Season();
			$game->startNewGame($season->id);
			break;
		
		default:
			display404();
			break;
	}
	exit();
}
display404();
?>