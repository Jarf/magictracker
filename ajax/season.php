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
			exit();
			break;

		case 'createNewSeason':
			$season = new Season();
			$startdate = $enddate = $name = null;
			foreach(array('startdate', 'enddate') as $datetype){
				if(isset($_GET[$datetype]) && preg_match('/(\d{4})\-(\d{2})\-(\d{2})/', $_GET[$datetype]) === 1){
					$$datetype = $_GET[$datetype];
				}
			}
			if(isset($_GET['name']) && !empty($_GET['name'])){
				$name = $_GET['name'];
			}
			if(!empty($startdate) && !empty($enddate) && !empty($name)){
				$season->createNewSeason($startdate, $enddate, $name);
			}
			exit();
			break;
		
		default:
			display404();
			break;
	}
}
display404();
?>