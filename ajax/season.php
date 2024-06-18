<?php
header('Content-Type: text/html');
require_once(dirname(__DIR__) . '/include/config.php');
require_once(dirname(__DIR__) . '/include/autoload.php');
$seasonid = (isset($_GET['season']) && !empty($_GET['season']) && is_numeric($_GET['season'])) ? intval($_GET['season']) : null;
if(isset($_GET) && isset($_GET['do'])){
	switch ($_GET['do']) {
		case 'saveSeasonDates':
			$season = new Season($seasonid);
			$startdate = $enddate = null;
			foreach(array('startdate', 'enddate') as $datetype){
				if(isset($_GET[$datetype]) && preg_match('/(\d{4})\-(\d{2})\-(\d{2})/', $_GET[$datetype]) === 1){
					$$datetype = $_GET[$datetype];
				}
			}
			$season->updateDates($startdate, $enddate);
			break;
		
		default:
			display404();
			break;
	}
	exit();
}
display404();
?>