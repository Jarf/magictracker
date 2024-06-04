<?php
require_once(dirname(__DIR__) . '/include/config.php');
require_once(dirname(__DIR__) . '/include/autoload.php');
if(isset($_GET) && isset($_GET['killer']) && isset($_GET['killed']) && isset($_GET['game']) && is_numeric($_GET['killer']) && is_numeric($_GET['killed']) && is_numeric($_GET['game']) && isset($_GET['do'])){
	$gameid = intval($_GET['game']);
	$killerid = intval($_GET['killer']);
	$killedid = intval($_GET['killed']);
	$game = new Game($gameid);
	switch ($_GET['do']) {
		case 'addkill':
			$game->addKill($killerid, $killedid);
			break;

		case 'removekill':
			$game->removeKill($killerid, $killedid);
			break;
		
		default:
			display404();
			break;
	}
}else{
	display404();
}
?>